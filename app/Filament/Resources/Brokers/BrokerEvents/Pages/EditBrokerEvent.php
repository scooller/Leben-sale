<?php

namespace App\Filament\Resources\Brokers\BrokerEvents\Pages;

use App\Filament\Resources\Brokers\BrokerEvents\BrokerEventResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBrokerEvent extends EditRecord
{
    protected static string $resource = BrokerEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
