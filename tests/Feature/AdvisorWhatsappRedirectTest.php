<?php

namespace Tests\Feature;

use App\Models\Asesor;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvisorWhatsappRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_redirects_directly_to_whatsapp_when_tag_manager_is_missing(): void
    {
        $asesor = Asesor::factory()->create([
            'first_name' => 'Camila',
            'whatsapp_owner' => '+56 9 8765 4321',
            'is_active' => true,
        ]);

        SiteSetting::current()->update([
            'tag_manager_id' => null,
        ]);

        $response = $this->get(route('advisors.whatsapp.redirect', ['asesor' => $asesor]).'?text=Hola%20Camila');

        $response->assertRedirect('https://wa.me/56987654321?text=Hola%20Camila');
    }

    public function test_it_renders_bridge_page_for_whatsapp_when_tag_manager_exists(): void
    {
        $asesor = Asesor::factory()->create([
            'first_name' => 'Camila',
            'last_name' => 'Diaz',
            'email' => 'camila@example.com',
            'whatsapp_owner' => '+56 9 8765 4321',
            'is_active' => true,
        ]);

        SiteSetting::current()->update([
            'tag_manager_id' => 'GTM-TEST123',
        ]);

        $response = $this->get(route('advisors.whatsapp.redirect', ['asesor' => $asesor]).'?text=Hola%20Camila&utm_source=google&plant_id=10');

        $response
            ->assertOk()
            ->assertSee('googletagmanager.com/gtm.js', false)
            ->assertSee('wa_link', false)
            ->assertSee('Camila Diaz', false)
            ->assertSee('https://wa.me/56987654321?text=Hola%20Camila', false)
            ->assertSee('google', false);
    }

    public function test_it_returns_not_found_for_inactive_or_unconfigured_advisor(): void
    {
        $inactiveAdvisor = Asesor::factory()->create([
            'whatsapp_owner' => '+56 9 8765 4321',
            'is_active' => false,
        ]);

        $withoutWhatsapp = Asesor::factory()->create([
            'whatsapp_owner' => null,
            'is_active' => true,
        ]);

        $this->get(route('advisors.whatsapp.redirect', ['asesor' => $inactiveAdvisor]))
            ->assertNotFound();

        $this->get(route('advisors.whatsapp.redirect', ['asesor' => $withoutWhatsapp]))
            ->assertNotFound();
    }
}
