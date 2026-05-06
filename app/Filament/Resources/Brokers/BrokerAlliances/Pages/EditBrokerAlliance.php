<?php

namespace App\Filament\Resources\Brokers\BrokerAlliances\Pages;

use App\Filament\Resources\Brokers\BrokerAlliances\BrokerAllianceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBrokerAlliance extends EditRecord
{
    protected static string $resource = BrokerAllianceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
