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
        ]);

        $payload = SiteSetting::forFrontend();

        $this->assertArrayHasKey('brand_color', $payload);
        $this->assertSame('#112233', $payload['brand_color']);
    }
}
