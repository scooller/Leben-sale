<?php

namespace App\Filament\Resources\Plants\Tables;

use App\Models\Plant;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PlantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('proyecto.name')
                    ->label('Proyecto')
                    ->searchable()
                    ->sortable(),
                // TextColumn::make('product_code')
                //     ->label('Código')
                //     ->searchable()
                //     ->sortable(),
                TextColumn::make('programa')
                    ->label('Programa')
                    ->searchable()
                    ->sortable(),
                // TextColumn::make('programa2')
                //     ->label('Programa 2')
                //     ->searchable()
                //     ->sortable(),
                TextColumn::make('piso')
                    ->label('Piso')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('orientacion')
                    ->label('Orientación')
                    ->searchable(),
                TextColumn::make('precio_base')
                    ->label('Precio Base')
                    ->formatStateUsing(fn ($state) => $state ? 'UF '.number_format($state, 0, ',', '.') : '-')
                    ->sortable(),
                TextColumn::make('precio_lista')
                    ->label('Precio Lista')
                    ->formatStateUsing(fn ($state) => $state ? 'UF '.number_format($state, 0, ',', '.') : '-')
                    ->sortable(),
                // TextColumn::make('superficie_util')
                //     ->label('Sup. Útil')
                //     ->suffix(' m²')
                //     ->sortable(),
                // TextColumn::make('superficie_vendible')
                //     ->label('Sup. Vendible')
                //     ->suffix(' m²')
                //     ->sortable(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                TextColumn::make('last_synced_at')
                    ->label('Sincronizado')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('activos')
                    ->query(fn ($query) => $query->where('is_active', true))
                    ->toggle(),
                SelectFilter::make('proyecto')
                    ->label('Proyecto')
                    ->relationship('proyecto', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('programa')
                    ->query(fn ($query, string $value) => $query->where('programa', $value)),
                Filter::make('piso')
                    ->query(fn ($query, string $value) => $query->where('piso', $value)),
            ])
            ->recordActions([
                Action::make('toggleActive')
                    ->label(fn (Plant $record): string => $record->is_active ? 'Desactivar' : 'Activar')
                    ->icon(fn (Plant $record): string => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Plant $record): string => $record->is_active ? 'warning' : 'success')
                    ->action(fn (Plant $record): bool => $record->update([
                        'is_active' => ! $record->is_active,
                    ]))
                    ->successNotificationTitle('Estado actualizado'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('deactivateSelected')
                        ->label('Desactivar seleccionadas')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update([
                                'is_active' => false,
                            ]);
                        })
                        ->successNotificationTitle('Plantas desactivadas'),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
