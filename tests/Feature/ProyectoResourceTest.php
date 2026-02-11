<?php

namespace Tests\Feature;

use App\Filament\Resources\ProyectoResource;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProyectoResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_navigation_badge_shows_proyecto_count(): void
    {
        Proyecto::factory()->count(3)->create();

        $badge = ProyectoResource::getNavigationBadge();

        $this->assertEquals('3', $badge);
    }

    public function test_navigation_badge_returns_zero_when_no_proyectos(): void
    {
        $badge = ProyectoResource::getNavigationBadge();

        $this->assertEquals('0', $badge);
    }

    public function test_can_list_proyectos(): void
    {
        $proyectos = Proyecto::factory()->count(3)->create();

        $this->assertDatabaseCount('proyectos', 3);
        $this->assertDatabaseHas('proyectos', [
            'id' => $proyectos[0]->id,
        ]);
    }

    public function test_can_search_proyectos_by_name(): void
    {
        $proyectoToFind = Proyecto::factory()->create(['name' => 'Torre Especial']);
        $otherProyecto = Proyecto::factory()->create(['name' => 'Edificio Común']);

        $this->assertDatabaseHas('proyectos', [
            'id' => $proyectoToFind->id,
            'name' => 'Torre Especial',
        ]);
        $this->assertDatabaseHas('proyectos', [
            'id' => $otherProyecto->id,
            'name' => 'Edificio Común',
        ]);
    }

    public function test_can_create_proyecto(): void
    {
        $proyectoData = [
            'salesforce_id' => fake()->unique()->uuid(),
            'name' => 'Test Proyecto',
            'descripcion' => 'Descripción test',
            'direccion' => 'Calle test 123',
            'comuna' => 'Santiago',
            'provincia' => 'Santiago',
            'region' => 'Metropolitana',
            'email' => 'test@example.com',
            'telefono' => '123456789',
            'pagina_web' => 'https://test.com',
            'razon_social' => 'Test SpA',
            'rut' => '12345678-9',
        ];

        $proyecto = Proyecto::create($proyectoData);

        $this->assertDatabaseHas('proyectos', [
            'id' => $proyecto->id,
            'name' => 'Test Proyecto',
        ]);
    }

    public function test_proyecto_name_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Proyecto::create([
            'name' => null,
        ]);
    }
}
