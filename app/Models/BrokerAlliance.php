<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrokerAlliance extends Model
{
    /** @use HasFactory<\Database\Factories\BrokerAllianceFactory> */
    use HasFactory;

    use LogsModelActivity;

    protected $fillable = [
        'broker_id',
        'image_id',
        'name',
        'url',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
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
