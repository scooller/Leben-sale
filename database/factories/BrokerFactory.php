<?php

namespace Database\Factories;

use App\Models\Broker;
use App\Models\BrokerCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Broker>
 */
class BrokerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'broker_category_id' => BrokerCategory::factory(),
            'avatar_image_id' => null,
            'display_name' => fake()->name(),
            'contact_email' => fake()->safeEmail(),
            'contact_phone' => fake()->phoneNumber(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 30),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
