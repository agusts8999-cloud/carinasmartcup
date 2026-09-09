<?php

use App\Models\InventoryLocation;
use App\Models\InventoryStock;
use App\Models\ProductVariant;
use App\Services\InventoryService;

it('reduces available quantity when stock is reserved', function () {
    $location = InventoryLocation::factory()->create(['is_default' => true]);
    $variant = ProductVariant::factory()->create();

    InventoryStock::factory()->create([
        'inventory_location_id' => $location->id,
        'product_variant_id' => $variant->id,
        'qty_on_hand' => 100,
        'qty_reserved' => 0,
    ]);

    app(InventoryService::class)->reserve($variant, 30, $location);

    $stock = InventoryStock::query()->where('product_variant_id', $variant->id)->first();

    expect($variant->fresh()->availableQty())->toBe(70)
        ->and($stock->qty_reserved)->toBe(30);
});

it('cannot oversell beyond available stock', function () {
    $location = InventoryLocation::factory()->create(['is_default' => true]);
    $variant = ProductVariant::factory()->create();

    InventoryStock::factory()->create([
        'inventory_location_id' => $location->id,
        'product_variant_id' => $variant->id,
        'qty_on_hand' => 100,
        'qty_reserved' => 0,
    ]);

    app(InventoryService::class)->reserve($variant, 80, $location);

    app(InventoryService::class)->reserve($variant, 30, $location);
})->throws(RuntimeException::class);

it('commits reserved stock and reduces on hand', function () {
    $location = InventoryLocation::factory()->create(['is_default' => true]);
    $variant = ProductVariant::factory()->create();

    InventoryStock::factory()->create([
        'inventory_location_id' => $location->id,
        'product_variant_id' => $variant->id,
        'qty_on_hand' => 100,
        'qty_reserved' => 0,
    ]);

    $service = app(InventoryService::class);
    $service->reserve($variant, 40, $location);
    $service->commit($variant, 40, $location);

    $stock = InventoryStock::query()->where('product_variant_id', $variant->id)->first();

    expect($stock->qty_on_hand)->toBe(60)
        ->and($stock->qty_reserved)->toBe(0)
        ->and($variant->fresh()->availableQty())->toBe(60);
});

it('restores available quantity when reservation is released', function () {
    $location = InventoryLocation::factory()->create(['is_default' => true]);
    $variant = ProductVariant::factory()->create();

    InventoryStock::factory()->create([
        'inventory_location_id' => $location->id,
        'product_variant_id' => $variant->id,
        'qty_on_hand' => 100,
        'qty_reserved' => 0,
    ]);

    $service = app(InventoryService::class);
    $service->reserve($variant, 25, $location);
    $service->release($variant, 25, $location);

    $stock = InventoryStock::query()->where('product_variant_id', $variant->id)->first();

    expect($stock->qty_on_hand)->toBe(100)
        ->and($stock->qty_reserved)->toBe(0)
        ->and($variant->fresh()->availableQty())->toBe(100);
});
