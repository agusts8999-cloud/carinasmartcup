<?php

namespace App\Actions;

use App\Models\Cart;
use App\Models\Coupon;
use Illuminate\Validation\ValidationException;

class ApplyCoupon
{
    public function __construct(
        private readonly RecalculateCart $recalculateCart,
    ) {}

    public function handle(Cart $cart, string $code): void
    {
        $coupon = Coupon::query()
            ->where('code', $code)
            ->first();

        if ($coupon === null) {
            throw ValidationException::withMessages([
                'coupon' => 'Kode kupon tidak ditemukan.',
            ]);
        }

        if (! $coupon->is_active) {
            throw ValidationException::withMessages([
                'coupon' => 'Kupon tidak aktif.',
            ]);
        }

        $now = now();

        if ($coupon->starts_at !== null && $coupon->starts_at->isFuture()) {
            throw ValidationException::withMessages([
                'coupon' => 'Kupon belum berlaku.',
            ]);
        }

        if ($coupon->ends_at !== null && $coupon->ends_at->isPast()) {
            throw ValidationException::withMessages([
                'coupon' => 'Kupon sudah kedaluwarsa.',
            ]);
        }

        if ($coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses) {
            throw ValidationException::withMessages([
                'coupon' => 'Kuota penggunaan kupon sudah habis.',
            ]);
        }

        $pricing = $this->recalculateCart->handle($cart);
        $subtotalAfterVolume = $pricing['subtotal'] - $pricing['discount'];

        if ($coupon->min_subtotal !== null && $subtotalAfterVolume < (float) $coupon->min_subtotal) {
            throw ValidationException::withMessages([
                'coupon' => 'Subtotal minimum untuk kupon ini adalah Rp '.number_format((float) $coupon->min_subtotal, 0, ',', '.').'.',
            ]);
        }

        $cart->coupon_id = $coupon->id;
        $cart->save();
    }
}
