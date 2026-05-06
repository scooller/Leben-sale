<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrokerGalleryItem extends Model
{
    /** @use HasFactory<\Database\Factories\BrokerGalleryItemFactory> */
    use HasFactory;

    use LogsModelActivity;

    protected $fillable = [
        'broker_gallery_id',
        'image_id',
        'caption',
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

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(BrokerGallery::class, 'broker_gallery_id');
    }

    public function imageMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }
}
