<?php

namespace App\Filament\Resources\Brokers\BrokerCategories\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BenefitsRelationManager extends RelationManager
{
    protected static string $relationship = 'benefits';

    protected static ?string $title = 'Beneficios';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->label('Estado en esta categoría')
                    ->options([
                        'included' => 'Incluido',
                        'not_applicable' => 'No aplica',
                    ])
                    ->default('included')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('section')
                    ->label('Sección')
                    ->badge()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Beneficio')
                    ->searchable(),

                TextColumn::make('pivot.status')
                    ->label('Estado')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'included' => 'Incluido',
                        'not_applicable' => 'No aplica',
                        default => '-',
                    })
                    ->badge()
                    ->color(fn (?string $state): string => $state === 'included' ? 'success' : 'gray'),
            ])
            ->filters([])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Beneficio')
                            ->searchable(),
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'included' => 'Incluido',
                                'not_applicable' => 'No aplica',
                            ])
                            ->default('included')
                            ->required(),
                    ]),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make()
                    ->label('Cambiar estado'),
                DetachAction::make()
                    ->label('Quitar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
