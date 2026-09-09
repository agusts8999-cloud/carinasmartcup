<?php

namespace App\Livewire\Account;

use App\Actions\AddCartItem;
use App\Livewire\Concerns\ResolvesCart;
use App\Models\Order;
use App\Services\CartService;
use Livewire\Component;
use Livewire\WithPagination;

class OrderHistory extends Component
{
    use ResolvesCart;
    use WithPagination;

    public function reorder(int $orderId, AddCartItem $addCartItem, CartService $cartService): void
    {
        $order = Order::query()
            ->where('user_id', auth()->id())
            ->with(['items.productVariant'])
            ->findOrFail($orderId);

        $cart = $this->resolveCart($cartService);
        $added = 0;

        foreach ($order->items as $item) {
            $variant = $item->productVariant;

            if ($variant !== null && $variant->is_active) {
                try {
                    $addCartItem->handle($cart, $variant, $item->quantity);
                    $added++;
                } catch (\Illuminate\Validation\ValidationException) {
                    // Skip unavailable variants
                }
            }
        }

        $this->dispatchCartUpdated($cart->fresh());

        if ($added > 0) {
            session()->flash('success', "{$added} item ditambahkan ke keranjang.");
            $this->redirect(route('cart'), navigate: true);
        } else {
            session()->flash('error', 'Tidak ada item yang dapat ditambahkan.');
        }
    }

    public function render()
    {
        $orders = Order::query()
            ->where('user_id', auth()->id())
            ->withCount('items')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.account.order-history', [
            'orders' => $orders,
        ])->layout('layouts.shop', ['title' => 'Riwayat Pesanan']);
    }
}
