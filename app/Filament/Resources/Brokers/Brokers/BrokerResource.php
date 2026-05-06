<?php

namespace App\Filament\Resources\Brokers\Brokers;

use App\Filament\Resources\Brokers\Brokers\Pages\CreateBroker;
use App\Filament\Resources\Brokers\Brokers\Pages\EditBroker;
use App\Filament\Resources\Brokers\Brokers\Pages\ListBrokers;
use App\Filament\Resources\Brokers\Brokers\Schemas\BrokerForm;
use App\Filament\Resources\Brokers\Brokers\Tables\BrokersTable;
use App\Models\Broker;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BrokerResource extends Resource
{
    protected static ?string $model = Broker::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Brokers';

    protected static string|UnitEnum|null $navigationGroup = 'Brokers';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'display_name';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'blue';
    }

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
        return BrokerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BrokersTable::configure($table);
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
            'index' => ListBrokers::route('/'),
            'create' => CreateBroker::route('/create'),
            'edit' => EditBroker::route('/{record}/edit'),
        ];
    }
}
