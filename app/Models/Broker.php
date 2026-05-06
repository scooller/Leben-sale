<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Broker extends Model
{
    /** @use HasFactory<\Database\Factories\BrokerFactory> */
    use HasFactory;

    use LogsModelActivity;

    protected $fillable = [
        'salesforce_id',
        'user_id',
        'broker_category_id',
        'avatar_image_id',
        'display_name',
        'contact_email',
        'contact_phone',
        'is_active',
        'sort_order',
        'notes',
        'salesforce_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'salesforce_synced_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BrokerCategory::class, 'broker_category_id');
    }

    public function avatarImageMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'avatar_image_id');
    }

    public function alliances(): HasMany
    {
        return $this->hasMany(BrokerAlliance::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(BrokerEvent::class);
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(BrokerGallery::class);
    }

    public function getResolvedNameAttribute(): string
    {
        if (filled($this->display_name)) {
            return (string) $this->display_name;
        }

        return (string) ($this->user?->name ?? ('Broker #'.$this->id));
    }

    public function getResolvedEmailAttribute(): ?string
    {
        return $this->contact_email ?: $this->user?->email;
    }

    public function getResolvedPhoneAttribute(): ?string
    {
        return $this->contact_phone ?: $this->user?->phone;
    }
}
