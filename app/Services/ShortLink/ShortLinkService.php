<?php

namespace App\Services\ShortLink;

use App\Enums\ShortLinkStatus;
use App\Models\ShortLink;
use App\Models\SiteSetting;
use Illuminate\Support\Str;

class ShortLinkService
{
    public function generateUniqueSlug(int $length = 7): string
    {
        do {
            $slug = Str::lower(Str::random($length));
        } while (ShortLink::query()->where('slug', $slug)->exists());

        return $slug;
    }

    public function normalizeAndValidateDestinationUrl(string $destinationUrl): string
    {
        $normalizedUrl = trim($destinationUrl);

        if ($normalizedUrl === '') {
            throw new InvalidArgumentException('La URL de destino es obligatoria.');
        }

        $parts = parse_url($normalizedUrl);
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));

        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            throw new InvalidArgumentException('La URL de destino debe ser una URL válida (http o https).');
        }

        return $normalizedUrl;
    }

    public function resolveStatusForRedirect(ShortLink $shortLink): ShortLinkStatus
    {
        if ($shortLink->isExpired()) {
            return ShortLinkStatus::EXPIRED;
        }

        return $shortLink->status instanceof ShortLinkStatus
            ? $shortLink->status
            : ShortLinkStatus::fromValue((string) $shortLink->status) ?? ShortLinkStatus::DISABLED;
    }

    public function resolveTagManagerId(ShortLink $shortLink): ?string
    {
        $linkLevelTagManager = trim((string) ($shortLink->tag_manager_id ?? ''));

        if ($linkLevelTagManager !== '') {
            return $linkLevelTagManager;
        }

        $globalTagManager = trim((string) SiteSetting::get('tag_manager_id', ''));

        return $globalTagManager !== '' ? $globalTagManager : null;
    }

    public function withForwardedQuery(string $destinationUrl, array $query): string
    {
        if ($query === []) {
            return $destinationUrl;
        }

        $parts = parse_url($destinationUrl);

        if (! is_array($parts)) {
            return $destinationUrl;
        }

        $existingQuery = [];
        parse_str((string) ($parts['query'] ?? ''), $existingQuery);

        $mergedQuery = array_merge($existingQuery, $query);

        $rebuiltUrl = ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '');

        if (isset($parts['port'])) {
            $rebuiltUrl .= ':'.$parts['port'];
        }

        $rebuiltUrl .= $parts['path'] ?? '';

        if ($mergedQuery !== []) {
            $rebuiltUrl .= '?'.http_build_query($mergedQuery);
        }

        if (isset($parts['fragment']) && $parts['fragment'] !== '') {
            $rebuiltUrl .= '#'.$parts['fragment'];
        }

        return $rebuiltUrl;
    }
}
