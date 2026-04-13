<?php

namespace App\Filament\Resources\ContactSubmissions\ContactSubmissions\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContactSubmissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos principales')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nombre')
                            ->placeholder('-'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->label('Teléfono')
                            ->placeholder('-'),
                        TextEntry::make('rut')
                            ->label('RUT')
                            ->placeholder('-'),
                        TextEntry::make('recipient_email')
                            ->label('Email destino')
                            ->placeholder('-'),
                        TextEntry::make('submitted_at')
                            ->label('Fecha de envío')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Section::make('Campos enviados')
                    ->schema([
                        TextEntry::make('fields')
                            ->label('Payload')
                            ->formatStateUsing(static function ($state): string {
                                if (! is_array($state)) {
                                    return '-';
                                }

                                $lines = [];

                                foreach ($state as $key => $value) {
                                    $displayValue = is_scalar($value) ? (string) $value : json_encode($value);
                                    $lines[] = sprintf('%s: %s', $key, $displayValue);
                                }

                                return implode(PHP_EOL, $lines);
                            })
                            ->copyable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
