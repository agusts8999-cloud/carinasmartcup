<?php

namespace App\Livewire\Cart;

use App\Actions\ApplyCoupon;
use App\Actions\RecalculateCart;
use App\Livewire\Concerns\ResolvesCart;
use App\Models\ProductVariant;
use App\Services\CartService;
use Livewire\Component;

class CartPage extends Component
{
    use ResolvesCart;

    public string $couponCode = '';

    public ?string $shareLink = null;

    public function mount(CartService $cartService, RecalculateCart $recalculateCart): void
    {
        $cart = $this->resolveCart($cartService);
        $this->couponCode = $cart->coupon?->code ?? '';
    }

    public function updateQuantity(int $variantId, int $quantity, CartService $cartService): void
    {
        $cart = $this->resolveCart($cartService);
        $variant = ProductVariant::query()->findOrFail($variantId);

        try {
            if ($quantity <= 0) {
                $cartService->removeItem($cart, $variant);
            } else {
                $cartService->updateItem($cart, $variant, $quantity);
            }

            $this->dispatchCartUpdated($cart->fresh());
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
        }
    }

    public function removeItem(int $variantId, CartService $cartService): void
    {
        $cart = $this->resolveCart($cartService);
        $variant = ProductVariant::query()->findOrFail($variantId);
        $cartService->removeItem($cart, $variant);
        $this->dispatchCartUpdated($cart->fresh());
    }

    public function applyCoupon(ApplyCoupon $applyCoupon, CartService $cartService): void
    {
        $cart = $this->resolveCart($cartService);

        try {
            $applyCoupon->handle($cart, trim($this->couponCode));
            session()->flash('success', 'Kupon berhasil diterapkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
        }
    }

    public function removeCoupon(CartService $cartService): void
    {
        $cart = $this->resolveCart($cartService);
        $cart->update(['coupon_id' => null]);
        $this->couponCode = '';
    }

    public function shareCart(CartService $cartService): void
    {
        $cart = $this->resolveCart($cartService);
        $this->shareLink = url($cartService->createShareLink($cart));
    }

    public function render(CartService $cartService, RecalculateCart $recalculateCart)
    {
        $cart = $this->resolveCart($cartService);
        $cart->load(['items.productVariant.product', 'coupon']);
        $pricing = $recalculateCart->handle($cart);

        return view('livewire.cart.cart-page', [
            'cart' => $cart,
            'pricing' => $pricing,
        ])->layout('layouts.shop', ['title' => 'Keranjang']);
    }
}
