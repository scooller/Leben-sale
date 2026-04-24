<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\SiteSetting;
use App\Models\User;
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

    public function test_success_page_does_not_emit_checkout_success_for_manual_payments(): void
    {
        SiteSetting::current()->update([
            'tag_manager_id' => 'GTM-TEST123',
        ]);

        $user = User::factory()->create();

        $payment = Payment::query()->create([
            'user_id' => $user->id,
            'gateway' => 'manual',
            'gateway_tx_id' => 'MAN-TRACK-001',
            'amount' => 15000,
            'currency' => 'CLP',
            'status' => PaymentStatus::PENDING_APPROVAL,
            'metadata' => [],
        ]);

        $response = $this->get('/payments/success/'.$payment->id);

        $response->assertOk();
        $response->assertDontSee('checkout_success', false);
        $response->assertSee('Pago Exitoso', false);
    }
}
