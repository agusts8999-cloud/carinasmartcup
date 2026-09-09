<?php

namespace App\Actions;

use App\Models\Cart;
use App\Services\VolumePricingService;

class RecalculateCart
{
    public function __construct(
        private readonly VolumePricingService $volumePricingService,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(Cart $cart): array
    {
        $cart->loadMissing('items.productVariant.product');

        $pricing = $this->volumePricingService->calculateForCart($cart);

        $weightGram = 0;
        $volumeCm3 = 0;

        foreach ($cart->items as $item) {
            $variant = $item->productVariant;
            $weightGram += $variant->weight_gram * $item->quantity;

            if ($variant->length_cm && $variant->width_cm && $variant->height_cm) {
                $volumeCm3 += (int) round(
                    (float) $variant->length_cm
                    * (float) $variant->width_cm
                    * (float) $variant->height_cm
                    * $item->quantity
                );
            }
        }

        $weightKg = round($weightGram / 1000, 3);
        $koliCount = max(1, (int) ceil($weightGram / 20000));

        return array_merge($pricing, [
            'weight_gram' => $weightGram,
            'weight_kg' => $weightKg,
            'volume_cm3' => $volumeCm3,
            'koli_count' => $koliCount,
        ]);
    }
}
