<?php

namespace App\Livewire\Shop;

use App\Actions\AddCartItem;
use App\Livewire\Concerns\ResolvesCart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Livewire\Component;

class ProductDetail extends Component
{
    use ResolvesCart;

    public Product $product;

    public ?int $selectedVariantId = null;

    public int $quantity = 1;

    public function mount(string $slug): void
    {
        $product = Product::query()
            ->published()
            ->where('slug', $slug)
            ->with(['category', 'media', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->firstOrFail();

        $this->product = $product;
        $this->selectedVariantId = $product->variants->first()?->id;
        $this->quantity = max(1, (int) ($product->variants->first()?->min_order_qty ?? 1));

        \App\Support\Analytics::track('view_product', [
            'product_id' => $product->id,
            'slug' => $product->slug,
        ]);
    }

    public function updatedSelectedVariantId(): void
    {
        $variant = $this->selectedVariant();

        if ($variant !== null) {
            $this->quantity = max((int) $variant->min_order_qty, $this->quantity);
        }
    }

    public function addToCart(AddCartItem $addCartItem, CartService $cartService): void
    {
        $variant = $this->selectedVariant();

        if ($variant === null) {
            $this->addError('quantity', 'Pilih varian produk terlebih dahulu.');

            return;
        }

        try {
            $cart = $this->resolveCart($cartService);
            $addCartItem->handle($cart, $variant, $this->quantity);
            \App\Support\Analytics::track('add_to_cart', [
                'variant_id' => $variant->id,
                'sku' => $variant->sku,
                'quantity' => $this->quantity,
            ], $cart->token);
            $this->dispatchCartUpdated($cart->fresh());
            session()->flash('success', 'Produk ditambahkan ke keranjang.');
            $this->redirect(route('cart'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
        }
    }

    public function selectedVariant(): ?ProductVariant
    {
        if ($this->selectedVariantId === null) {
            return null;
        }

        return $this->product->variants->firstWhere('id', $this->selectedVariantId);
    }

    public function render()
    {
        $variant = $this->selectedVariant();
        $available = $variant?->availableQty() ?? 0;

        return view('livewire.shop.product-detail', [
            'variant' => $variant,
            'available' => $available,
        ])->layout('layouts.shop', ['title' => $this->product->name]);
    }
}
