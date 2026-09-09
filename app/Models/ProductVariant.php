<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use HasFactory;
    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'name',
        'color',
        'lid_type',
        'unit_type',
        'pack_size',
        'price_retail',
        'weight_gram',
        'length_cm',
        'width_cm',
        'height_cm',
        'min_order_qty',
        'order_multiple',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'pack_size' => 'integer',
            'price_retail' => 'decimal:2',
            'weight_gram' => 'integer',
            'length_cm' => 'decimal:2',
            'width_cm' => 'decimal:2',
            'height_cm' => 'decimal:2',
            'min_order_qty' => 'integer',
            'order_multiple' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function inventoryStocks(): HasMany
    {
        return $this->hasMany(InventoryStock::class);
    }

    public function bundleItems(): HasMany
    {
        return $this->hasMany(ProductBundleItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function availableQty(): int
    {
        return (int) $this->inventoryStocks()
            ->selectRaw('COALESCE(SUM(qty_on_hand - qty_reserved), 0) as available')
            ->value('available');
    }
}
