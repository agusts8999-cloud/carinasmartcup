<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\VolumeRule;
use App\Models\VolumeRuleTier;

class VolumePricingService
{
    /**
     * @return array{
     *     subtotal: float,
     *     discount: float,
     *     tiers_applied: list<array<string, mixed>>,
     *     next_tier_remaining: float|null,
     *     unit_prices: array<int, float>
     * }
     */
    public function calculateForCart(Cart $cart): array
    {
        $cart->loadMissing('items.productVariant.product');

        $unitPrices = [];
        $lineTotals = [];

        foreach ($cart->items as $item) {
            $variant = $item->productVariant;
            $retail = (float) $variant->price_retail;
            $unitPrices[$variant->id] = $retail;
            $lineTotals[$variant->id] = [
                'quantity' => $item->quantity,
                'retail' => $retail,
                'category_id' => $variant->product?->category_id,
            ];
        }

        $rules = VolumeRule::query()
            ->where('is_active', true)
            ->with(['tiers' => fn ($query) => $query->orderByDesc('min_value')])
            ->get();

        $tiersApplied = [];
        $bestDiscountPerVariant = array_fill_keys(array_keys($unitPrices), 0.0);
        $nextTierRemaining = null;

        foreach ($rules as $rule) {
            $eligibleVariantIds = $this->eligibleVariantIds($rule, $lineTotals);

            if ($eligibleVariantIds === []) {
                continue;
            }

            if ($rule->allows_mix) {
                $basisValue = $this->aggregateBasis($rule, $eligibleVariantIds, $lineTotals);
                $tier = $this->matchingTier($rule, $basisValue);

                if ($tier !== null) {
                    foreach ($eligibleVariantIds as $variantId) {
                        $retail = $lineTotals[$variantId]['retail'];
                        $discountPerUnit = $this->discountPerUnit($retail, $tier);
                        $bestDiscountPerVariant[$variantId] = max(
                            $bestDiscountPerVariant[$variantId],
                            $discountPerUnit
                        );
                    }

                    $tiersApplied[] = $this->tierPayload($rule, $tier, $basisValue);
                }

                $next = $this->nextTierRemaining($rule, $basisValue);

                if ($next !== null && ($nextTierRemaining === null || $next < $nextTierRemaining)) {
                    $nextTierRemaining = $next;
                }
            } else {
                foreach ($eligibleVariantIds as $variantId) {
                    $basisValue = $this->itemBasis($rule, $variantId, $lineTotals);
                    $tier = $this->matchingTier($rule, $basisValue);

                    if ($tier === null) {
                        continue;
                    }

                    $retail = $lineTotals[$variantId]['retail'];
                    $discountPerUnit = $this->discountPerUnit($retail, $tier);
                    $bestDiscountPerVariant[$variantId] = max(
                        $bestDiscountPerVariant[$variantId],
                        $discountPerUnit
                    );

                    $tiersApplied[] = $this->tierPayload($rule, $tier, $basisValue, $variantId);

                    $next = $this->nextTierRemaining($rule, $basisValue);

                    if ($next !== null && ($nextTierRemaining === null || $next < $nextTierRemaining)) {
                        $nextTierRemaining = $next;
                    }
                }
            }
        }

        $subtotal = 0.0;
        $discount = 0.0;

        foreach ($lineTotals as $variantId => $line) {
            $discountedUnit = max(0, $line['retail'] - $bestDiscountPerVariant[$variantId]);
            $unitPrices[$variantId] = round($discountedUnit, 2);
            $subtotal += $line['retail'] * $line['quantity'];
            $discount += $bestDiscountPerVariant[$variantId] * $line['quantity'];
        }

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'tiers_applied' => $tiersApplied,
            'next_tier_remaining' => $nextTierRemaining !== null ? round($nextTierRemaining, 2) : null,
            'unit_prices' => $unitPrices,
        ];
    }

    /**
     * @param  array<int, array{quantity: int, retail: float, category_id: int|null}>  $lineTotals
     * @return list<int>
     */
    private function eligibleVariantIds(VolumeRule $rule, array $lineTotals): array
    {
        $ids = [];

        foreach ($lineTotals as $variantId => $line) {
            if ($rule->applies_to_all) {
                $ids[] = $variantId;

                continue;
            }

            if ($rule->category_id !== null && $line['category_id'] === $rule->category_id) {
                $ids[] = $variantId;
            }
        }

        return $ids;
    }

    /**
     * @param  list<int>  $variantIds
     * @param  array<int, array{quantity: int, retail: float, category_id: int|null}>  $lineTotals
     */
    private function aggregateBasis(VolumeRule $rule, array $variantIds, array $lineTotals): float
    {
        $total = 0.0;

        foreach ($variantIds as $variantId) {
            $total += $this->itemBasis($rule, $variantId, $lineTotals);
        }

        return $total;
    }

    /**
     * @param  array<int, array{quantity: int, retail: float, category_id: int|null}>  $lineTotals
     */
    private function itemBasis(VolumeRule $rule, int $variantId, array $lineTotals): float
    {
        $line = $lineTotals[$variantId];

        return match ($rule->basis) {
            'amount' => $line['quantity'] * $line['retail'],
            default => (float) $line['quantity'],
        };
    }

    private function matchingTier(VolumeRule $rule, float $basisValue): ?VolumeRuleTier
    {
        return $rule->tiers
            ->first(fn (VolumeRuleTier $tier): bool => (float) $tier->min_value <= $basisValue);
    }

    private function nextTierRemaining(VolumeRule $rule, float $basisValue): ?float
    {
        $nextTier = $rule->tiers
            ->sortBy('min_value')
            ->first(fn (VolumeRuleTier $tier): bool => (float) $tier->min_value > $basisValue);

        if ($nextTier === null) {
            return null;
        }

        return (float) $nextTier->min_value - $basisValue;
    }

    private function discountPerUnit(float $retail, VolumeRuleTier $tier): float
    {
        return match ($tier->discount_type) {
            'percent' => $retail * ((float) $tier->discount_value / 100),
            'fixed_per_unit' => min($retail, (float) $tier->discount_value),
            default => 0.0,
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function tierPayload(
        VolumeRule $rule,
        VolumeRuleTier $tier,
        float $basisValue,
        ?int $variantId = null,
    ): array {
        return [
            'rule_id' => $rule->id,
            'rule_name' => $rule->name,
            'tier_id' => $tier->id,
            'min_value' => (float) $tier->min_value,
            'discount_type' => $tier->discount_type,
            'discount_value' => (float) $tier->discount_value,
            'basis_value' => $basisValue,
            'variant_id' => $variantId,
        ];
    }
}
