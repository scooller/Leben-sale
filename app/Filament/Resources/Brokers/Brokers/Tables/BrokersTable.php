<?php

namespace App\Filament\Resources\Brokers\Brokers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BrokersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('avatar_image_id')
                    ->label('Avatar')
                    ->getStateUsing(fn($record): ?string => $record->avatarImageMedia?->url)
                    ->circular(),

                TextColumn::make('salesforce_id')
                    ->label('SF ID')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('resolved_name')
                    ->label('Nombre')
                    ->searchable(['display_name', 'contact_email']),

                TextColumn::make('category.name')
                    ->label('Categoria')
                    ->badge(),

                TextColumn::make('resolved_phone')
                    ->label('Telefono')
                    ->toggleable(),

                TextColumn::make('resolved_email')
                    ->label('Email')
                    ->toggleable(),

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
                SelectFilter::make('is_active')
                    ->label('Estado')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
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
