<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolumeRuleTier extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'volume_rule_id',
        'min_value',
        'discount_type',
        'discount_value',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'min_value' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function volumeRule(): BelongsTo
    {
        return $this->belongsTo(VolumeRule::class);
    }
}
