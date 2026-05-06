<?php

namespace App\Filament\Resources\Brokers\BrokerBenefits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BrokerBenefitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('section')
                    ->label('Sección')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Beneficio')
                    ->searchable(),

                TextColumn::make('categories.name')
                    ->label('Categorías')
                    ->badge()
                    ->separator(',')
                    ->placeholder('Sin categorías'),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('section')
                    ->label('Sección')
                    ->options([
                        'Comunicación' => 'Comunicación',
                        'Capacitación' => 'Capacitación',
                        'Negocio' => 'Negocio',
                        'Visitas' => 'Visitas',
                        'Pagos' => 'Pagos',
                        'Beneficios Adicionales' => 'Beneficios Adicionales',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
