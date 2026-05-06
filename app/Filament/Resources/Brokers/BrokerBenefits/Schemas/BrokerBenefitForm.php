<?php

namespace App\Filament\Resources\Brokers\BrokerBenefits\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrokerBenefitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Beneficio')
                    ->schema([
                        Select::make('broker_category_id')
                            ->label('Categoria')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('section')
                            ->label('Seccion')
                            ->options([
                                'comunicacion' => 'Comunicacion',
                                'capacitacion' => 'Capacitacion',
                                'negocio' => 'Negocio',
                                'visitas' => 'Visitas',
                                'pagos' => 'Pagos',
                                'beneficios' => 'Beneficios adicionales',
                            ])
                            ->required(),

                        TextInput::make('title')
                            ->label('Titulo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Descripcion')
                            ->rows(2)
                            ->columnSpanFull(),

                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'included' => 'Incluido',
                                'not_applicable' => 'No aplica',
                            ])
                            ->default('included')
                            ->required(),

                        TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
