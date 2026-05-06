<?php

namespace App\Filament\Resources\Brokers\BrokerBenefits\Pages;

use App\Filament\Resources\Brokers\BrokerBenefits\BrokerBenefitResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBrokerBenefit extends EditRecord
{
    protected static string $resource = BrokerBenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
