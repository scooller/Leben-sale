<?php

namespace Database\Factories;

use App\Models\Broker;
use App\Models\BrokerEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BrokerEvent>
 */
class BrokerEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('-1 month', '+2 months');

        return [
            'broker_id' => Broker::factory(),
            'image_id' => null,
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'starts_at' => $startsAt,
            'ends_at' => fake()->optional()->dateTimeBetween($startsAt, '+3 months'),
            'location' => fake()->optional()->address(),
            'is_published' => true,
        ];
    }
}
