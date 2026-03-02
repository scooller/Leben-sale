<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reservations\Tables;

use App\Enums\ReservationStatus;
use App\Services\PlantReservationService;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PlantReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('plant.name')
                    ->label('Planta')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(function (ReservationStatus|string|null $state): string {
                        $status = $state instanceof ReservationStatus ? $state : ($state ? ReservationStatus::from($state) : null);

                        return $status?->color() ?? 'gray';
                    })
                    ->icon(function (ReservationStatus|string|null $state): string {
                        $status = $state instanceof ReservationStatus ? $state : ($state ? ReservationStatus::from($state) : null);

                        return $status?->icon() ?? 'heroicon-o-question-mark-circle';
                    })
                    ->formatStateUsing(function (ReservationStatus|string|null $state): string {
                        $status = $state instanceof ReservationStatus ? $state : ($state ? ReservationStatus::from($state) : null);

                        return $status?->label() ?? '-';
                    })
                    ->searchable(),
                TextColumn::make('expires_at')
                    ->label('Expira')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('released_by')
                    ->label('Liberada por')
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(ReservationStatus::toSelectArray())
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('release')
                    ->label('Liberar')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Liberar Reserva')
                    ->modalDescription('Esta accion liberara la reserva y permitira que otros usuarios puedan reservar esta planta.')
                    ->visible(fn ($record) => $record->status === ReservationStatus::ACTIVE)
                    ->action(function ($record): void {
                        app(PlantReservationService::class)->releaseById($record->id, 'admin', 'Released from admin panel');
                    }),
            ]);
    }
}
