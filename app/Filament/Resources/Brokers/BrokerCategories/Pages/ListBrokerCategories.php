<?php

namespace App\Filament\Resources\Brokers\BrokerCategories\Pages;

use App\Filament\Resources\Brokers\BrokerCategories\BrokerCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBrokerCategories extends ListRecords
{
    protected static string $resource = BrokerCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
