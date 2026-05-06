<?php

namespace App\Filament\Resources\Brokers\BrokerBenefits;

use App\Filament\Resources\Brokers\BrokerBenefits\Pages\CreateBrokerBenefit;
use App\Filament\Resources\Brokers\BrokerBenefits\Pages\EditBrokerBenefit;
use App\Filament\Resources\Brokers\BrokerBenefits\Pages\ListBrokerBenefits;
use App\Filament\Resources\Brokers\BrokerBenefits\Schemas\BrokerBenefitForm;
use App\Filament\Resources\Brokers\BrokerBenefits\Tables\BrokerBenefitsTable;
use App\Models\BrokerBenefit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BrokerBenefitResource extends Resource
{
    protected static ?string $model = BrokerBenefit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static ?string $navigationLabel = 'Beneficios';

    protected static string|UnitEnum|null $navigationGroup = 'Brokers';

    protected static ?int $navigationSort = 3;

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
        return BrokerBenefitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BrokerBenefitsTable::configure($table);
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
            'index' => ListBrokerBenefits::route('/'),
            'create' => CreateBrokerBenefit::route('/create'),
            'edit' => EditBrokerBenefit::route('/{record}/edit'),
        ];
    }
}
