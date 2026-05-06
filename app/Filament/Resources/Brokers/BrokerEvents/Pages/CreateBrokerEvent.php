<?php

namespace App\Filament\Resources\Brokers\BrokerEvents\Pages;

use App\Filament\Resources\Brokers\BrokerEvents\BrokerEventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBrokerEvent extends CreateRecord
{
    protected static string $resource = BrokerEventResource::class;
}
