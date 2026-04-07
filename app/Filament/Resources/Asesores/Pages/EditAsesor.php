<?php

namespace App\Filament\Resources\Asesores\Pages;

use App\Filament\Resources\Asesores\AsesorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditAsesor extends EditRecord
{
    protected static string $resource = AsesorResource::class;

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
