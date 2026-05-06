<?php

namespace App\Filament\Resources\Brokers\BrokerAlliances\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrokerAllianceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Alianza')
                    ->schema([
                        Select::make('broker_id')
                            ->label('Broker')
                            ->relationship('broker', 'display_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('url')
                            ->label('Link')
                            ->url()
                            ->maxLength(2048),

                        CuratorPicker::make('image_id')
                            ->label('Imagen de marca'),

                        TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->label('Activa')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
