<?php

namespace App\Filament\Resources\Brokers\BrokerGalleries\Pages;

use App\Filament\Resources\Brokers\BrokerGalleries\BrokerGalleryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBrokerGallery extends EditRecord
{
    protected static string $resource = BrokerGalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
