<?php

namespace App\Livewire\Cart;

use App\Actions\AddCartItem;
use App\Livewire\Concerns\ResolvesCart;
use App\Services\CartService;
use Livewire\Component;

class ShareCart extends Component
{
    use ResolvesCart;

    public function mount(string $token, CartService $cartService, AddCartItem $addCartItem): void
    {
        try {
            $sharedCart = $cartService->loadSharedCart($token);
            $cart = $this->resolveCart($cartService);

            foreach ($sharedCart->items as $item) {
                if ($item->productVariant !== null && $item->productVariant->is_active) {
                    $addCartItem->handle($cart, $item->productVariant, $item->quantity);
                }
            }

            $this->dispatchCartUpdated($cart->fresh());
            session()->flash('success', 'Item dari keranjang bagikan telah ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', $e->validator->errors()->first());
        }

        $this->redirect(route('cart'), navigate: true);
    }

    public function render()
    {
        return view('livewire.cart.share-cart')
            ->layout('layouts.shop', ['title' => 'Memuat Keranjang']);
    }
}
