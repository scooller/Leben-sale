<?php

namespace App\Filament\Resources\Asesores\Schemas;

use App\Services\Salesforce\SalesforceService;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class AsesorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del Asesor')
                    ->schema([
                        TextInput::make('salesforce_id')
                            ->label('Salesforce ID')
                            ->maxLength(30)
                            ->unique(ignoreRecord: true)
                            ->readOnly(true),

                        TextInput::make('first_name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('last_name')
                            ->label('Apellido')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->suffixAction(
                                Action::make('syncAsesorByEmail')
                                    ->icon('heroicon-m-arrow-path')
                                    ->tooltip('Importar asesor desde Salesforce por email')
                                    ->action(function (SalesforceService $salesforceService, Get $get, Set $set): void {
                                        $email = mb_strtolower(trim((string) $get('email')));

                                        if ($email === '') {
                                            Notification::make()
                                                ->title('Ingresa un email para sincronizar')
                                                ->warning()
                                                ->send();

                                            return;
                                        }

                                        $salesforceUser = $salesforceService->findSalesforceUserByEmail($email);

                                        if ($salesforceUser === null) {
                                            Notification::make()
                                                ->title('No se encontró un asesor con ese email en Salesforce')
                                                ->danger()
                                                ->send();

                                            return;
                                        }

                                        $set('salesforce_id', $salesforceUser['id']);
                                        $set('first_name', $salesforceUser['first_name']);
                                        $set('last_name', $salesforceUser['last_name']);
                                        $set('email', $salesforceUser['email'] ?: $email);
                                        $set('whatsapp_owner', $salesforceUser['whatsapp_owner']);
                                        $set('avatar_url', $salesforceUser['avatar_url']);
                                        $set('is_active', (bool) ($salesforceUser['is_active'] ?? true));

                                        Notification::make()
                                            ->title('Datos del asesor importados desde Salesforce')
                                            ->success()
                                            ->send();
                                    })
                            )
                            ->maxLength(255),

                        TextInput::make('whatsapp_owner')
                            ->label('WhatsApp')
                            ->maxLength(255),

                        CuratorPicker::make('avatar_image_id')
                            ->label('Avatar Manual (Curator)')
                            ->helperText('Si cargas una imagen aquí, tendrá prioridad sobre el avatar sincronizado desde Salesforce.'),

                        TextInput::make('avatar_url')
                            ->label('Avatar URL (Salesforce)')
                            ->url()
                            ->maxLength(2048)
                            ->helperText('Este valor se sincroniza desde MediumPhotoUrl y se usa como fallback cuando no hay avatar manual en Curator.'),

                        Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
