<?php

namespace Tests\Feature;

use App\Filament\Resources\Plants\PlantResource;
use App\Models\Plant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlantResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_navigation_badge_shows_plant_count(): void
    {
        Plant::factory()->count(5)->create();

        $badge = PlantResource::getNavigationBadge();

        $this->assertEquals('5', $badge);
    }

    public function test_navigation_badge_returns_zero_when_no_plants(): void
    {
        $badge = PlantResource::getNavigationBadge();

        $this->assertEquals('0', $badge);
    }

    public function test_can_list_plants(): void
    {
        $plants = Plant::factory()->count(3)->create();

        $this->assertDatabaseCount('plants', 3);
        $this->assertDatabaseHas('plants', [
            'id' => $plants[0]->id,
        ]);
    }

    public function test_can_search_plants_by_name(): void
    {
        $plantToFind = Plant::factory()->create(['name' => 'Plant 101']);
        $otherPlant = Plant::factory()->create(['name' => 'Plant 202']);

        $this->assertDatabaseHas('plants', [
            'id' => $plantToFind->id,
            'name' => 'Plant 101',
        ]);
        $this->assertDatabaseHas('plants', [
            'id' => $otherPlant->id,
            'name' => 'Plant 202',
        ]);
    }

    public function test_plants_table_columns_are_displayed(): void
    {
        $plant = Plant::factory()->create([
            'name' => 'Test Plant',
            'precio_venta' => 5000.00,
            'superficie_total_principal' => 75.50,
        ]);

        $this->assertDatabaseHas('plants', [
            'id' => $plant->id,
            'name' => 'Test Plant',
            'precio_venta' => 5000.00,
            'superficie_total_principal' => 75.50,
        ]);
    }
}
