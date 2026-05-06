<?php

namespace Database\Factories;

use App\Models\BrokerBenefit;
use App\Models\BrokerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BrokerBenefit>
 */
class BrokerBenefitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'broker_category_id' => BrokerCategory::factory(),
            'section' => fake()->randomElement(['comunicacion', 'capacitacion', 'negocio', 'visitas', 'pagos', 'beneficios']),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['included', 'not_applicable']),
            'sort_order' => fake()->numberBetween(0, 50),
            'is_active' => true,
        ];
    }
}
