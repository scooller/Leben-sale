<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Jobs\SyncPlantsJob;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PaymentObserverSyncPlantsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_dispatches_sync_plants_job_when_payment_becomes_completed(): void
    {
        Queue::fake();

        $payment = Payment::query()->create([
            'user_id' => User::factory()->create()->id,
            'gateway' => 'transbank',
            'gateway_tx_id' => 'test-completed-1',
            'amount' => 1000,
            'currency' => 'CLP',
            'status' => PaymentStatus::PENDING,
        ]);

        $payment->update([
            'status' => PaymentStatus::COMPLETED,
        ]);

        Queue::assertPushed(SyncPlantsJob::class);
    }

    public function test_it_dispatches_sync_plants_job_when_payment_becomes_authorized(): void
    {
        Queue::fake();

        $payment = Payment::query()->create([
            'user_id' => User::factory()->create()->id,
            'gateway' => 'mercadopago',
            'gateway_tx_id' => 'test-authorized-1',
            'amount' => 2000,
            'currency' => 'CLP',
            'status' => PaymentStatus::PENDING,
        ]);

        $payment->update([
            'status' => PaymentStatus::AUTHORIZED,
        ]);

        Queue::assertPushed(SyncPlantsJob::class);
    }

    public function test_it_does_not_dispatch_sync_plants_job_for_non_completed_status_changes(): void
    {
        Queue::fake();

        $payment = Payment::query()->create([
            'user_id' => User::factory()->create()->id,
            'gateway' => 'transbank',
            'gateway_tx_id' => 'test-failed-1',
            'amount' => 1000,
            'currency' => 'CLP',
            'status' => PaymentStatus::PENDING,
        ]);

        $payment->update([
            'status' => PaymentStatus::FAILED,
        ]);

        Queue::assertNotPushed(SyncPlantsJob::class);
    }
}
