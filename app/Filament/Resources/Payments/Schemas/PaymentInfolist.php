<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pago')
                    ->columns(2)
                    ->components([
                        TextEntry::make('id')
                            ->label('ID'),
                        TextEntry::make('gateway')
                            ->label('Gateway')
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
                            }),
                        TextEntry::make('status')
                            ->label('Estado')
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
                            }),
                        TextEntry::make('amount')
                            ->label('Monto')
                            ->money(fn ($record): string => $record->currency ?? 'CLP'),
                        TextEntry::make('currency')
                            ->label('Moneda')
                            ->placeholder('-'),
                        TextEntry::make('gateway_tx_id')
                            ->label('Gateway TX ID')
                            ->copyable()
                            ->placeholder('-'),
                        TextEntry::make('completed_at')
                            ->label('Completado')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label('Creado')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Actualizado')
                            ->dateTime(),
                    ]),
                Section::make('Relaciones')
                    ->columns(2)
                    ->components([
                        TextEntry::make('user.name')
                            ->label('Usuario')
                            ->placeholder('-'),
                        TextEntry::make('project.name')
                            ->label('Proyecto')
                            ->placeholder('-'),
                    ]),
                Section::make('Metadata')
                    ->components([
                        KeyValueEntry::make('metadata')
                            ->label('Metadata')
                            ->placeholder('Sin metadata'),
                    ]),
            ]);
    }
}
