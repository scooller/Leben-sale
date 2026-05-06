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
                TextColumn::make('category.name')
                    ->label('Categoria')
                    ->badge()
                    ->sortable(),

                TextColumn::make('section')
                    ->label('Seccion')
                    ->badge(),

                TextColumn::make('title')
                    ->label('Beneficio')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn(string $state): string => $state === 'included' ? 'Incluido' : 'No aplica')
                    ->badge(),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('broker_category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name'),

                SelectFilter::make('section')
                    ->label('Seccion')
                    ->options([
                        'comunicacion' => 'Comunicacion',
                        'capacitacion' => 'Capacitacion',
                        'negocio' => 'Negocio',
                        'visitas' => 'Visitas',
                        'pagos' => 'Pagos',
                        'beneficios' => 'Beneficios adicionales',
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
