<?php

namespace App\Filament\Resources\Brokers\BrokerEvents\Pages;

use App\Filament\Resources\Brokers\BrokerEvents\BrokerEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBrokerEvents extends ListRecords
{
    protected static string $resource = BrokerEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
