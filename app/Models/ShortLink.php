<?php

namespace App\Models;

use App\Enums\ShortLinkStatus;
use App\Support\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShortLink extends Model
{
    /** @use HasFactory<\Database\Factories\ShortLinkFactory> */
    use HasFactory, LogsModelActivity;

    protected $fillable = [
        'created_by',
        'slug',
        'title',
        'destination_url',
        'status',
        'tag_manager_id',
        'visits_count',
        'last_visited_at',
        'expires_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => ShortLinkStatus::class,
            'visits_count' => 'integer',
            'last_visited_at' => 'datetime',
            'expires_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(ShortLinkVisit::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ShortLinkStatus::ACTIVE->value)
            ->where(function (Builder $builder): void {
                $builder->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isRedirectEnabled(): bool
    {
        return $this->status === ShortLinkStatus::ACTIVE && ! $this->isExpired();
    }

    public function shortUrl(): string
    {
        return url('/s/'.$this->slug);
    }
}
