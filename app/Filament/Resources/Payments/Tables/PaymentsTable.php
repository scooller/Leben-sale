<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Filament\Exports\PaymentExporter;
use App\Filament\Resources\Payments\PaymentResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('gateway')
                    ->badge()
                    ->color('info')
                    ->icon(function (PaymentGateway|string|null $state): string {
                        $gateway = $state instanceof PaymentGateway
                            ? $state
                            : collect(PaymentGateway::cases())->first(
                                fn (PaymentGateway $gateway): bool => $gateway->value === (string) $state,
                            );

                        return $gateway?->icon() ?? 'heroicon-o-credit-card';
                    })
                    ->formatStateUsing(function (PaymentGateway|string|null $state): string {
                        $gateway = $state instanceof PaymentGateway
                            ? $state
                            : collect(PaymentGateway::cases())->first(
                                fn (PaymentGateway $gateway): bool => $gateway->value === (string) $state,
                            );

                        return $gateway?->label() ?? '-';
                    })
                    ->searchable(),
                TextColumn::make('gateway_tx_id')
                    ->searchable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(function (PaymentStatus|string|null $state): string {
                        $status = $state instanceof PaymentStatus
                            ? $state
                            : collect(PaymentStatus::cases())->first(
                                fn (PaymentStatus $status): bool => $status->value === (string) $state,
                            );

                        return $status?->color() ?? 'gray';
                    })
                    ->icon(function (PaymentStatus|string|null $state): string {
                        $status = $state instanceof PaymentStatus
                            ? $state
                            : collect(PaymentStatus::cases())->first(
                                fn (PaymentStatus $status): bool => $status->value === (string) $state,
                            );

                        return $status?->icon() ?? 'heroicon-o-question-mark-circle';
                    })
                    ->formatStateUsing(function (PaymentStatus|string|null $state): string {
                        $status = $state instanceof PaymentStatus
                            ? $state
                            : collect(PaymentStatus::cases())->first(
                                fn (PaymentStatus $status): bool => $status->value === (string) $state,
                            );

                        return $status?->label() ?? '-';
                    })
                    ->searchable(),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordUrl(fn ($record): string => PaymentResource::getUrl('view', ['record' => $record]))
            ->filters([
                SelectFilter::make('gateway')
                    ->label('Gateway')
                    ->options(PaymentGateway::toSelectArray())
                    ->searchable(),
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(PaymentStatus::toSelectArray())
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(PaymentExporter::class),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
