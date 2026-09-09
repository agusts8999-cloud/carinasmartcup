<?php

use App\Actions\ReleaseExpiredReservations;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\InventoryLocation;
use App\Models\InventoryStock;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;

it('cancels expired pending orders and releases stock', function () {
    $location = InventoryLocation::factory()->create(['is_default' => true]);
    $variant = ProductVariant::factory()->create();

    InventoryStock::factory()->create([
        'inventory_location_id' => $location->id,
        'product_variant_id' => $variant->id,
        'qty_on_hand' => 500,
        'qty_reserved' => 50,
    ]);

    $order = Order::query()->create([
        'number' => 'CSC-TEST-0001',
        'status' => OrderStatus::PendingPayment,
        'customer_name' => 'Expired Customer',
        'customer_whatsapp' => '6281234567890',
        'shipping_address_snapshot' => [
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
        ],
        'subtotal' => 10000,
        'discount_amount' => 0,
        'shipping_amount' => 0,
        'total' => 10000,
        'payment_method' => PaymentMethod::BankTransfer,
        'payment_due_at' => now()->subHour(),
        'reserved_until' => now()->subHour(),
    ]);

    OrderItem::query()->create([
        'order_id' => $order->id,
        'product_variant_id' => $variant->id,
        'product_name' => 'Test Product',
        'sku' => $variant->sku,
        'quantity' => 50,
        'unit_price' => 200,
        'line_total' => 10000,
    ]);

    Payment::query()->create([
        'order_id' => $order->id,
        'method' => PaymentMethod::BankTransfer,
        'status' => PaymentStatus::Pending,
        'amount' => 10000,
    ]);

    $released = app(ReleaseExpiredReservations::class)->handle();

    $order->refresh();
    $stock = InventoryStock::query()->where('product_variant_id', $variant->id)->first();

    expect($released)->toBe(1)
        ->and($order->status)->toBe(OrderStatus::Cancelled)
        ->and($order->cancelled_at)->not->toBeNull()
        ->and($stock->qty_reserved)->toBe(0)
        ->and($variant->fresh()->availableQty())->toBe(500)
        ->and($order->payments->first()->status)->toBe(PaymentStatus::Expired);
});
