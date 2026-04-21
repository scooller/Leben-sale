<?php

namespace App\Filament\Widgets;

use App\Models\ShortLinkVisit;
use Filament\Widgets\ChartWidget;

class ShortLinksVisitsChartWidget extends ChartWidget
{
    protected ?string $heading = 'Visitas a Links Cortos - Últimos 30 Días';

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = [
        'md' => 2,
    ];

    protected function getData(): array
    {
        // Últimos 30 días
        $days = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days->push([
                'date' => $date->format('Y-m-d'),
                'label' => $date->translatedFormat('d M'),
            ]);
        }

        $data = $days->map(function ($day) {
            return ShortLinkVisit::whereDate('visited_at', $day['date'])
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Visitas',
                    'data' => $data->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $days->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
