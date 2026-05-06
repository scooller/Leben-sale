<?php

namespace App\Filament\Resources\Brokers\BrokerGalleries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BrokerGalleriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('year', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Titulo')
                    ->searchable(),

                TextColumn::make('broker.resolved_name')
                    ->label('Broker')
                    ->searchable(),

                TextColumn::make('year')
                    ->label('Ano')
                    ->sortable(),

                TextColumn::make('month')
                    ->label('Mes')
                    ->sortable(),

                TextColumn::make('items_count')
                    ->label('Imagenes')
                    ->counts('items'),

                IconColumn::make('is_published')
                    ->label('Publicada')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('broker_id')
                    ->label('Broker')
                    ->relationship('broker', 'display_name'),
                SelectFilter::make('month')
                    ->label('Mes')
                    ->options([
                        1 => 'Enero',
                        2 => 'Febrero',
                        3 => 'Marzo',
                        4 => 'Abril',
                        5 => 'Mayo',
                        6 => 'Junio',
                        7 => 'Julio',
                        8 => 'Agosto',
                        9 => 'Septiembre',
                        10 => 'Octubre',
                        11 => 'Noviembre',
                        12 => 'Diciembre',
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
