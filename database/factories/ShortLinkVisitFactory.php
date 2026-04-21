<?php

namespace Database\Factories;

use App\Models\ShortLink;
use App\Models\ShortLinkVisit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShortLinkVisit>
 */
class ShortLinkVisitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ShortLinkVisit>
     */
    protected $model = ShortLinkVisit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'short_link_id' => ShortLink::factory(),
            'visited_at' => now(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'referer' => fake()->url(),
            'utm_source' => fake()->word(),
            'utm_medium' => fake()->word(),
            'utm_campaign' => fake()->word(),
            'utm_term' => null,
            'utm_content' => null,
            'session_fingerprint' => fake()->sha256(),
            'query_params' => [],
        ];
    }
}
