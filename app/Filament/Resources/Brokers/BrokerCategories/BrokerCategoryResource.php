<?php

namespace App\Filament\Resources\Brokers\BrokerCategories;

use App\Filament\Resources\Brokers\BrokerCategories\Pages\CreateBrokerCategory;
use App\Filament\Resources\Brokers\BrokerCategories\Pages\EditBrokerCategory;
use App\Filament\Resources\Brokers\BrokerCategories\Pages\ListBrokerCategories;
use App\Filament\Resources\Brokers\BrokerCategories\RelationManagers\BenefitsRelationManager;
use App\Filament\Resources\Brokers\BrokerCategories\Schemas\BrokerCategoryForm;
use App\Filament\Resources\Brokers\BrokerCategories\Tables\BrokerCategoriesTable;
use App\Models\BrokerCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BrokerCategoryResource extends Resource
{
    protected static ?string $model = BrokerCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Categorias';

    protected static string|UnitEnum|null $navigationGroup = 'Brokers';

    protected static ?int $navigationSort = 2;

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
        return BrokerCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BrokerCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            BenefitsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBrokerCategories::route('/'),
            'create' => CreateBrokerCategory::route('/create'),
            'edit' => EditBrokerCategory::route('/{record}/edit'),
        ];
    }
}
