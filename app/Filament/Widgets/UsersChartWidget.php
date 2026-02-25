<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class UsersChartWidget extends ChartWidget
{
    protected ?string $heading = 'Usuarios Registrados por Mes';

    protected static ?int $sort = 12;

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
            return User::whereYear('created_at', Carbon::parse($month['month'])->year)
                ->whereMonth('created_at', Carbon::parse($month['month'])->month)
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Usuarios',
                    'data' => $data->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
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
