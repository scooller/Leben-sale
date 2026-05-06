<?php

namespace Database\Factories;

use App\Models\Broker;
use App\Models\BrokerGallery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BrokerGallery>
 */
class BrokerGalleryFactory extends Factory
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
            'title' => 'Galeria ' . fake()->monthName() . ' ' . fake()->year(),
            'year' => (int) fake()->year(),
            'month' => (int) fake()->month(),
            'is_published' => true,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}
