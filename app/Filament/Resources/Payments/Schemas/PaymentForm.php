<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Models\Plant;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('project_id')
                    ->label('Proyecto')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn (Set $set): mixed => $set('plant_id', null)),
                Select::make('plant_id')
                    ->label('Planta')
                    ->relationship(
                        name: 'plant',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query, Get $get) => $query
                            ->when(
                                filled($get('project_id')),
                                fn ($filteredQuery) => $filteredQuery->whereHas(
                                    'proyecto',
                                    fn ($projectQuery) => $projectQuery->whereKey($get('project_id')),
                                ),
                            )
                            ->with('proyecto')
                            ->orderBy('name'),
                    )
                    ->getOptionLabelFromRecordUsing(fn (Plant $record): string => $record->proyecto?->name
                        ? $record->name.' - '.$record->proyecto->name
                        : (string) $record->name)
                    ->searchable()
                    ->preload()
                    ->disabled(fn (Get $get): bool => blank($get('project_id')))
                    ->helperText('Selecciona primero un proyecto para filtrar sus plantas.')
                    ->live(),
                Select::make('gateway')
                    ->options(PaymentGateway::toSelectArray())
                    ->searchable()
                    ->required(),
                TextInput::make('gateway_tx_id'),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('CLP'),
                Select::make('status')
                    ->options(PaymentStatus::toSelectArray())
                    ->searchable()
                    ->required()
                    ->default(PaymentStatus::PENDING->value),
                Textarea::make('metadata')
                    ->columnSpanFull(),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
