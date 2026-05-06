<?php

namespace App\Filament\Resources\SoqlQueryRuns;

use App\Filament\Resources\SoqlQueryRuns\Pages\ListSoqlQueryRuns;
use App\Filament\Resources\SoqlQueryRuns\Tables\SoqlQueryRunsTable;
use App\Models\SoqlQueryRun;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class SoqlQueryRunResource extends Resource
{
    protected static ?string $model = SoqlQueryRun::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCommandLine;

    protected static ?string $navigationLabel = 'SOQL Runner';

    protected static ?string $modelLabel = 'Consulta SOQL';

    protected static ?string $pluralModelLabel = 'Consultas SOQL';

    protected static string|UnitEnum|null $navigationGroup = 'Herramientas';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return SoqlQueryRunsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSoqlQueryRuns::route('/'),
        ];
    }
}
