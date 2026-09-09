<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'material' => fake()->randomElement(['PP', 'PET', 'Paper']),
            'capacity_ml' => fake()->randomElement([240, 350, 470]),
            'size_label' => fake()->randomElement(['8oz', '12oz', '16oz']),
            'status' => ProductStatus::Published,
            'is_featured' => false,
            'published_at' => now(),
        ];
    }
}
