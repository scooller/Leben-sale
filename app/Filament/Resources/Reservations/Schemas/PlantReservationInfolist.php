<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reservations\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PlantReservationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Reserva')
                    ->columns(2)
                    ->components([
                        TextEntry::make('id')
                            ->label('ID'),
                        TextEntry::make('status')
                            ->label('Estado')
                            ->badge(),
                        TextEntry::make('session_token')
                            ->label('Session Token')
                            ->copyable(),
                        TextEntry::make('expires_at')
                            ->label('Expira')
                            ->dateTime(),
                        TextEntry::make('completed_at')
                            ->label('Completada')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('released_at')
                            ->label('Liberada')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('released_by')
                            ->label('Liberada por')
                            ->placeholder('-'),
                    ]),
                Section::make('Relaciones')
                    ->columns(2)
                    ->components([
                        TextEntry::make('plant.name')
                            ->label('Planta')
                            ->placeholder('-'),
                        TextEntry::make('user.name')
                            ->label('Usuario')
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label('Creada')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Actualizada')
                            ->dateTime(),
                    ]),
                Section::make('Metadata')
                    ->components([
                        KeyValueEntry::make('metadata')
                            ->label('Metadata')
                            ->placeholder('Sin metadata'),
                    ]),
            ]);
    }
}
