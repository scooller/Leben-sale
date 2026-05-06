<?php

namespace App\Filament\Resources\Brokers\BrokerGalleries\Pages;

use App\Filament\Resources\Brokers\BrokerGalleries\BrokerGalleryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBrokerGalleries extends ListRecords
{
    protected static string $resource = BrokerGalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
