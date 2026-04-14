<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentResultTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_page_emits_checkout_success_tracking_event(): void
    {
        SiteSetting::current()->update([
            'tag_manager_id' => 'GTM-TEST123',
        ]);

        $response = $this->get('/payments/success/42');

        $response->assertOk();
        $response->assertSee('googletagmanager.com/gtm.js?id=GTM-TEST123', false);
        $response->assertSee('checkout_success', false);
        $response->assertSee('completed', false);
        $response->assertSee('42', false);
    }

    public function test_pending_and_failed_pages_emit_tracking_events(): void
    {
        SiteSetting::current()->update([
            'tag_manager_id' => 'GTM-TEST123',
        ]);

        $pendingResponse = $this->get('/payments/pending/77');
        $failedResponse = $this->get('/payments/failed/88');

        $pendingResponse->assertOk();
        $pendingResponse->assertSee('checkout_pending', false);
        $pendingResponse->assertSee('pending', false);
        $pendingResponse->assertSee('77', false);

        $failedResponse->assertOk();
        $failedResponse->assertSee('checkout_failed', false);
        $failedResponse->assertSee('failed', false);
        $failedResponse->assertSee('88', false);
    }
}
