<?php

use App\Actions\ConfirmManualPayment;
use App\Actions\PlaceOrder;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\InventoryLocation;
use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingRate;
use App\Models\ShippingZone;
use App\Models\User;

function createCheckoutFixtures(): array
{
    $location = InventoryLocation::factory()->create([
        'is_default' => true,
        'pickup_available' => true,
        'is_active' => true,
    ]);

    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'price_retail' => 200,
        'weight_gram' => 10,
        'min_order_qty' => 50,
        'order_multiple' => 50,
    ]);

    InventoryStock::factory()->create([
        'inventory_location_id' => $location->id,
        'product_variant_id' => $variant->id,
        'qty_on_hand' => 1000,
        'qty_reserved' => 0,
    ]);

    $zone = ShippingZone::query()->create([
        'name' => 'Test Zone',
        'provinces' => ['DKI Jakarta'],
        'cities' => ['Jakarta Pusat'],
        'is_active' => true,
    ]);

    $rate = ShippingRate::query()->create([
        'shipping_zone_id' => $zone->id,
        'name' => 'Reguler Test',
        'code' => 'TEST-REG',
        'base_price' => 10000,
        'price_per_kg' => 0,
        'price_per_koli' => 0,
        'eta_days_min' => 1,
        'eta_days_max' => 2,
        'is_pickup' => false,
        'is_active' => true,
    ]);

    $cart = Cart::factory()->create();

    CartItem::query()->create([
        'cart_id' => $cart->id,
        'product_variant_id' => $variant->id,
        'quantity' => 100,
    ]);

    return compact('location', 'variant', 'rate', 'cart');
}

it('places order as pending payment with reserved stock and payment record', function () {
    ['variant' => $variant, 'rate' => $rate, 'cart' => $cart] = createCheckoutFixtures();

    $order = app(PlaceOrder::class)->handle([
        'cart' => $cart->fresh(['items.productVariant.product']),
        'customer_name' => 'Test Customer',
        'customer_email' => 'customer@test.com',
        'customer_whatsapp' => '6281234567890',
        'address' => [
            'recipient_name' => 'Test Customer',
            'phone' => '6281234567890',
            'address_line' => 'Jl. Test No. 1',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'postal_code' => '10110',
        ],
        'shipping_rate_id' => $rate->id,
        'shipping_accepted_estimate' => true,
    ]);

    $stock = InventoryStock::query()->where('product_variant_id', $variant->id)->first();

    expect($order->status)->toBe(OrderStatus::PendingPayment)
        ->and($order->payments)->toHaveCount(1)
        ->and($order->payments->first()->status)->toBe(PaymentStatus::Pending)
        ->and($stock->qty_reserved)->toBe(100)
        ->and($variant->fresh()->availableQty())->toBe(900);
});

it('confirms payment and commits reserved stock', function () {
    ['variant' => $variant, 'rate' => $rate, 'cart' => $cart] = createCheckoutFixtures();

    $order = app(PlaceOrder::class)->handle([
        'cart' => $cart->fresh(['items.productVariant.product']),
        'customer_name' => 'Test Customer',
        'customer_whatsapp' => '6281234567890',
        'address' => [
            'recipient_name' => 'Test Customer',
            'phone' => '6281234567890',
            'address_line' => 'Jl. Test No. 1',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'postal_code' => '10110',
        ],
        'shipping_rate_id' => $rate->id,
    ]);

    $admin = User::factory()->create();
    $payment = $order->payments->first();

    app(ConfirmManualPayment::class)->handle($payment, $admin);

    $order->refresh();
    $payment->refresh();
    $stock = InventoryStock::query()->where('product_variant_id', $variant->id)->first();

    expect($order->status)->toBe(OrderStatus::Paid)
        ->and($payment->status)->toBe(PaymentStatus::Paid)
        ->and($stock->qty_on_hand)->toBe(900)
        ->and($stock->qty_reserved)->toBe(0);
});
