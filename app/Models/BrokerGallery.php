<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BrokerGallery extends Model
{
    /** @use HasFactory<\Database\Factories\BrokerGalleryFactory> */
    use HasFactory;

    use LogsModelActivity;

    protected $fillable = [
        'broker_id',
        'title',
        'year',
        'month',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function broker(): BelongsTo
    {
        return $this->belongsTo(Broker::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BrokerGalleryItem::class)->orderBy('sort_order');
    }
}
