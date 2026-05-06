<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrokerEvent extends Model
{
    /** @use HasFactory<\Database\Factories\BrokerEventFactory> */
    use HasFactory;

    use LogsModelActivity;

    protected $fillable = [
        'broker_id',
        'image_id',
        'title',
        'description',
        'starts_at',
        'ends_at',
        'location',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function broker(): BelongsTo
    {
        return $this->belongsTo(Broker::class);
    }

    public function imageMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }
}
