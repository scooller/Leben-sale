<?php

namespace Database\Factories;

use App\Models\BrokerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BrokerCategory>
 */
class BrokerCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'Partner ' . fake()->randomElement(['Black', 'Gold', 'Silver']);

        return [
            'name' => $name,
            'slug' => str($name)->slug()->value(),
            'headline' => fake()->sentence(),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }
}
