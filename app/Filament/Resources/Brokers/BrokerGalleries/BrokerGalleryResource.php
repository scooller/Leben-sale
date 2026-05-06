<?php

namespace App\Filament\Resources\Brokers\BrokerGalleries;

use App\Filament\Resources\Brokers\BrokerGalleries\Pages\CreateBrokerGallery;
use App\Filament\Resources\Brokers\BrokerGalleries\Pages\EditBrokerGallery;
use App\Filament\Resources\Brokers\BrokerGalleries\Pages\ListBrokerGalleries;
use App\Filament\Resources\Brokers\BrokerGalleries\Schemas\BrokerGalleryForm;
use App\Filament\Resources\Brokers\BrokerGalleries\Tables\BrokerGalleriesTable;
use App\Models\BrokerGallery;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BrokerGalleryResource extends Resource
{
    protected static ?string $model = BrokerGallery::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Galerias';

    protected static string|UnitEnum|null $navigationGroup = 'Brokers';

    protected static ?int $navigationSort = 6;

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
        return BrokerGalleryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BrokerGalleriesTable::configure($table);
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
            'index' => ListBrokerGalleries::route('/'),
            'create' => CreateBrokerGallery::route('/create'),
            'edit' => EditBrokerGallery::route('/{record}/edit'),
        ];
    }
}
