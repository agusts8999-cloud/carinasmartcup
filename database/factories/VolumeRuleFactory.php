<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\VolumeRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VolumeRule>
 */
class VolumeRuleFactory extends Factory
{
    protected $model = VolumeRule::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'category_id' => Category::factory(),
            'applies_to_all' => false,
            'allows_mix' => true,
            'is_active' => true,
            'basis' => 'quantity',
        ];
    }
}
