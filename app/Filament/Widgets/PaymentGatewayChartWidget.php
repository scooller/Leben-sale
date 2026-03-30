<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentGateway;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class PaymentGatewayChartWidget extends ChartWidget
{
    protected ?string $heading = 'Pagos por Tipo de Pago';

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $countsByGateway = Payment::query()
            ->selectRaw('gateway, COUNT(*) as total')
            ->groupBy('gateway')
            ->pluck('total', 'gateway')
            ->map(fn (mixed $total): int => (int) $total)
            ->toArray();

        $labels = [];
        $data = [];
        $colors = [];

        foreach (PaymentGateway::toSelectArray() as $gatewayValue => $gatewayLabel) {
            $count = $countsByGateway[$gatewayValue] ?? 0;

            if ($count === 0) {
                continue;
            }

            $labels[] = $gatewayLabel;
            $data[] = $count;
            $colors[] = $this->gatewayColor((string) $gatewayValue);
            unset($countsByGateway[$gatewayValue]);
        }

        foreach ($countsByGateway as $gatewayValue => $count) {
            if ($count === 0) {
                continue;
            }

            $labels[] = ucfirst((string) $gatewayValue);
            $data[] = (int) $count;
            $colors[] = $this->gatewayColor((string) $gatewayValue);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pagos',
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    private function gatewayColor(string $gateway): string
    {
        return match ($gateway) {
            PaymentGateway::MANUAL->value => 'rgba(251, 191, 36, 0.85)',
            PaymentGateway::TRANSBANK->value => 'rgba(59, 130, 246, 0.85)',
            PaymentGateway::MERCADOPAGO->value => 'rgba(16, 185, 129, 0.85)',
            default => 'rgba(156, 163, 175, 0.85)',
        };
    }
}
