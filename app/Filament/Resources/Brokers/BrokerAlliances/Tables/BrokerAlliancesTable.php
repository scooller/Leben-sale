<?php

namespace App\Filament\Resources\Brokers\BrokerAlliances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BrokerAlliancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image_id')
                    ->label('Marca')
                    ->getStateUsing(fn($record): ?string => $record->imageMedia?->url),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),

                TextColumn::make('broker.resolved_name')
                    ->label('Broker')
                    ->searchable(),

                TextColumn::make('url')
                    ->label('Link')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('broker_id')
                    ->label('Broker')
                    ->relationship('broker', 'display_name'),
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
