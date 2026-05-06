<?php

namespace Database\Factories;

use App\Models\BrokerBenefit;
use App\Models\BrokerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;

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
        $data = [
            'section' => fake()->randomElement(['Comunicación', 'Capacitación', 'Negocio', 'Visitas', 'Pagos', 'Beneficios Adicionales']),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->sentence(),
            'sort_order' => fake()->numberBetween(0, 50),
            'is_active' => true,
        ];

        if (Schema::hasColumn('broker_benefits', 'broker_category_id')) {
            $data['broker_category_id'] = fn (): int => BrokerCategory::query()->inRandomOrder()->value('id')
                ?? BrokerCategory::factory()->create()->id;
        }

        if (Schema::hasColumn('broker_benefits', 'status')) {
            $data['status'] = fake()->randomElement(['included', 'not_applicable']);
        }

        return $data;
    }
}
