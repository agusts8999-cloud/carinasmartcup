<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingRate extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'shipping_zone_id',
        'name',
        'code',
        'price_per_kg',
        'price_per_koli',
        'base_price',
        'min_order_amount',
        'subsidy_amount',
        'eta_days_min',
        'eta_days_max',
        'cutoff_time',
        'requires_manual_verify',
        'is_pickup',
        'is_active',
        'min_weight_kg',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_per_kg' => 'decimal:2',
            'price_per_koli' => 'decimal:2',
            'base_price' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'subsidy_amount' => 'decimal:2',
            'eta_days_min' => 'integer',
            'eta_days_max' => 'integer',
            'requires_manual_verify' => 'boolean',
            'is_pickup' => 'boolean',
            'is_active' => 'boolean',
            'min_weight_kg' => 'decimal:2',
        ];
    }

    public function shippingZone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class);
    }
}
