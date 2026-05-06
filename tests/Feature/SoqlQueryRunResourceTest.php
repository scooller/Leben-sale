<?php

namespace Tests\Feature;

use App\Filament\Resources\SoqlQueryRuns\SoqlQueryRunResource;
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
        $this->assertSame('Herramientas', SoqlQueryRunResource::getNavigationGroup());

        $resourceClass = new ReflectionClass(SoqlQueryRunResource::class);
        $navigationSort = $resourceClass->getStaticPropertyValue('navigationSort');

        $this->assertSame(2, $navigationSort);

        $this->get(SoqlQueryRunResource::getUrl('index'))
            ->assertOk()
            ->assertSee('Ejecutar SOQL');
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
    }
}
