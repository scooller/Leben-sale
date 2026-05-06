<?php

namespace App\Filament\Resources\Brokers\BrokerAlliances;

use App\Filament\Resources\Brokers\BrokerAlliances\Pages\CreateBrokerAlliance;
use App\Filament\Resources\Brokers\BrokerAlliances\Pages\EditBrokerAlliance;
use App\Filament\Resources\Brokers\BrokerAlliances\Pages\ListBrokerAlliances;
use App\Filament\Resources\Brokers\BrokerAlliances\Schemas\BrokerAllianceForm;
use App\Filament\Resources\Brokers\BrokerAlliances\Tables\BrokerAlliancesTable;
use App\Models\BrokerAlliance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BrokerAllianceResource extends Resource
{
    protected static ?string $model = BrokerAlliance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $navigationLabel = 'Alianzas';

    protected static string|UnitEnum|null $navigationGroup = 'Brokers';

    protected static ?int $navigationSort = 4;

    public static function shouldRegisterNavigation(): bool
    {
        return (auth()->user()?->isAdmin() ?? false) || (auth()->user()?->isMarketing() ?? false);
    }

    public static function canViewAny(): bool
    {
        return (auth()->user()?->isAdmin() ?? false) || (auth()->user()?->isMarketing() ?? false);
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return BrokerAllianceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BrokerAlliancesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBrokerAlliances::route('/'),
            'create' => CreateBrokerAlliance::route('/create'),
            'edit' => EditBrokerAlliance::route('/{record}/edit'),
        ];
    }
}
