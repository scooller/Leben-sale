<?php

namespace Database\Factories;

use App\Models\Broker;
use App\Models\BrokerAlliance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BrokerAlliance>
 */
class BrokerAllianceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'broker_id' => Broker::factory(),
            'image_id' => null,
            'name' => fake()->company(),
            'url' => fake()->optional()->url(),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }
}
