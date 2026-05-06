<?php

namespace App\Filament\Resources\Brokers\BrokerEvents;

use App\Filament\Resources\Brokers\BrokerEvents\Pages\CreateBrokerEvent;
use App\Filament\Resources\Brokers\BrokerEvents\Pages\EditBrokerEvent;
use App\Filament\Resources\Brokers\BrokerEvents\Pages\ListBrokerEvents;
use App\Filament\Resources\Brokers\BrokerEvents\Schemas\BrokerEventForm;
use App\Filament\Resources\Brokers\BrokerEvents\Tables\BrokerEventsTable;
use App\Models\BrokerEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BrokerEventResource extends Resource
{
    protected static ?string $model = BrokerEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Eventos';

    protected static string|UnitEnum|null $navigationGroup = 'Brokers';

    protected static ?int $navigationSort = 5;

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
        return BrokerEventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BrokerEventsTable::configure($table);
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
            'index' => ListBrokerEvents::route('/'),
            'create' => CreateBrokerEvent::route('/create'),
            'edit' => EditBrokerEvent::route('/{record}/edit'),
        ];
    }
}
