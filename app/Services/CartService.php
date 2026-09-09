<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function resolveCart(?User $user, ?string $token): Cart
    {
        if ($user !== null) {
            $cart = Cart::query()->firstOrCreate(
                ['user_id' => $user->id],
                ['token' => (string) Str::uuid()]
            );

            if ($token !== null && $token !== '' && $cart->token !== $token) {
                $guestCart = Cart::query()->where('token', $token)->whereNull('user_id')->first();

                if ($guestCart !== null) {
                    $this->mergeCarts($guestCart, $cart);
                }
            }

            return $cart->fresh(['items.productVariant.product']);
        }

        if ($token === null || $token === '') {
            return Cart::query()->create([
                'token' => (string) Str::uuid(),
            ]);
        }

        return Cart::query()->firstOrCreate(
            ['token' => $token],
            ['token' => $token]
        )->fresh(['items.productVariant.product']);
    }

    public function addItem(Cart $cart, ProductVariant $variant, int $qty): void
    {
        $this->validateQuantity($variant, $qty);

        $item = CartItem::query()->firstOrNew([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
        ]);

        $newQty = ($item->exists ? $item->quantity : 0) + $qty;
        $this->validateQuantity($variant, $newQty);

        $item->quantity = $newQty;
        $item->save();
    }

    public function updateItem(Cart $cart, ProductVariant $variant, int $qty): void
    {
        $this->validateQuantity($variant, $qty);

        CartItem::query()->updateOrCreate(
            [
                'cart_id' => $cart->id,
                'product_variant_id' => $variant->id,
            ],
            ['quantity' => $qty]
        );
    }

    public function removeItem(Cart $cart, ProductVariant $variant): void
    {
        CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('product_variant_id', $variant->id)
            ->delete();
    }

    public function createShareLink(Cart $cart): string
    {
        if ($cart->share_token === null || $cart->share_token === '') {
            $cart->share_token = Str::random(32);
            $cart->save();
        }

        return '/keranjang/bagikan/'.$cart->share_token;
    }

    public function loadSharedCart(string $token): Cart
    {
        $cart = Cart::query()
            ->where('share_token', $token)
            ->with(['items.productVariant.product'])
            ->first();

        if ($cart === null) {
            throw ValidationException::withMessages([
                'share_token' => 'Keranjang bagikan tidak ditemukan atau sudah tidak valid.',
            ]);
        }

        return $cart;
    }

    public function validateQuantity(ProductVariant $variant, int $qty): void
    {
        $minQty = max(1, (int) $variant->min_order_qty);
        $multiple = max(1, (int) $variant->order_multiple);

        if ($qty < $minQty) {
            throw ValidationException::withMessages([
                'quantity' => "Jumlah minimum pesanan untuk {$variant->sku} adalah {$minQty}.",
            ]);
        }

        if ($qty % $multiple !== 0) {
            throw ValidationException::withMessages([
                'quantity' => "Jumlah pesanan untuk {$variant->sku} harus kelipatan {$multiple}.",
            ]);
        }

        if (! $variant->is_active) {
            throw ValidationException::withMessages([
                'quantity' => "Varian {$variant->sku} tidak tersedia.",
            ]);
        }
    }

    private function mergeCarts(Cart $from, Cart $to): void
    {
        foreach ($from->items as $item) {
            $existing = CartItem::query()
                ->where('cart_id', $to->id)
                ->where('product_variant_id', $item->product_variant_id)
                ->first();

            if ($existing !== null) {
                $this->updateItem($to, $item->productVariant, $existing->quantity + $item->quantity);
            } else {
                CartItem::query()->create([
                    'cart_id' => $to->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                ]);
            }
        }

        if ($to->coupon_id === null && $from->coupon_id !== null) {
            $to->coupon_id = $from->coupon_id;
            $to->save();
        }

        $from->items()->delete();
        $from->delete();
    }
}
