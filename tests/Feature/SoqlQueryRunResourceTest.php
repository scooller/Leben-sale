<?php

namespace Tests\Feature;

use App\Filament\Resources\SoqlQueryRuns\SoqlQueryRunResource;
use App\Models\SoqlQueryRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use Tests\TestCase;

class SoqlQueryRunResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_soql_runner_resource(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create([
            'user_type' => 'admin',
        ]);

        $this->actingAs($admin);

        $this->assertTrue(SoqlQueryRunResource::shouldRegisterNavigation());
        $this->assertTrue(SoqlQueryRunResource::canViewAny());
        $this->assertTrue(SoqlQueryRunResource::canDeleteAny());
        $this->assertSame('Herramientas', SoqlQueryRunResource::getNavigationGroup());

        $run = SoqlQueryRun::query()->create([
            'user_id' => $admin->id,
            'soql' => 'SELECT Id FROM Lead LIMIT 1',
            'status' => 'success',
            'records_count' => 1,
            'duration_ms' => 10,
            'limit_value' => 1,
            'result_preview' => ['total_size' => 1],
            'meta' => ['source' => 'test'],
        ]);

        $this->assertTrue(SoqlQueryRunResource::canDelete($run));

        $resourceClass = new ReflectionClass(SoqlQueryRunResource::class);
        $navigationSort = $resourceClass->getStaticPropertyValue('navigationSort');

        $this->assertSame(2, $navigationSort);

        $this->get(SoqlQueryRunResource::getUrl('index'))
            ->assertOk()
            ->assertSee('Ejecutar SOQL')
            ->assertSee('Reutilizar');
    }

    public function test_marketing_cannot_access_soql_runner_resource(): void
    {
        /** @var User $marketing */
        $marketing = User::factory()->create([
            'user_type' => 'marketing',
        ]);

        $this->actingAs($marketing);

        $this->assertFalse(SoqlQueryRunResource::shouldRegisterNavigation());
        $this->assertFalse(SoqlQueryRunResource::canViewAny());
        $this->assertFalse(SoqlQueryRunResource::canDeleteAny());
    }
}
