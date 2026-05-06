<?php

namespace App\Filament\Resources\ApiTokens;

use App\Filament\Resources\ApiTokens\Pages\ListApiTokens;
use App\Filament\Resources\ApiTokens\Tables\ApiTokensTable;
use App\Models\PersonalAccessToken;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ApiTokenResource extends Resource
{
    protected static ?string $model = PersonalAccessToken::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Tokens API';

    protected static ?string $modelLabel = 'Token API';

    protected static ?string $pluralModelLabel = 'Tokens API';

    protected static string|UnitEnum|null $navigationGroup = 'Herramientas';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadgeColor(): ?string
    {
        return 'purple';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) PersonalAccessToken::query()->count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return ApiTokensTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiTokens::route('/'),
        ];
    }
}
