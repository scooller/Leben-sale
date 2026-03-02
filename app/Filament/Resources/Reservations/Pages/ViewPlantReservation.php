<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\PlantReservationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPlantReservation extends ViewRecord
{
    protected static string $resource = PlantReservationResource::class;
}
