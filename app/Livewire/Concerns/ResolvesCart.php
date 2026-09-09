<?php

namespace App\Livewire\Concerns;

use App\Models\Cart;
use App\Services\CartService;

trait ResolvesCart
{
    protected function cartToken(): ?string
    {
        return request()->cookie('cart_token');
    }

    protected function resolveCart(CartService $cartService): Cart
    {
        return $cartService->resolveCart(auth()->user(), $this->cartToken());
    }

    protected function dispatchCartUpdated(Cart $cart): void
    {
        $count = (int) $cart->items()->sum('quantity');

        $this->dispatch('cart-updated', count: $count);
    }
}
