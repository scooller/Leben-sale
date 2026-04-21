<?php

namespace App\Filament\Resources\ContactChannels\Tables;

use App\Filament\Resources\ContactChannels\ContactChannelResource;
use App\Models\ContactChannel;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactChannelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (ContactChannel $record): array => self::resolveBadgeColor($record->slug_badge_color)),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('domain_patterns')
                    ->label('Dominios asociados')
                    ->getStateUsing(fn (ContactChannel $record): string => implode(', ', $record->domain_patterns ?? []))
                    ->placeholder('(ninguno)')
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                IconColumn::make('is_default')
                    ->label('Por defecto')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray'),
                TextColumn::make('notification_email')
                    ->label('Email notificación')
                    ->placeholder('(usa global)')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('contact_submissions_by_slug_count')
                    ->label('Envíos')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->modalHeading('¿Eliminar canal de contacto?')
                    ->modalDescription('Esta acción es irreversible. El canal se eliminará permanentemente.')
                    ->modalSubmitActionLabel('Sí, eliminar')
                    ->modalIcon('heroicon-o-exclamation-triangle')
                    ->visible(fn (ContactChannel $record): bool => ContactChannelResource::canDelete($record)),
            ]);
    }

    /**
     * @return array<string, string>
     */
    private static function resolveBadgeColor(?string $color): array
    {
        return match (strtolower(trim((string) $color))) {
            'success', 'green', 'emerald' => Color::Emerald,
            'warning', 'yellow', 'amber' => Color::Amber,
            'danger', 'red', 'rose' => Color::Red,
            'info', 'blue', 'sky' => Color::Blue,
            'primary' => Color::Indigo,
            default => Color::Gray,
        };
    }
}
