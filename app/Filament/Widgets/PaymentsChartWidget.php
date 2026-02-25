<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PaymentsChartWidget extends ChartWidget
{
    protected ?string $heading = 'Pagos por Mes';

    protected static ?int $sort = 2;

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        // Últimos 12 meses
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push([
                'month' => $date->format('Y-m'),
                'label' => $date->translatedFormat('M Y'),
            ]);
        }

        $data = $months->map(function ($month) {
            return Payment::whereYear('created_at', Carbon::parse($month['month'])->year)
                ->whereMonth('created_at', Carbon::parse($month['month'])->month)
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Pagos',
                    'data' => $data->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
            ],
            'labels' => $months->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
