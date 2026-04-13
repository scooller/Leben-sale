<?php

namespace App\Filament\Resources\ContactSubmissions\ContactSubmissions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('submitted_at')
                    ->label('Enviado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
