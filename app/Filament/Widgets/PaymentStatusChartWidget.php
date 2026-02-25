<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class PaymentStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'Pagos por Estado';

    protected static ?int $sort = 3;

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $statuses = [
            PaymentStatus::PENDING,
            PaymentStatus::PROCESSING,
            PaymentStatus::AUTHORIZED,
            PaymentStatus::COMPLETED,
            PaymentStatus::FAILED,
            PaymentStatus::CANCELLED,
            PaymentStatus::REFUNDED,
            PaymentStatus::PARTIALLY_REFUNDED,
            PaymentStatus::EXPIRED,
            PaymentStatus::PENDING_APPROVAL,
        ];

        $data = [];
        $labels = [];
        $colors = [];

        $colorMap = [
            'gray' => 'rgba(156, 163, 175, 0.8)',
            'warning' => 'rgba(251, 191, 36, 0.8)',
            'info' => 'rgba(59, 130, 246, 0.8)',
            'success' => 'rgba(34, 197, 94, 0.8)',
            'danger' => 'rgba(239, 68, 68, 0.8)',
        ];

        foreach ($statuses as $status) {
            $count = Payment::where('status', $status->value)->count();
            if ($count > 0) {
                $data[] = $count;
                $labels[] = $status->label();
                $colors[] = $colorMap[$status->color()] ?? 'rgba(156, 163, 175, 0.8)';
            }
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
}
