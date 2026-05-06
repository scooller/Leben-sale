<?php

namespace App\Filament\Resources\Brokers\BrokerEvents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BrokerEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('starts_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Titulo')
                    ->searchable(),

                TextColumn::make('broker.resolved_name')
                    ->label('Broker')
                    ->searchable(),

                TextColumn::make('starts_at')
                    ->label('Inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('Fin')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(),

                TextColumn::make('location')
                    ->label('Ubicacion')
                    ->toggleable(),

                IconColumn::make('is_published')
                    ->label('Publicado')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('broker_id')
                    ->label('Broker')
                    ->relationship('broker', 'display_name'),

                SelectFilter::make('is_published')
                    ->label('Publicacion')
                    ->options([
                        1 => 'Publicado',
                        0 => 'No publicado',
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
