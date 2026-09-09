<?php

namespace Database\Factories;

use App\Models\InventoryLocation;
use App\Models\InventoryStock;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryStock>
 */
class InventoryStockFactory extends Factory
{
    protected $model = InventoryStock::class;

    public function definition(): array
    {
        return [
            'inventory_location_id' => InventoryLocation::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'qty_on_hand' => fake()->numberBetween(500, 2000),
            'qty_reserved' => 0,
        ];
    }
}
