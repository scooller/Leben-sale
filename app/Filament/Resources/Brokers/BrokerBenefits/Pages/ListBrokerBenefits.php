<?php

namespace App\Filament\Resources\Brokers\BrokerBenefits\Pages;

use App\Filament\Resources\Brokers\BrokerBenefits\BrokerBenefitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBrokerBenefits extends ListRecords
{
    protected static string $resource = BrokerBenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
