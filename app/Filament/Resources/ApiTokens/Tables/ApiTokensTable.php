<?php

namespace App\Filament\Resources\ApiTokens\Tables;

use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApiTokensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tokenable.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tokenable.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('authorized_url')
                    ->label('URL Autorizada')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                IconColumn::make('expires_at')
                    ->label('Activo')
                    ->boolean()
                    ->state(fn ($record): bool => blank($record->expires_at) || $record->expires_at->isFuture()),

                TextColumn::make('last_used_at')
                    ->label('Último Uso')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Nunca')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Expira')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Sin expiración')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->label('Revocar')
                    ->modalHeading('Revocar token API')
                    ->modalDescription('Esta acción invalida el token y no se puede deshacer.')
                    ->successNotificationTitle('Token revocado correctamente'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
