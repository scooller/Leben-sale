<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingFrontendConfigTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure brand_color is persisted and exposed in the public site configuration payload.
     */
    public function test_for_frontend_includes_brand_color(): void
    {
        SiteSetting::current()->update([
            'brand_color' => '#112233',
            'evento_sale' => true,
            'contact_page_title' => 'Conversemos',
            'contact_page_subtitle' => 'Te ayudamos a elegir tu próxima planta',
            'contact_page_content' => '<p>Contenido administrable de contacto</p>',
            'contact_form_fields' => [
                [
                    'key' => 'name',
                    'label' => 'Nombre',
                    'type' => 'text',
                    'required' => true,
                ],
            ],
            'contact_notification_email' => 'ventas@ileben.cl',
            'footer_menu' => [
                [
                    'label' => 'Bases Legales',
                    'url' => '/bases-legales',
                    'new_tab' => false,
                ],
            ],
            'footer_legal_text' => '<p>Texto legal de prueba</p>',
        ]);

        $payload = SiteSetting::forFrontend();

        $this->assertArrayHasKey('brand_color', $payload);
        $this->assertSame('#112233', $payload['brand_color']);
        $this->assertArrayHasKey('evento_sale', $payload);
        $this->assertTrue($payload['evento_sale']);
        $this->assertArrayHasKey('footer_menu', $payload);
        $this->assertSame('Bases Legales', $payload['footer_menu'][0]['label']);
        $this->assertSame('/bases-legales', $payload['footer_menu'][0]['url']);
        $this->assertFalse($payload['footer_menu'][0]['new_tab']);
        $this->assertArrayHasKey('footer_legal_text', $payload);
        $this->assertSame('<p>Texto legal de prueba</p>', $payload['footer_legal_text']);
        $this->assertArrayHasKey('contact_page', $payload);
        $this->assertSame('Conversemos', $payload['contact_page']['title']);
        $this->assertSame('Te ayudamos a elegir tu próxima planta', $payload['contact_page']['subtitle']);
        $this->assertSame('<p>Contenido administrable de contacto</p>', $payload['contact_page']['content']);
        $this->assertArrayHasKey('form_fields', $payload['contact_page']);
        $this->assertSame('name', $payload['contact_page']['form_fields'][0]['key']);
    }
}
