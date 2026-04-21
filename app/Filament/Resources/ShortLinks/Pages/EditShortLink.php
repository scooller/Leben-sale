<?php

namespace App\Filament\Resources\ShortLinks\Pages;

use App\Filament\Resources\ShortLinks\ShortLinkResource;
use App\Services\ShortLink\ShortLinkService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditShortLink extends EditRecord
{
    protected static string $resource = ShortLinkResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var ShortLinkService $service */
        $service = app(ShortLinkService::class);

        $data['destination_url'] = $service->normalizeAndValidateDestinationUrl((string) ($data['destination_url'] ?? ''));
        $data['slug'] = strtolower((string) ($data['slug'] ?? ''));

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
