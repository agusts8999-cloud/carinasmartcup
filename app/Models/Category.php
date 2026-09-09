<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;
    /**
     * @var list<string>
     */
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'sort_order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function latestProductWithImage(): HasOne
    {
        return $this->hasOne(Product::class)
            ->ofMany(
                ['published_at' => 'max'],
                fn (Builder $query) => $query->published()->whereHas('media')
            );
    }

    public function imageUrl(): ?string
    {
        $product = $this->relationLoaded('latestProductWithImage')
            && $this->latestProductWithImage?->relationLoaded('media')
            ? $this->latestProductWithImage
            : $this->latestProductWithImage()->with('media')->first();

        if ($product === null) {
            return null;
        }

        return $product->primaryImageUrl();
    }

    public function volumeRules(): HasMany
    {
        return $this->hasMany(VolumeRule::class);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }
}
