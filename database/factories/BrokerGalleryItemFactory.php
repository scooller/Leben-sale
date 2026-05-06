<?php

namespace Database\Factories;

use App\Models\BrokerGallery;
use App\Models\BrokerGalleryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BrokerGalleryItem>
 */
class BrokerGalleryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'broker_gallery_id' => BrokerGallery::factory(),
            'image_id' => null,
            'caption' => fake()->optional()->sentence(),
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }
}
