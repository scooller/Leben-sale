<?php

namespace Database\Factories;

use App\Models\BrokerBenefit;
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
            'section' => fake()->randomElement(['Comunicación', 'Capacitación', 'Negocio', 'Visitas', 'Pagos', 'Beneficios Adicionales']),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->sentence(),
            'sort_order' => fake()->numberBetween(0, 50),
            'is_active' => true,
        ];
    }
}
