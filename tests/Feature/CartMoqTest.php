<?php

use App\Actions\AddCartItem;
use App\Models\Cart;
use App\Models\ProductVariant;
use Illuminate\Validation\ValidationException;

it('rejects quantity below minimum order qty', function () {
    $cart = Cart::factory()->create();

    $variant = ProductVariant::factory()->create([
        'min_order_qty' => 50,
        'order_multiple' => 50,
    ]);

    app(AddCartItem::class)->handle($cart, $variant, 25);
})->throws(ValidationException::class);

it('rejects quantity that is not a multiple of order_multiple', function () {
    $cart = Cart::factory()->create();

    $variant = ProductVariant::factory()->create([
        'min_order_qty' => 50,
        'order_multiple' => 50,
    ]);

    app(AddCartItem::class)->handle($cart, $variant, 75);
})->throws(ValidationException::class);

it('accepts valid quantity meeting moq and multiple', function () {
    $cart = Cart::factory()->create();

    $variant = ProductVariant::factory()->create([
        'min_order_qty' => 50,
        'order_multiple' => 50,
    ]);

    app(AddCartItem::class)->handle($cart, $variant, 100);

    expect($cart->fresh('items')->items)->toHaveCount(1)
        ->and($cart->items->first()->quantity)->toBe(100);
});
