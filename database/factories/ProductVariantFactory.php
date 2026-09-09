<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####-??')),
            'name' => fake()->words(2, true),
            'color' => fake()->randomElement(['Bening', 'Putih', 'Merah']),
            'unit_type' => 'pcs',
            'pack_size' => 50,
            'price_retail' => fake()->numberBetween(150, 500),
            'weight_gram' => fake()->numberBetween(5, 25),
            'length_cm' => 8,
            'width_cm' => 8,
            'height_cm' => 12,
            'min_order_qty' => 50,
            'order_multiple' => 50,
            'is_active' => true,
        ];
    }
}
