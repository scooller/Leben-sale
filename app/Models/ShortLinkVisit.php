<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShortLinkVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_link_id',
        'visited_at',
        'ip_address',
        'user_agent',
        'referer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'session_fingerprint',
        'query_params',
    ];

    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
            'query_params' => 'array',
        ];
    }

    public function shortLink(): BelongsTo
    {
        return $this->belongsTo(ShortLink::class);
    }
}
