<?php

namespace App\Filament\Resources\SoqlQueryRuns\Pages;

use App\Filament\Resources\SoqlQueryRuns\SoqlQueryRunResource;
use App\Models\SoqlQueryRun;
use App\Services\Salesforce\SalesforceService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                ->modalDescription('Ejemplo: SELECT FIELDS(ALL) FROM Product2 — El LIMIT se agrega automáticamente.')
                ->modalSubmitActionLabel('Ejecutar')
                ->form([
                    Textarea::make('soql')
                        ->label('Consulta SOQL')
                        ->rows(8)
                        ->required()
                        ->maxLength(8000)
                        ->placeholder('SELECT Id, Name, Email FROM Lead')
                        ->rules([
                            'required',
                            'string',
                            'max:8000',
                            'regex:/^\s*select\b/i',
                        ])
                        ->validationMessages([
                            'regex' => 'La consulta debe comenzar con SELECT.',
                        ]),
                    TextInput::make('limit')
                        ->label('LIMIT')
                        ->numeric()
                        ->default(10)
                        ->minValue(1)
                        ->maxValue(2000)
                        ->required()
                        ->suffix('registros')
                        ->rules(['required', 'integer', 'min:1', 'max:2000']),
                ])
                ->action(function (array $data): void {
                    $limitValue = max(1, (int) ($data['limit'] ?? 10));
                    $inputSoql = trim((string) ($data['soql'] ?? ''));
                    $soqlWithoutLimit = trim(preg_replace('/\s+limit\s+[0-9]+\s*$/i', '', $inputSoql));
                    $isGlobalAggregate = preg_match('/^\s*select\s+(count\s*\(|sum\s*\(|avg\s*\(|min\s*\(|max\s*\()/i', $soqlWithoutLimit) === 1
                        && preg_match('/\bgroup\s+by\b/i', $soqlWithoutLimit) !== 1;
                    $soql = $isGlobalAggregate ? $soqlWithoutLimit : $soqlWithoutLimit . ' LIMIT ' . $limitValue;
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
                            'limit_value' => $response['limit'] ?? null,
                            'result_preview' => [
                                'total_size' => (int) ($response['total_size'] ?? count($records)),
                                'done' => (bool) ($response['done'] ?? true),
                                'sample_records' => $records,
                            ],
                            'meta' => [
                                'source' => 'filament_soql_runner',
                            ],
                        ]);

                        Notification::make()
                            ->title('Consulta ejecutada')
                            ->body('Registros recibidos: ' . count($records) . ' | Duracion: ' . $durationMs . ' ms')
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
