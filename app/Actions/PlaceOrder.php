<?php

namespace App\Actions;

use App\Models\AnalyticsEvent;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingRate;
use App\Models\User;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Address;
use App\Models\Cart;
use App\Mail\OrderCreatedMail;
use App\Services\InventoryService;
use App\Services\NotificationService;
use App\Services\OrderNumberGenerator;
use App\Services\Payment\ManualTransferGateway;
use App\Services\ShippingQuoteService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class PlaceOrder
{
    public function __construct(
        private readonly RecalculateCart $recalculateCart,
        private readonly InventoryService $inventoryService,
        private readonly OrderNumberGenerator $orderNumberGenerator,
        private readonly ManualTransferGateway $paymentGateway,
        private readonly ShippingQuoteService $shippingQuoteService,
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): Order
    {
        return DB::transaction(function () use ($data): Order {
            /** @var Cart $cart */
            $cart = $data['cart'];
            $cart->loadMissing(['items.productVariant.product', 'coupon']);

            if ($cart->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Keranjang kosong.',
                ]);
            }

            $pricing = $this->recalculateCart->handle($cart);

            foreach ($cart->items as $item) {
                $variant = $item->productVariant;
                $available = $variant->availableQty();

                if ($available < $item->quantity) {
                    throw ValidationException::withMessages([
                        'stock' => "Stok {$variant->sku} tidak mencukupi. Tersedia: {$available}.",
                    ]);
                }
            }

            $addressSnapshot = $this->resolveAddressSnapshot($data['address']);
            $province = (string) ($addressSnapshot['province'] ?? '');
            $city = $addressSnapshot['city'] ?? null;

            $subtotalAfterVolume = $pricing['subtotal'] - $pricing['discount'];
            $couponDiscount = $this->calculateCouponDiscount($cart, $subtotalAfterVolume);
            $netSubtotal = $subtotalAfterVolume - $couponDiscount;

            $shippingRate = ShippingRate::query()
                ->with('shippingZone')
                ->findOrFail($data['shipping_rate_id']);

            $quotes = $this->shippingQuoteService->quote(
                $province,
                $city,
                (float) $pricing['weight_kg'],
                $netSubtotal,
                (int) $pricing['koli_count'],
            );

            $selectedQuote = $quotes->firstWhere('id', $shippingRate->id);

            if ($selectedQuote === null) {
                throw ValidationException::withMessages([
                    'shipping_rate_id' => 'Tarif pengiriman tidak tersedia untuk alamat atau pesanan ini.',
                ]);
            }

            $shippingAmount = (float) $selectedQuote['price'];
            $total = round($netSubtotal + $shippingAmount, 2);
            $reservationHours = (int) config('carina.payment_reservation_hours', 24);
            $dueAt = now()->addHours($reservationHours);

            $orderNumber = $this->orderNumberGenerator->generate();

            $order = Order::query()->create([
                'number' => $orderNumber,
                'user_id' => $data['user_id'] ?? null,
                'status' => OrderStatus::PendingPayment,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_whatsapp' => $data['customer_whatsapp'],
                'shipping_address_snapshot' => $addressSnapshot,
                'subtotal' => $pricing['subtotal'],
                'discount_amount' => $pricing['discount'],
                'coupon_code' => $cart->coupon?->code,
                'coupon_discount' => $couponDiscount,
                'shipping_amount' => $shippingAmount,
                'shipping_rate_name' => $shippingRate->name,
                'shipping_estimate_note' => $this->shippingEstimateNote($shippingRate),
                'total' => $total,
                'weight_gram' => $pricing['weight_gram'],
                'volume_cm3' => $pricing['volume_cm3'],
                'koli_count' => $pricing['koli_count'],
                'notes' => $data['notes'] ?? null,
                'payment_method' => PaymentMethod::BankTransfer,
                'payment_due_at' => $dueAt,
                'reserved_until' => $dueAt,
                'shipping_accepted_estimate' => (bool) ($data['shipping_accepted_estimate'] ?? false),
            ]);

            foreach ($cart->items as $item) {
                $variant = $item->productVariant;
                $product = $variant->product;
                $unitPrice = (float) ($pricing['unit_prices'][$variant->id] ?? $variant->price_retail);
                $retail = (float) $variant->price_retail;
                $lineDiscount = ($retail - $unitPrice) * $item->quantity;

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $product->name,
                    'sku' => $variant->sku,
                    'variant_name' => $variant->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice,
                    'discount_amount' => round($lineDiscount, 2),
                    'line_total' => round($unitPrice * $item->quantity, 2),
                    'weight_gram' => $variant->weight_gram,
                    'pack_size' => $variant->pack_size,
                    'snapshot' => [
                        'product' => $product->only(['id', 'name', 'slug']),
                        'variant' => $variant->only([
                            'id', 'sku', 'name', 'color', 'lid_type', 'unit_type', 'pack_size', 'price_retail',
                        ]),
                    ],
                ]);

                $this->inventoryService->reserve($variant, $item->quantity);
            }

            $payment = $this->paymentGateway->createPayment($order);

            Invoice::query()->create([
                'order_id' => $order->id,
                'number' => 'INV-'.$orderNumber,
                'issued_at' => now(),
            ]);

            AnalyticsEvent::query()->create([
                'event' => 'purchase',
                'user_id' => $data['user_id'] ?? null,
                'session_id' => $cart->token,
                'payload' => [
                    'order_id' => $order->id,
                    'order_number' => $order->number,
                    'total' => $order->total,
                    'payment_id' => $payment->id,
                    'item_count' => $cart->items->count(),
                ],
            ]);

            $this->notificationService->send($order, 'email', 'order_created', [
                'order_number' => $order->number,
                'total' => $order->total,
            ]);

            if (! empty($order->customer_email)) {
                Mail::to($order->customer_email)->send(new OrderCreatedMail($order));
            }

            $cart->items()->delete();
            $cart->update(['coupon_id' => null]);

            return $order->fresh(['items', 'payments', 'invoices']);
        });
    }

    /**
     * @param  array<string, mixed>|Address  $address
     * @return array<string, mixed>
     */
    private function resolveAddressSnapshot(array|Address $address): array
    {
        if ($address instanceof Address) {
            return $address->only([
                'recipient_name',
                'phone',
                'address_line',
                'village',
                'district',
                'city',
                'province',
                'postal_code',
                'latitude',
                'longitude',
            ]);
        }

        return $address;
    }

    private function calculateCouponDiscount(Cart $cart, float $subtotalAfterVolume): float
    {
        $coupon = $cart->coupon;

        if ($coupon === null) {
            return 0.0;
        }

        $discount = match ($coupon->type) {
            'percent' => $subtotalAfterVolume * ((float) $coupon->value / 100),
            'fixed' => (float) $coupon->value,
            default => 0.0,
        };

        return round(min($subtotalAfterVolume, max(0, $discount)), 2);
    }

    private function shippingEstimateNote(ShippingRate $rate): string
    {
        return sprintf(
            'Estimasi %d–%d hari kerja',
            $rate->eta_days_min,
            $rate->eta_days_max
        );
    }
}
