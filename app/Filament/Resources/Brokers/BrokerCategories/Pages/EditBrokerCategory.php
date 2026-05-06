<?php

namespace App\Filament\Resources\Brokers\BrokerCategories\Pages;

use App\Filament\Resources\Brokers\BrokerCategories\BrokerCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBrokerCategory extends EditRecord
{
    protected static string $resource = BrokerCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
