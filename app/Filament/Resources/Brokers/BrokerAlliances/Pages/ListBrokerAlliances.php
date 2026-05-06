<?php

namespace App\Filament\Resources\Brokers\BrokerAlliances\Pages;

use App\Filament\Resources\Brokers\BrokerAlliances\BrokerAllianceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBrokerAlliances extends ListRecords
{
    protected static string $resource = BrokerAllianceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
