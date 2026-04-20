<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reservations\Pages;

use App\Enums\ReservationStatus;
use App\Filament\Resources\Reservations\PlantReservationResource;
use App\Services\PlantReservationService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPlantReservation extends ViewRecord
{
    protected static string $resource = PlantReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('release')
                ->label('Liberar reserva')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Liberar reserva')
                ->modalDescription('Esta accion liberara la reserva activa de esta planta.')
                ->visible(fn (): bool => $this->record->status === ReservationStatus::ACTIVE)
                ->action(function (): void {
                    $released = app(PlantReservationService::class)
                        ->releaseById($this->record->id, 'admin', 'Released from reservation detail');

                    $notification = Notification::make()
                        ->title($released ? 'Reserva liberada' : 'No se pudo liberar la reserva');

                    if ($released) {
                        $notification->success();
                    } else {
                        $notification->warning();
                    }

                    $notification->send();

                    $this->record->refresh();
                }),
            DeleteAction::make()
                ->label('Eliminar reserva')
                ->modalHeading('Eliminar reserva')
                ->modalDescription('Esta accion eliminara definitivamente este registro de reserva.'),
        ];
    }
}
