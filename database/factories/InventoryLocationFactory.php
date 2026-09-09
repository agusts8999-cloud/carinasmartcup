<?php

namespace Database\Factories;

use App\Models\InventoryLocation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<InventoryLocation>
 */
class InventoryLocationFactory extends Factory
{
    protected $model = InventoryLocation::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company().' Warehouse',
            'code' => strtoupper(Str::random(4)),
            'address' => fake()->address(),
            'is_default' => true,
            'is_active' => true,
            'pickup_available' => true,
            'pickup_hours' => [
                'monday' => ['09:00', '17:00'],
                'tuesday' => ['09:00', '17:00'],
            ],
        ];
    }
}
