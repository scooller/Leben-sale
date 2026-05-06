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
                        Select::make('section')
                            ->label('Sección')
                            ->options([
                                'Comunicación' => 'Comunicación',
                                'Capacitación' => 'Capacitación',
                                'Negocio' => 'Negocio',
                                'Visitas' => 'Visitas',
                                'Pagos' => 'Pagos',
                                'Beneficios Adicionales' => 'Beneficios Adicionales',
                            ])
                            ->required(),

                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(2)
                            ->columnSpanFull(),

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
