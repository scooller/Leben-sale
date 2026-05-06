<?php

namespace App\Filament\Resources\SoqlQueryRuns\Pages;

use App\Filament\Resources\SoqlQueryRuns\SoqlQueryRunResource;
use App\Models\SoqlQueryRun;
use App\Services\Salesforce\SalesforceService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class ListSoqlQueryRuns extends ListRecords
{
    protected static string $resource = SoqlQueryRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('execute_soql')
                ->label('Ejecutar SOQL')
                ->icon('heroicon-o-play')
                ->color('primary')
                ->modalHeading('Ejecutar consulta SOQL')
                ->modalDescription('La consulta debe comenzar con SELECT e incluir LIMIT.<br>Ejemplo: SELECT FIELDS(ALL) FROM Product2 LIMIT 3')
                ->modalSubmitActionLabel('Ejecutar')
                ->form([
                    Textarea::make('soql')
                        ->label('Consulta SOQL')
                        ->rows(10)
                        ->required()
                        ->maxLength(8000)
                        ->placeholder('SELECT Id, Name FROM Lead LIMIT 10')
                        ->rules([
                            'required',
                            'string',
                            'max:8000',
                            'regex:/^\s*select\b/i',
                            'regex:/\blimit\s+[1-9][0-9]*\b/i',
                        ])
                        ->validationMessages([
                            'regex' => 'La consulta debe comenzar con SELECT e incluir LIMIT mayor a 0.',
                        ]),
                ])
                ->action(function (array $data): void {
                    $soql = (string) ($data['soql'] ?? '');
                    $startedAt = microtime(true);

                    try {
                        $response = app(SalesforceService::class)->runSoqlWithoutCache($soql);
                        $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
                        $records = Arr::wrap($response['records'] ?? []);

                        SoqlQueryRun::query()->create([
                            'user_id' => Auth::id(),
                            'soql' => $soql,
                            'status' => 'success',
                            'records_count' => count($records),
                            'duration_ms' => $durationMs,
                            'limit_value' => (int) ($response['limit'] ?? 0),
                            'result_preview' => [
                                'total_size' => (int) ($response['total_size'] ?? count($records)),
                                'done' => (bool) ($response['done'] ?? true),
                                'sample_records' => array_slice($records, 0, 3),
                            ],
                            'meta' => [
                                'source' => 'filament_soql_runner',
                            ],
                        ]);

                        Notification::make()
                            ->title('Consulta ejecutada')
                            ->body('Registros recibidos: '.count($records).' | Duracion: '.$durationMs.' ms')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

                        SoqlQueryRun::query()->create([
                            'user_id' => Auth::id(),
                            'soql' => $soql,
                            'status' => 'error',
                            'records_count' => 0,
                            'duration_ms' => $durationMs,
                            'limit_value' => null,
                            'error_message' => str($e->getMessage())->limit(1000)->value(),
                            'meta' => [
                                'source' => 'filament_soql_runner',
                            ],
                        ]);

                        Notification::make()
                            ->title('Error al ejecutar SOQL')
                            ->body($e->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                }),
        ];
    }
}
