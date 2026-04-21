<?php

namespace Database\Factories;

use App\Enums\ShortLinkStatus;
use App\Models\ShortLink;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ShortLink>
 */
class ShortLinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => Str::lower(Str::random(7)),
            'title' => fake()->sentence(3),
            'destination_url' => fake()->url(),
            'status' => ShortLinkStatus::ACTIVE,
            'tag_manager_id' => null,
            'visits_count' => 0,
            'last_visited_at' => null,
            'expires_at' => null,
            'metadata' => [],
        ];
    }

    public function disabled(): static
    {
        return $this->state(fn (): array => [
            'status' => ShortLinkStatus::DISABLED,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (): array => [
            'status' => ShortLinkStatus::ACTIVE,
            'expires_at' => now()->subMinute(),
        ]);
    }
}
