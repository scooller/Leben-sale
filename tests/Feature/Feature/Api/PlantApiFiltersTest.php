<?php

namespace Tests\Feature\Feature\Api;

use App\Enums\ReservationStatus;
use App\Models\Plant;
use App\Models\PlantReservation;
use App\Models\Proyecto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PlantApiFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_plants_by_proyecto_id(): void
    {
        $targetProject = Proyecto::factory()->create();
        $otherProject = Proyecto::factory()->create();

        $plantInTargetProject = $this->createPlant($targetProject->salesforce_id, true);

        $plantInOtherProject = $this->createPlant($otherProject->salesforce_id, true);

        $response = $this->getJson('/api/v1/plantas?proyecto_id='.$targetProject->id);

        $response->assertOk();
        $responsePlantIds = collect($response->json('data'))->pluck('id')->all();

        $this->assertContains($plantInTargetProject->id, $responsePlantIds);
        $this->assertNotContains($plantInOtherProject->id, $responsePlantIds);
    }

    public function test_it_filters_plants_by_availability(): void
    {
        $project = Proyecto::factory()->create();

        $availablePlant = $this->createPlant($project->salesforce_id, true);

        $reservedPlant = $this->createPlant($project->salesforce_id, true);

        PlantReservation::query()->create([
            'plant_id' => $reservedPlant->id,
            'session_token' => Str::random(64),
            'status' => ReservationStatus::ACTIVE,
            'expires_at' => now()->addMinutes(30),
        ]);

        $availableResponse = $this->getJson('/api/v1/plantas?proyecto_id='.$project->id.'&disponible=1');
        $availableResponse->assertOk();
        $availablePlantIds = collect($availableResponse->json('data'))->pluck('id')->all();

        $this->assertContains($availablePlant->id, $availablePlantIds);
        $this->assertNotContains($reservedPlant->id, $availablePlantIds);

        $unavailableResponse = $this->getJson('/api/v1/plantas?proyecto_id='.$project->id.'&disponible=0');
        $unavailableResponse->assertOk();
        $unavailablePlantIds = collect($unavailableResponse->json('data'))->pluck('id')->all();

        $this->assertContains($reservedPlant->id, $unavailablePlantIds);
        $this->assertNotContains($availablePlant->id, $unavailablePlantIds);
    }

    public function test_it_excludes_plants_from_inactive_projects(): void
    {
        $activeProject = Proyecto::factory()->create([
            'is_active' => true,
        ]);

        $inactiveProject = Proyecto::factory()->create([
            'is_active' => false,
        ]);

        $activePlant = $this->createPlant($activeProject->salesforce_id, true);
        $inactiveProjectPlant = $this->createPlant($inactiveProject->salesforce_id, true);

        $response = $this->getJson('/api/v1/plantas');

        $response->assertOk();
        $responsePlantIds = collect($response->json('data'))->pluck('id')->all();

        $this->assertContains($activePlant->id, $responsePlantIds);
        $this->assertNotContains($inactiveProjectPlant->id, $responsePlantIds);
    }

    public function test_it_returns_limited_proyecto_fields_in_plantas_response(): void
    {
        $project = Proyecto::factory()->create([
            'name' => 'Proyecto API',
            'direccion' => 'Av. Siempre Viva 123',
            'comuna' => 'Santiago',
            'pagina_web' => 'https://proyecto.test',
            'region' => 'Metropolitana',
            'email' => 'hidden@example.com',
        ]);

        $this->createPlant($project->salesforce_id, true);

        $response = $this->getJson('/api/v1/plantas?proyecto_id='.$project->id);

        $response->assertOk();

        $proyectoPayload = $response->json('data.0.proyecto');

        $this->assertSame([
            'id',
            'name',
            'direccion',
            'comuna',
            'pagina_web',
        ], array_keys($proyectoPayload));

        $this->assertSame('Proyecto API', $proyectoPayload['name']);
        $this->assertArrayNotHasKey('region', $proyectoPayload);
        $this->assertArrayNotHasKey('email', $proyectoPayload);
    }

    private function createPlant(string $salesforceProyectoId, bool $isActive): Plant
    {
        return Plant::query()->create([
            'salesforce_product_id' => (string) Str::uuid(),
            'salesforce_proyecto_id' => $salesforceProyectoId,
            'name' => (string) random_int(100, 999),
            'product_code' => 'PLANT-'.random_int(1000, 9999),
            'programa' => '2 dormitorios',
            'programa2' => '2 baños',
            'precio_base' => 5000,
            'precio_lista' => 5500,
            'is_active' => $isActive,
            'last_synced_at' => now(),
        ]);
    }
}
