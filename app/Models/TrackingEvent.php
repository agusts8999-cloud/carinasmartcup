<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TrackingEvent extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'shipment_id',
        'status',
        'description',
        'happened_at',
        'actor_type',
        'actor_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'happened_at' => 'datetime',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }
}
