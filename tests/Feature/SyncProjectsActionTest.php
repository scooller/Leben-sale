<?php

namespace Tests\Feature;

use App\Filament\Actions\SyncProjectsAction;
use App\Models\Asesor;
use App\Models\ContactChannel;
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
                    $this->projectPayload([
                        'id' => 'SF001',
                        'name' => 'Proyecto Test 1',
                    ]),
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
                    $this->projectPayload([
                        'id' => 'SF001',
                        'name' => 'Updated Name',
                    ]),
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

    public function test_sync_projects_does_not_update_is_active_on_existing_projects(): void
    {
        Proyecto::factory()->create([
            'salesforce_id' => 'SF-ACTIVE-001',
            'name' => 'Proyecto Estado Local',
            'is_active' => false,
        ]);

        $this->mock(SalesforceService::class, function (MockInterface $mock) {
            $mock->shouldReceive('findProjects')
                ->once()
                ->andReturn([
                    $this->projectPayload([
                        'id' => 'SF-ACTIVE-001',
                        'name' => 'Proyecto Estado Local Actualizado',
                        'is_active' => true,
                    ]),
                ]);
        });

        $result = SyncProjectsAction::execute();

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('proyectos', [
            'salesforce_id' => 'SF-ACTIVE-001',
            'name' => 'Proyecto Estado Local Actualizado',
            'is_active' => false,
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
                    $this->projectPayload([
                        'id' => 'SF-TIPO-001',
                        'name' => 'Proyecto Tipo',
                        'is_active' => true,
                        'tipo' => ['best', 'broker', 'home', 'icon', 'invest'],
                    ]),
                ]);
        });

        $result = SyncProjectsAction::execute();

        $this->assertTrue($result['success']);

        $project = Proyecto::query()->where('salesforce_id', 'SF-TIPO-001')->firstOrFail();
        $this->assertSame(['best', 'broker', 'home', 'icon', 'invest'], $project->tipo);
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
                    $this->projectPayload([
                        'id' => 'SF-TIPO-002',
                        'name' => 'Proyecto Existing Tipo Updated',
                        'is_active' => true,
                    ]),
                ]);
        });

        $result = SyncProjectsAction::execute();

        $this->assertTrue($result['success']);

        $project = Proyecto::query()->where('salesforce_id', 'SF-TIPO-002')->firstOrFail();
        $this->assertSame(['icon'], $project->tipo);
    }

    public function test_sync_projects_persists_salesforce_logo_and_cover_urls(): void
    {
        $this->mock(SalesforceService::class, function (MockInterface $mock) {
            $mock->shouldReceive('findProjects')
                ->once()
                ->andReturn([
                    $this->projectPayload([
                        'id' => 'SF-BRAND-001',
                        'name' => 'Edificio Indi',
                        'descripcion' => 'Descripción branding',
                        'direccion' => 'Calle Branding 123',
                        'email' => 'branding@example.com',
                        'pagina_web' => 'https://branding.test',
                        'razon_social' => 'Branding SpA',
                        'is_active' => true,
                    ]),
                ]);

            $mock->shouldReceive('findPublicCotizadorDocuments')
                ->once()
                ->andReturn([
                    [
                        'project_name' => 'Edificio Indi',
                        'asset_kind' => 'portada',
                        'download_url' => 'https://example.my.salesforce.com/services/data/v57.0/sobjects/Document/015XX0000000001AAA/Body',
                    ],
                    [
                        'project_name' => 'Edificio Indi',
                        'asset_kind' => 'logo',
                        'download_url' => 'https://example.my.salesforce.com/services/data/v57.0/sobjects/Document/015XX0000000002AAA/Body',
                    ],
                ]);
        });

        $result = SyncProjectsAction::execute();

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('proyectos', [
            'salesforce_id' => 'SF-BRAND-001',
            'salesforce_portada_url' => 'https://example.my.salesforce.com/services/data/v57.0/sobjects/Document/015XX0000000001AAA/Body',
            'salesforce_logo_url' => 'https://example.my.salesforce.com/services/data/v57.0/sobjects/Document/015XX0000000002AAA/Body',
        ]);
    }

    public function test_sync_projects_keeps_existing_branding_when_no_match_is_found(): void
    {
        Proyecto::factory()->create([
            'salesforce_id' => 'SF-BRAND-KEEP-001',
            'name' => 'Proyecto Sin Match',
            'salesforce_logo_url' => 'https://example.my.salesforce.com/old-logo',
            'salesforce_portada_url' => 'https://example.my.salesforce.com/old-portada',
        ]);

        $this->mock(SalesforceService::class, function (MockInterface $mock) {
            $mock->shouldReceive('findProjects')
                ->once()
                ->andReturn([
                    $this->projectPayload([
                        'id' => 'SF-BRAND-KEEP-001',
                        'name' => 'Proyecto Sin Match',
                        'is_active' => true,
                    ]),
                ]);

            $mock->shouldReceive('findPublicCotizadorDocuments')
                ->once()
                ->andReturn([
                    [
                        'project_name' => 'Otro Proyecto',
                        'asset_kind' => 'portada',
                        'download_url' => 'https://example.my.salesforce.com/new-portada',
                    ],
                ]);
        });

        $result = SyncProjectsAction::execute();

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('proyectos', [
            'salesforce_id' => 'SF-BRAND-KEEP-001',
            'salesforce_portada_url' => 'https://example.my.salesforce.com/old-portada',
            'salesforce_logo_url' => 'https://example.my.salesforce.com/old-logo',
        ]);
    }

    public function test_sync_projects_creates_and_links_salesforce_asesores(): void
    {
        $this->mock(SalesforceService::class, function (MockInterface $mock) {
            $mock->shouldReceive('findProjects')
                ->once()
                ->andReturn([
                    $this->projectPayload([
                        'id' => 'SF-PRJ-ASE-001',
                        'name' => 'Proyecto con Asesor',
                        'asesor_responsable_ids' => ['005XX0000001AAA'],
                    ]),
                ]);

            $mock->shouldReceive('findPublicCotizadorDocuments')
                ->once()
                ->andReturn([]);

            $mock->shouldReceive('findSalesforceUsersByIds')
                ->once()
                ->with(['005XX0000001AAA'])
                ->andReturn([
                    [
                        'id' => '005XX0000001AAA',
                        'first_name' => 'Ana',
                        'last_name' => 'Pérez',
                        'email' => 'ana@example.com',
                        'whatsapp_owner' => '+56911111111',
                        'avatar_url' => 'https://example.com/avatar.jpg',
                        'is_active' => true,
                    ],
                ]);
        });

        $result = SyncProjectsAction::execute();

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('asesores', [
            'salesforce_id' => '005XX0000001AAA',
            'first_name' => 'Ana',
            'last_name' => 'Pérez',
            'email' => 'ana@example.com',
            'whatsapp_owner' => '+56911111111',
            'avatar_url' => 'https://example.com/avatar.jpg',
        ]);

        $proyecto = Proyecto::query()->where('salesforce_id', 'SF-PRJ-ASE-001')->firstOrFail();
        $asesor = Asesor::query()->where('salesforce_id', '005XX0000001AAA')->firstOrFail();

        $this->assertTrue($proyecto->asesores->contains($asesor->id));
        $this->assertDatabaseHas('asesor_proyecto', [
            'proyecto_id' => $proyecto->id,
            'asesor_id' => $asesor->id,
        ]);
    }

    public function test_sync_projects_creates_or_updates_contact_channels_from_project_data(): void
    {
        ContactChannel::query()
            ->where('slug', 'sale')
            ->firstOrFail()
            ->update([
                'name' => 'Sale Legacy',
                'domain_patterns' => ['old.sale.cl'],
            ]);

        $this->mock(SalesforceService::class, function (MockInterface $mock) {
            $mock->shouldReceive('findProjects')
                ->once()
                ->andReturn([
                    $this->projectPayload([
                        'id' => 'SF-CH-001',
                        'name' => 'Sale',
                        'pagina_web' => 'https://sale.ileben.cl/landing',
                    ]),
                ]);

            $mock->shouldReceive('findPublicCotizadorDocuments')
                ->once()
                ->andReturn([]);
        });

        $result = SyncProjectsAction::execute();

        $this->assertTrue($result['success']);
        $this->assertSame(0, $result['channels_created']);
        $this->assertSame(1, $result['channels_updated']);

        $channel = ContactChannel::query()->where('slug', 'sale')->firstOrFail();

        $this->assertSame('Sale', $channel->name);
        $this->assertTrue($channel->is_active);
        $this->assertContains('old.sale.cl', (array) $channel->domain_patterns);
        $this->assertContains('sale.ileben.cl', (array) $channel->domain_patterns);
    }

    public function test_sync_projects_creates_missing_contact_channel_for_project_slug(): void
    {
        ContactChannel::query()->where('slug', 'new-project')->delete();

        $this->mock(SalesforceService::class, function (MockInterface $mock) {
            $mock->shouldReceive('findProjects')
                ->once()
                ->andReturn([
                    $this->projectPayload([
                        'id' => 'SF-CH-002',
                        'name' => 'New Project',
                        'pagina_web' => 'new-project.ileben.cl',
                    ]),
                ]);

            $mock->shouldReceive('findPublicCotizadorDocuments')
                ->once()
                ->andReturn([]);
        });

        $result = SyncProjectsAction::execute();

        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['channels_created']);

        $this->assertDatabaseHas('contact_channels', [
            'slug' => 'new-project',
            'name' => 'New Project',
            'is_default' => false,
            'is_active' => true,
        ]);

        $channel = ContactChannel::query()->where('slug', 'new-project')->firstOrFail();
        $this->assertContains('new-project.ileben.cl', (array) $channel->domain_patterns);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function projectPayload(array $overrides = []): array
    {
        return array_merge([
            'id' => 'SF001',
            'name' => 'Proyecto Test',
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
            'valor_reserva_exigido_defecto_peso' => 100000.0,
            'valor_reserva_exigido_min_peso' => 50000.0,
            'entrega_inmediata' => false,
        ], $overrides);
    }
}
