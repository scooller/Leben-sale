<?php

namespace App\Jobs;

use App\Models\ShortLink;
use App\Models\ShortLinkVisit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecordShortLinkVisitJob implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public int $shortLinkId,
        public array $payload,
    ) {}

    public function handle(): void
    {
        $shortLink = ShortLink::query()->find($this->shortLinkId);

        if (! $shortLink instanceof ShortLink) {
            return;
        }

        ShortLinkVisit::query()->create([
            'short_link_id' => $shortLink->id,
            'visited_at' => now(),
            'ip_address' => $this->payload['ip_address'] ?? null,
            'user_agent' => $this->payload['user_agent'] ?? null,
            'referer' => $this->payload['referer'] ?? null,
            'utm_source' => $this->payload['utm_source'] ?? null,
            'utm_medium' => $this->payload['utm_medium'] ?? null,
            'utm_campaign' => $this->payload['utm_campaign'] ?? null,
            'utm_term' => $this->payload['utm_term'] ?? null,
            'utm_content' => $this->payload['utm_content'] ?? null,
            'session_fingerprint' => $this->payload['session_fingerprint'] ?? null,
            'query_params' => $this->payload['query_params'] ?? [],
        ]);

        $shortLink->increment('visits_count');
        $shortLink->forceFill([
            'last_visited_at' => now(),
        ])->save();
    }
}
