<?php

use App\Actions\RecalculateCart;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VolumeRule;
use App\Models\VolumeRuleTier;

it('applies mixed SKU volume discount at tier threshold', function () {
    $category = Category::factory()->create();

    $rule = VolumeRule::factory()->create([
        'category_id' => $category->id,
        'allows_mix' => true,
        'is_active' => true,
        'basis' => 'quantity',
    ]);

    VolumeRuleTier::query()->create([
        'volume_rule_id' => $rule->id,
        'min_value' => 100,
        'discount_type' => 'percent',
        'discount_value' => 5,
        'sort_order' => 1,
    ]);

    VolumeRuleTier::query()->create([
        'volume_rule_id' => $rule->id,
        'min_value' => 300,
        'discount_type' => 'percent',
        'discount_value' => 10,
        'sort_order' => 2,
    ]);

    $productA = Product::factory()->create(['category_id' => $category->id]);
    $productB = Product::factory()->create(['category_id' => $category->id]);

    $variantA = ProductVariant::factory()->create([
        'product_id' => $productA->id,
        'price_retail' => 200,
        'min_order_qty' => 50,
        'order_multiple' => 50,
    ]);

    $variantB = ProductVariant::factory()->create([
        'product_id' => $productB->id,
        'price_retail' => 200,
        'min_order_qty' => 50,
        'order_multiple' => 50,
    ]);

    $cart = Cart::factory()->create();

    CartItem::query()->create([
        'cart_id' => $cart->id,
        'product_variant_id' => $variantA->id,
        'quantity' => 150,
    ]);

    CartItem::query()->create([
        'cart_id' => $cart->id,
        'product_variant_id' => $variantB->id,
        'quantity' => 150,
    ]);

    $pricing = app(RecalculateCart::class)->handle($cart);

    expect($pricing['subtotal'])->toBe(60000.0)
        ->and($pricing['discount'])->toBe(6000.0)
        ->and($pricing['unit_prices'][$variantA->id])->toBe(180.0)
        ->and($pricing['unit_prices'][$variantB->id])->toBe(180.0)
        ->and($pricing['tiers_applied'])->not->toBeEmpty();
});
