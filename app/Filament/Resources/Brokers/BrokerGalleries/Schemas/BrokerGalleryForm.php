<?php

namespace App\Filament\Resources\Brokers\BrokerGalleries\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrokerGalleryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Galeria')
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
                            ->maxLength(255),

                        TextInput::make('year')
                            ->label('Ano')
                            ->numeric()
                            ->required()
                            ->minValue(2020)
                            ->maxValue(2100),

                        Select::make('month')
                            ->label('Mes')
                            ->required()
                            ->options([
                                1 => 'Enero',
                                2 => 'Febrero',
                                3 => 'Marzo',
                                4 => 'Abril',
                                5 => 'Mayo',
                                6 => 'Junio',
                                7 => 'Julio',
                                8 => 'Agosto',
                                9 => 'Septiembre',
                                10 => 'Octubre',
                                11 => 'Noviembre',
                                12 => 'Diciembre',
                            ]),

                        TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_published')
                            ->label('Publicada')
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make('Imagenes')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                CuratorPicker::make('image_id')
                                    ->label('Imagen')
                                    ->required(),
                                TextInput::make('caption')
                                    ->label('Caption')
                                    ->maxLength(255),
                                TextInput::make('sort_order')
                                    ->label('Orden')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                                Toggle::make('is_active')
                                    ->label('Activa')
                                    ->default(true),
                            ])
                            ->columns(2)
                            ->addActionLabel('Agregar imagen')
                            ->reorderableWithButtons()
                            ->collapsible(),
                    ]),
            ]);
    }
}
