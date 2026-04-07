<?php

namespace App\Filament\Resources\Asesores\Pages;

use App\Filament\Resources\Asesores\AsesorResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateAsesor extends CreateRecord
{
    protected static string $resource = AsesorResource::class;

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }
}
