<?php

namespace Tests\Feature;

use App\Filament\Actions\SyncProjectsAction;
use App\Models\Proyecto;
use App\Services\Salesforce\SalesforceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class SyncProjectsActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_projects_creates_new_projects(): void
    {
        $this->mock(SalesforceService::class, function (MockInterface $mock) {
            $mock->shouldReceive('findProjects')
                ->once()
                ->andReturn([
                    [
                        'id' => 'SF001',
                        'name' => 'Proyecto Test 1',
                        'descripcion' => 'Descripción test',
                        'direccion' => 'Calle Test 123',
                        'comuna' => 'Santiago',
                        'provincia' => 'Santiago',
                        'region' => 'Metropolitana',
                        'email' => 'test@example.com',
                        'telefono' => '123456789',
                        'pagina_web' => 'https://test.com',
                        'razon_social' => 'Test SpA',
                        'rut' => '12345678-9',
                        'fecha_inicio_ventas' => now()->toDateString(),
                        'fecha_entrega' => now()->addYear()->toDateString(),
                        'etapa' => 'Preventa',
                        'horario_atencion' => 'Lunes a Viernes',
                        'dscto_m_x_prod_principal_porc' => 10.0,
                        'dscto_m_x_prod_principal_uf' => 50.0,
                        'dscto_m_x_bodega_porc' => 5.0,
                        'dscto_m_x_bodega_uf' => 25.0,
                        'dscto_m_x_estac_porc' => 3.0,
                        'dscto_m_x_estac_uf' => 15.0,
                        'dscto_max_otros_porc' => 15.0,
                        'dscto_max_otros_prod_uf' => 75.0,
                        'dscto_maximo_aporte_leben' => 100.0,
                        'n_anos_1' => 1,
                        'n_anos_2' => 5,
                        'n_anos_3' => 10,
                        'n_anos_4' => 15,
                        'valor_reserva_exigido_defecto_peso' => 100000.0,
                        'valor_reserva_exigido_min_peso' => 50000.0,
                        'tasa' => 5.0,
                        'entrega_inmediata' => false,
                    ],
                ]);
        });

        $result = SyncProjectsAction::execute();

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['created']);
        $this->assertEquals(0, $result['updated']);
        $this->assertDatabaseHas('proyectos', [
            'salesforce_id' => 'SF001',
            'name' => 'Proyecto Test 1',
        ]);
    }

    public function test_sync_projects_updates_existing_projects(): void
    {
        Proyecto::factory()->create([
            'salesforce_id' => 'SF001',
            'name' => 'Old Name',
        ]);

        $this->mock(SalesforceService::class, function (MockInterface $mock) {
            $mock->shouldReceive('findProjects')
                ->once()
                ->andReturn([
                    [
                        'id' => 'SF001',
                        'name' => 'Updated Name',
                        'descripcion' => 'Descripción test',
                        'direccion' => 'Calle Test 123',
                        'comuna' => 'Santiago',
                        'provincia' => 'Santiago',
                        'region' => 'Metropolitana',
                        'email' => 'test@example.com',
                        'telefono' => '123456789',
                        'pagina_web' => 'https://test.com',
                        'razon_social' => 'Test SpA',
                        'rut' => '12345678-9',
                        'fecha_inicio_ventas' => now()->toDateString(),
                        'fecha_entrega' => now()->addYear()->toDateString(),
                        'etapa' => 'Preventa',
                        'horario_atencion' => 'Lunes a Viernes',
                        'dscto_m_x_prod_principal_porc' => 10.0,
                        'dscto_m_x_prod_principal_uf' => 50.0,
                        'dscto_m_x_bodega_porc' => 5.0,
                        'dscto_m_x_bodega_uf' => 25.0,
                        'dscto_m_x_estac_porc' => 3.0,
                        'dscto_m_x_estac_uf' => 15.0,
                        'dscto_max_otros_porc' => 15.0,
                        'dscto_max_otros_prod_uf' => 75.0,
                        'dscto_maximo_aporte_leben' => 100.0,
                        'n_anos_1' => 1,
                        'n_anos_2' => 5,
                        'n_anos_3' => 10,
                        'n_anos_4' => 15,
                        'valor_reserva_exigido_defecto_peso' => 100000.0,
                        'valor_reserva_exigido_min_peso' => 50000.0,
                        'tasa' => 5.0,
                        'entrega_inmediata' => false,
                    ],
                ]);
        });

        $result = SyncProjectsAction::execute();

        $this->assertTrue($result['success']);
        $this->assertEquals(0, $result['created']);
        $this->assertEquals(1, $result['updated']);
        $this->assertDatabaseHas('proyectos', [
            'salesforce_id' => 'SF001',
            'name' => 'Updated Name',
        ]);
    }

    public function test_sync_projects_handles_empty_results(): void
    {
        $this->mock(SalesforceService::class, function (MockInterface $mock) {
            $mock->shouldReceive('findProjects')
                ->once()
                ->andReturn([]);
        });

        $result = SyncProjectsAction::execute();

        $this->assertFalse($result['success']);
        $this->assertEquals(0, $result['count']);
        $this->assertEquals('No se encontraron proyectos en Salesforce', $result['message']);
    }

    public function test_sync_projects_persists_tipo_multiselect_values(): void
    {
        $this->mock(SalesforceService::class, function (MockInterface $mock) {
            $mock->shouldReceive('findProjects')
                ->once()
                ->andReturn([
                    [
                        'id' => 'SF-TIPO-001',
                        'name' => 'Proyecto Tipo',
                        'descripcion' => 'Descripción test',
                        'direccion' => 'Calle Test 123',
                        'comuna' => 'Santiago',
                        'provincia' => 'Santiago',
                        'region' => 'Metropolitana',
                        'email' => 'test@example.com',
                        'telefono' => '123456789',
                        'pagina_web' => 'https://test.com',
                        'razon_social' => 'Test SpA',
                        'rut' => '12345678-9',
                        'fecha_inicio_ventas' => now()->toDateString(),
                        'fecha_entrega' => now()->addYear()->toDateString(),
                        'etapa' => 'Preventa',
                        'horario_atencion' => 'Lunes a Viernes',
                        'is_active' => true,
                        'tipo' => ['invest', 'broker', 'invalid'],
                        'dscto_m_x_prod_principal_porc' => 10.0,
                        'dscto_m_x_prod_principal_uf' => 50.0,
                        'dscto_m_x_bodega_porc' => 5.0,
                        'dscto_m_x_bodega_uf' => 25.0,
                        'dscto_m_x_estac_porc' => 3.0,
                        'dscto_m_x_estac_uf' => 15.0,
                        'dscto_max_otros_porc' => 15.0,
                        'dscto_max_otros_prod_uf' => 75.0,
                        'dscto_maximo_aporte_leben' => 100.0,
                        'n_anos_1' => 1,
                        'n_anos_2' => 5,
                        'n_anos_3' => 10,
                        'n_anos_4' => 15,
                        'valor_reserva_exigido_defecto_peso' => 100000.0,
                        'valor_reserva_exigido_min_peso' => 50000.0,
                        'tasa' => 5.0,
                        'entrega_inmediata' => false,
                    ],
                ]);
        });

        $result = SyncProjectsAction::execute();

        $this->assertTrue($result['success']);

        $project = Proyecto::query()->where('salesforce_id', 'SF-TIPO-001')->firstOrFail();
        $this->assertSame(['invest', 'broker'], $project->tipo);
    }

    public function test_sync_projects_does_not_overwrite_tipo_when_missing_in_payload(): void
    {
        Proyecto::factory()->create([
            'salesforce_id' => 'SF-TIPO-002',
            'name' => 'Proyecto Existing Tipo',
            'tipo' => ['icon'],
        ]);

        $this->mock(SalesforceService::class, function (MockInterface $mock) {
            $mock->shouldReceive('findProjects')
                ->once()
                ->andReturn([
                    [
                        'id' => 'SF-TIPO-002',
                        'name' => 'Proyecto Existing Tipo Updated',
                        'descripcion' => 'Descripción test',
                        'direccion' => 'Calle Test 123',
                        'comuna' => 'Santiago',
                        'provincia' => 'Santiago',
                        'region' => 'Metropolitana',
                        'email' => 'test@example.com',
                        'telefono' => '123456789',
                        'pagina_web' => 'https://test.com',
                        'razon_social' => 'Test SpA',
                        'rut' => '12345678-9',
                        'fecha_inicio_ventas' => now()->toDateString(),
                        'fecha_entrega' => now()->addYear()->toDateString(),
                        'etapa' => 'Preventa',
                        'horario_atencion' => 'Lunes a Viernes',
                        'is_active' => true,
                        'dscto_m_x_prod_principal_porc' => 10.0,
                        'dscto_m_x_prod_principal_uf' => 50.0,
                        'dscto_m_x_bodega_porc' => 5.0,
                        'dscto_m_x_bodega_uf' => 25.0,
                        'dscto_m_x_estac_porc' => 3.0,
                        'dscto_m_x_estac_uf' => 15.0,
                        'dscto_max_otros_porc' => 15.0,
                        'dscto_max_otros_prod_uf' => 75.0,
                        'dscto_maximo_aporte_leben' => 100.0,
                        'n_anos_1' => 1,
                        'n_anos_2' => 5,
                        'n_anos_3' => 10,
                        'n_anos_4' => 15,
                        'valor_reserva_exigido_defecto_peso' => 100000.0,
                        'valor_reserva_exigido_min_peso' => 50000.0,
                        'tasa' => 5.0,
                        'entrega_inmediata' => false,
                    ],
                ]);
        });

        $result = SyncProjectsAction::execute();

        $this->assertTrue($result['success']);

        $project = Proyecto::query()->where('salesforce_id', 'SF-TIPO-002')->firstOrFail();
        $this->assertSame(['icon'], $project->tipo);
    }
}
