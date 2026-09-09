<?php

namespace App\Services;

use App\Models\InventoryLocation;
use App\Models\ShippingRate;
use App\Models\ShippingZone;
use Illuminate\Support\Collection;

class ShippingQuoteService
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function quote(
        string $province,
        ?string $city,
        float $weightKg,
        float $subtotal,
        int $koliCount = 1,
    ): Collection {
        $koliCount = max(1, $koliCount);
        $defaultLocation = $this->defaultPickupLocation();

        $zones = ShippingZone::query()
            ->where('is_active', true)
            ->with(['rates' => fn ($query) => $query->where('is_active', true)])
            ->get()
            ->filter(fn (ShippingZone $zone): bool => $this->matchesZone($zone, $province, $city));

        $quotes = collect();

        foreach ($zones as $zone) {
            foreach ($zone->rates as $rate) {
                if (! $this->rateEligible($rate, $weightKg, $subtotal, $defaultLocation)) {
                    continue;
                }

                $price = $this->computePrice($rate, $weightKg, $koliCount);

                $quotes->push([
                    'id' => $rate->id,
                    'shipping_zone_id' => $zone->id,
                    'zone_name' => $zone->name,
                    'name' => $rate->name,
                    'code' => $rate->code,
                    'price' => $price,
                    'eta_days_min' => $rate->eta_days_min,
                    'eta_days_max' => $rate->eta_days_max,
                    'is_pickup' => $rate->is_pickup,
                    'requires_manual_verify' => $rate->requires_manual_verify,
                    'base_price' => (float) $rate->base_price,
                    'price_per_kg' => (float) $rate->price_per_kg,
                    'price_per_koli' => (float) $rate->price_per_koli,
                    'subsidy_amount' => (float) $rate->subsidy_amount,
                ]);
            }
        }

        return $quotes->sortBy('price')->values();
    }

    private function matchesZone(ShippingZone $zone, string $province, ?string $city): bool
    {
        $provinces = collect($zone->provinces ?? []);

        if (! $provinces->contains(fn ($value): bool => strcasecmp((string) $value, $province) === 0)) {
            return false;
        }

        $cities = collect($zone->cities ?? []);

        if ($cities->isEmpty() || $city === null || $city === '') {
            return true;
        }

        return $cities->contains(fn ($value): bool => strcasecmp((string) $value, $city) === 0);
    }

    private function rateEligible(
        ShippingRate $rate,
        float $weightKg,
        float $subtotal,
        ?InventoryLocation $defaultLocation,
    ): bool {
        if ($rate->min_weight_kg !== null && $weightKg < (float) $rate->min_weight_kg) {
            return false;
        }

        if ($rate->min_order_amount !== null && $subtotal < (float) $rate->min_order_amount) {
            return false;
        }

        if ($rate->is_pickup) {
            return $defaultLocation !== null
                && $defaultLocation->pickup_available
                && $defaultLocation->is_active;
        }

        return true;
    }

    private function computePrice(ShippingRate $rate, float $weightKg, int $koliCount): float
    {
        $price = (float) $rate->base_price
            + ((float) $rate->price_per_kg * $weightKg)
            + ((float) $rate->price_per_koli * $koliCount)
            - (float) $rate->subsidy_amount;

        return round(max(0, $price), 2);
    }

    private function defaultPickupLocation(): ?InventoryLocation
    {
        return InventoryLocation::query()
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }
}
