<?php

namespace App\Actions;

use App\Models\Cart;
use App\Models\ProductVariant;
use App\Services\CartService;

class AddCartItem
{
    public function __construct(
        private readonly CartService $cartService,
    ) {}

    public function handle(Cart $cart, ProductVariant $variant, int $qty): void
    {
        $this->cartService->addItem($cart, $variant, $qty);
    }
}
