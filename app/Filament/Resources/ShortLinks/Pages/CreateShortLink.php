<?php

namespace App\Filament\Resources\ShortLinks\Pages;

use App\Enums\ShortLinkStatus;
use App\Filament\Resources\ShortLinks\ShortLinkResource;
use App\Services\ShortLink\ShortLinkService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateShortLink extends CreateRecord
{
    protected static string $resource = ShortLinkResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var ShortLinkService $service */
        $service = app(ShortLinkService::class);

        $data['destination_url'] = $service->normalizeAndValidateDestinationUrl((string) ($data['destination_url'] ?? ''));
        $data['slug'] = filled($data['slug'] ?? null)
            ? strtolower((string) $data['slug'])
            : $service->generateUniqueSlug();
        $data['status'] = $data['status'] ?? ShortLinkStatus::ACTIVE->value;
        $data['created_by'] = Auth::id();

        return $data;
    }
}
