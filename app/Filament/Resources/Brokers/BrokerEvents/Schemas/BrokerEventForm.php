<?php

namespace App\Filament\Resources\Brokers\BrokerEvents\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrokerEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Evento')
                    ->schema([
                        Select::make('broker_id')
                            ->label('Broker')
                            ->relationship('broker', 'display_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('title')
                            ->label('Titulo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Descripcion')
                            ->rows(3)
                            ->columnSpanFull(),

                        DateTimePicker::make('starts_at')
                            ->label('Inicio')
                            ->required(),

                        DateTimePicker::make('ends_at')
                            ->label('Fin'),

                        TextInput::make('location')
                            ->label('Ubicacion')
                            ->maxLength(255),

                        CuratorPicker::make('image_id')
                            ->label('Imagen'),

                        Toggle::make('is_published')
                            ->label('Publicado')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
