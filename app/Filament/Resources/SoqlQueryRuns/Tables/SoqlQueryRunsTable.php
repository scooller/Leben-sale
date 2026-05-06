<?php

namespace App\Filament\Resources\SoqlQueryRuns\Tables;

use App\Models\SoqlQueryRun;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
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
                    ->color(fn (string $state): string => match ($state) {
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
                    ->formatStateUsing(fn (?string $state): ?string => filled($state) ? Str::limit($state, 80) : null)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
            ])
            ->recordActions([
                Action::make('ver_resultado')
                    ->label('Ver Resultado')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Resultado SOQL')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(function (SoqlQueryRun $record): View {
                        return view('filament.soql-query-runs.result-modal', [
                            'record' => $record,
                        ]);
                    }),
            ])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }
}
