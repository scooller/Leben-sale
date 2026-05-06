<?php

namespace App\Filament\Resources\SoqlQueryRuns\Tables;

use App\Models\SoqlQueryRun;
use App\Services\Salesforce\SalesforceService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SoqlQueryRunsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d-m-Y H:i:s')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'success' => 'success',
                        default => 'danger',
                    }),
                TextColumn::make('records_count')
                    ->label('Registros')
                    ->sortable(),
                TextColumn::make('duration_ms')
                    ->label('Duracion ms')
                    ->sortable(),
                TextColumn::make('limit_value')
                    ->label('LIMIT')
                    ->sortable(),
                TextColumn::make('soql')
                    ->label('Consulta')
                    ->wrap()
                    ->limit(90)
                    ->searchable(),
                TextColumn::make('error_message')
                    ->label('Error')
                    ->formatStateUsing(fn(?string $state): ?string => filled($state) ? Str::limit($state, 80) : null)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                Action::make('reutilizar_consulta')
                    ->label('Reutilizar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->modalHeading('Reutilizar consulta SOQL')
                    ->modalDescription('Puedes ajustar la consulta antes de ejecutar. Debe incluir SELECT y LIMIT.')
                    ->modalSubmitActionLabel('Ejecutar')
                    ->form([
                        Textarea::make('soql')
                            ->label('Consulta SOQL')
                            ->rows(10)
                            ->required()
                            ->maxLength(8000)
                            ->default(fn(SoqlQueryRun $record): string => $record->soql)
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
                        self::executeAndStoreSoqlRun((string) ($data['soql'] ?? ''));
                    }),
                Action::make('ver_resultado')
                    ->label('Ver Resultado')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Resultado SOQL')
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(function (SoqlQueryRun $record): View {
                        return view('filament.soql-query-runs.result-modal', [
                            'record' => $record,
                        ]);
                    }),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionadas')
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    private static function executeAndStoreSoqlRun(string $soql): void
    {
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
                    'source' => 'filament_soql_runner_reuse',
                ],
            ]);

            Notification::make()
                ->title('Consulta reutilizada y ejecutada')
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
                    'source' => 'filament_soql_runner_reuse',
                ],
            ]);

            Notification::make()
                ->title('Error al reutilizar SOQL')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }
}
