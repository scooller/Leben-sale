<?php

namespace Tests\Unit\Filament\Widgets;

use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Filament\Widgets\PaymentGatewayChartWidget;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentGatewayChartWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_empty_dataset_without_errors_when_no_payments_exist(): void
    {
        $widget = new class extends PaymentGatewayChartWidget
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

    public function test_it_groups_payments_by_gateway_type(): void
    {
        $user = User::factory()->create();

        Payment::query()->create([
            'user_id' => $user->id,
            'gateway' => PaymentGateway::MANUAL->value,
            'gateway_tx_id' => 'tx-manual-1',
            'amount' => 10000,
            'currency' => 'CLP',
            'status' => PaymentStatus::PENDING_APPROVAL->value,
        ]);

        Payment::query()->create([
            'user_id' => $user->id,
            'gateway' => PaymentGateway::TRANSBANK->value,
            'gateway_tx_id' => 'tx-transbank-1',
            'amount' => 12000,
            'currency' => 'CLP',
            'status' => PaymentStatus::COMPLETED->value,
        ]);

        Payment::query()->create([
            'user_id' => $user->id,
            'gateway' => PaymentGateway::TRANSBANK->value,
            'gateway_tx_id' => 'tx-transbank-2',
            'amount' => 13000,
            'currency' => 'CLP',
            'status' => PaymentStatus::COMPLETED->value,
        ]);

        $widget = new class extends PaymentGatewayChartWidget
        {
            public function exposeData(): array
            {
                return $this->getData();
            }
        };

        $data = $widget->exposeData();

        $this->assertSame([2, 1], $data['datasets'][0]['data']);
        $this->assertSame(['Transbank (Webpay)', 'Pago Manual'], $data['labels']);
    }
}
