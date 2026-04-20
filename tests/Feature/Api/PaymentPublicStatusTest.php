<?php

namespace Tests\Feature\Api;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentPublicStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_status_returns_payment_data_when_token_is_valid(): void
    {
        $statusToken = (string) Str::uuid();

        $payment = Payment::query()->create([
            'gateway' => 'transbank',
            'gateway_tx_id' => 'OP-STATUS-001',
            'amount' => 50000,
            'currency' => 'CLP',
            'status' => PaymentStatus::COMPLETED,
            'metadata' => [
                'public_status_token' => $statusToken,
            ],
        ]);

        $response = $this->getJson("/api/v1/payments/public-status/{$payment->id}?token={$statusToken}");

        $response
            ->assertOk()
            ->assertJsonPath('id', $payment->id)
            ->assertJsonPath('gateway', 'transbank')
            ->assertJsonPath('status', 'completed')
            ->assertJsonPath('status_label', 'Completado');
    }

    public function test_public_status_returns_not_found_when_token_is_invalid(): void
    {
        $payment = Payment::query()->create([
            'gateway' => 'transbank',
            'gateway_tx_id' => 'OP-STATUS-002',
            'amount' => 32000,
            'currency' => 'CLP',
            'status' => PaymentStatus::PENDING,
            'metadata' => [
                'public_status_token' => (string) Str::uuid(),
            ],
        ]);

        $response = $this->getJson("/api/v1/payments/public-status/{$payment->id}?token=invalid-token");

        $response
            ->assertStatus(404)
            ->assertJson([
                'message' => 'Token de estado inválido.',
            ]);
    }
}
