<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryLocation extends Model
{
    /** @use HasFactory<\Database\Factories\InventoryLocationFactory> */
    use HasFactory;
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
        'address',
        'is_default',
        'is_active',
        'pickup_available',
        'pickup_hours',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'pickup_available' => 'boolean',
            'pickup_hours' => 'array',
        ];
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(InventoryStock::class);
    }
}
