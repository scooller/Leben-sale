<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reservations;

use App\Filament\Resources\Reservations\Pages\ListPlantReservations;
use App\Filament\Resources\Reservations\Pages\ViewPlantReservation;
use App\Filament\Resources\Reservations\Schemas\PlantReservationInfolist;
use App\Filament\Resources\Reservations\Tables\PlantReservationsTable;
use App\Models\PlantReservation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PlantReservationResource extends Resource
{
    protected static ?string $model = PlantReservation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Reservas';

    protected static ?string $modelLabel = 'Reserva';

    protected static ?string $pluralModelLabel = 'Reservas';

    protected static string|UnitEnum|null $navigationGroup = 'Comercio';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return (string) PlantReservation::active()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = PlantReservation::active()->count();

        return $count > 0 ? 'warning' : 'gray';
    }

    public static function table(Table $table): Table
    {
        return PlantReservationsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PlantReservationInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlantReservations::route('/'),
            'view' => ViewPlantReservation::route('/{record}'),
        ];
    }
}
