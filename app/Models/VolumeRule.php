<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VolumeRule extends Model
{
    /** @use HasFactory<\Database\Factories\VolumeRuleFactory> */
    use HasFactory;
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'category_id',
        'applies_to_all',
        'allows_mix',
        'is_active',
        'basis',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'applies_to_all' => 'boolean',
            'allows_mix' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(VolumeRuleTier::class);
    }
}
