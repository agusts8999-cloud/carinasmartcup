<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'number',
        'user_id',
        'status',
        'customer_name',
        'customer_email',
        'customer_whatsapp',
        'shipping_address_snapshot',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'coupon_discount',
        'shipping_amount',
        'shipping_rate_name',
        'shipping_estimate_note',
        'total',
        'weight_gram',
        'volume_cm3',
        'koli_count',
        'notes',
        'payment_method',
        'payment_due_at',
        'reserved_until',
        'paid_at',
        'cancelled_at',
        'shipping_accepted_estimate',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'shipping_address_snapshot' => 'array',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'coupon_discount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'weight_gram' => 'integer',
            'volume_cm3' => 'integer',
            'koli_count' => 'integer',
            'payment_method' => PaymentMethod::class,
            'payment_due_at' => 'datetime',
            'reserved_until' => 'datetime',
            'paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'shipping_accepted_estimate' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }
}
