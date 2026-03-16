<?php

namespace Tests\Unit\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Filament\Widgets\PaymentStatusChartWidget;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentStatusChartWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_empty_dataset_without_errors_when_no_payments_exist(): void
    {
        $widget = new class extends PaymentStatusChartWidget
        {
            public function exposeData(): array
            {
                return $this->getData();
            }
        };

        $data = $widget->exposeData();

        $this->assertSame([], $data['datasets'][0]['data']);
        $this->assertSame([], $data['labels']);
    }

    public function test_it_includes_only_statuses_with_payments(): void
    {
        $user = User::factory()->create();

        Payment::query()->create([
            'user_id' => $user->id,
            'gateway' => 'transbank',
            'gateway_tx_id' => 'tx-pending-1',
            'amount' => 10000,
            'currency' => 'CLP',
            'status' => PaymentStatus::PENDING->value,
        ]);

        Payment::query()->create([
            'user_id' => $user->id,
            'gateway' => 'mercadopago',
            'gateway_tx_id' => 'tx-completed-1',
            'amount' => 20000,
            'currency' => 'CLP',
            'status' => PaymentStatus::COMPLETED->value,
        ]);

        $widget = new class extends PaymentStatusChartWidget
        {
            public function exposeData(): array
            {
                return $this->getData();
            }
        };

        $data = $widget->exposeData();

        $this->assertSame([1, 1], $data['datasets'][0]['data']);
        $this->assertSame(['Pendiente', 'Completado'], $data['labels']);
    }
}
