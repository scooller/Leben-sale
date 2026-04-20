<?php

namespace Tests\Feature\Api;

use App\Models\FrontendPreviewLink;
use App\Models\Plant;
use App\Models\Proyecto;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_proyectos_endpoint_returns_401_when_unauthenticated(): void
    {
        $response = $this->get('/api/v1/proyectos');

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_me_endpoint_returns_401_when_unauthenticated(): void
    {
        $response = $this->get('/api/v1/me');

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_plantas_endpoint_returns_401_when_unauthenticated_and_without_preview_token(): void
    {
        $response = $this->get('/api/v1/plantas');

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_catalog_endpoints_allow_access_with_valid_preview_token(): void
    {
        SiteSetting::current()->update([
            'mostrar_plantas' => false,
        ]);

        $project = Proyecto::factory()->create([
            'is_active' => true,
            'slug' => 'preview-project',
        ]);

        $plant = Plant::factory()->create([
            'salesforce_proyecto_id' => $project->salesforce_id,
            'is_active' => true,
        ]);

        $plainToken = Str::random(64);

        FrontendPreviewLink::query()->create([
            'name' => 'preview-catalog',
            'token' => $plainToken,
            'expires_at' => Carbon::now()->addHour(),
        ]);

        $this->getJson('/api/v1/proyectos?preview_token='.$plainToken)
            ->assertOk()
            ->assertJsonFragment([
                'id' => $project->id,
                'name' => $project->name,
            ]);

        $this->getJson('/api/v1/plantas?preview_token='.$plainToken)
            ->assertOk()
            ->assertJsonFragment([
                'id' => $plant->id,
            ]);
    }

    public function test_catalog_endpoints_reject_expired_preview_token(): void
    {
        $plainToken = Str::random(64);

        FrontendPreviewLink::query()->create([
            'name' => 'preview-expired',
            'token' => $plainToken,
            'expires_at' => Carbon::now()->subMinute(),
        ]);

        $this->getJson('/api/v1/plantas?preview_token='.$plainToken)
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
