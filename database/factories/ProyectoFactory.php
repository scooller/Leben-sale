<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proyecto>
 */
class ProyectoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'salesforce_id' => fake()->unique()->uuid(),
            'name' => fake()->company().' '.fake()->randomElement(['Tower', 'Residencial', 'Edificio']),
            'descripcion' => fake()->sentence(10),
            'direccion' => fake()->streetAddress(),
            'comuna' => fake()->randomElement(['Santiago', 'Providencia', 'Las Condes', 'Vitacura', 'Ñuñoa']),
            'provincia' => 'Santiago',
            'region' => 'Metropolitana',
            'email' => fake()->companyEmail(),
            'telefono' => fake()->phoneNumber(),
            'pagina_web' => fake()->url(),
            'razon_social' => fake()->company(),
            'rut' => fake()->numerify('########-#'),
            'fecha_inicio_ventas' => fake()->dateTimeBetween('-1 year', 'now'),
            'fecha_entrega' => fake()->dateTimeBetween('now', '+2 years'),
            'etapa' => fake()->randomElement(['Preventa', 'En Construcción', 'Entrega Inmediata']),
            'horario_atencion' => 'Lunes a Viernes 9:00 - 18:00',
            'dscto_m_x_prod_principal_porc' => fake()->randomFloat(2, 0, 20),
            'dscto_m_x_prod_principal_uf' => fake()->randomFloat(2, 0, 100),
            'dscto_m_x_bodega_porc' => fake()->randomFloat(2, 0, 15),
            'dscto_m_x_bodega_uf' => fake()->randomFloat(2, 0, 50),
            'dscto_m_x_estac_porc' => fake()->randomFloat(2, 0, 10),
            'dscto_m_x_estac_uf' => fake()->randomFloat(2, 0, 30),
            'dscto_max_otros_porc' => fake()->randomFloat(2, 0, 25),
            'dscto_max_otros_prod_uf' => fake()->randomFloat(2, 0, 150),
            'dscto_maximo_aporte_leben' => fake()->randomFloat(2, 0, 200),
            'n_anos_1' => fake()->numberBetween(1, 5),
            'n_anos_2' => fake()->numberBetween(5, 10),
            'n_anos_3' => fake()->numberBetween(10, 15),
            'n_anos_4' => fake()->numberBetween(15, 20),
            'valor_reserva_exigido_defecto_peso' => fake()->randomFloat(2, 100000, 500000),
            'valor_reserva_exigido_min_peso' => fake()->randomFloat(2, 50000, 200000),
            'tasa' => fake()->randomFloat(2, 3, 8),
            'entrega_inmediata' => fake()->boolean(30),
        ];
    }
}
