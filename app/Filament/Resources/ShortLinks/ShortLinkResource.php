<?php

namespace App\Filament\Resources\ShortLinks;

use App\Filament\Resources\ShortLinks\Pages\CreateShortLink;
use App\Filament\Resources\ShortLinks\Pages\EditShortLink;
use App\Filament\Resources\ShortLinks\Pages\ListShortLinks;
use App\Filament\Resources\ShortLinks\Pages\ViewShortLink;
use App\Filament\Resources\ShortLinks\Schemas\ShortLinkForm;
use App\Filament\Resources\ShortLinks\Schemas\ShortLinkInfolist;
use App\Filament\Resources\ShortLinks\Tables\ShortLinksTable;
use App\Models\ShortLink;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ShortLinkResource extends Resource
{
    protected static ?string $model = ShortLink::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static ?string $navigationLabel = 'Links cortos';

    protected static ?string $modelLabel = 'Link corto';

    protected static ?string $pluralModelLabel = 'Links cortos';

    protected static string|UnitEnum|null $navigationGroup = 'Herramientas';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'slug';

    public static function getNavigationBadge(): ?string
    {
        return (string) ShortLink::query()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return ShortLinkForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ShortLinkInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShortLinksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShortLinks::route('/'),
            'create' => CreateShortLink::route('/create'),
            'view' => ViewShortLink::route('/{record}'),
            'edit' => EditShortLink::route('/{record}/edit'),
        ];
    }
}
