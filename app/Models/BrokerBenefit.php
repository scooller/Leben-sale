<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrokerBenefit extends Model
{
    /** @use HasFactory<\Database\Factories\BrokerBenefitFactory> */
    use HasFactory;

    use LogsModelActivity;

    protected $fillable = [
        'broker_category_id',
        'section',
        'title',
        'description',
        'status',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BrokerCategory::class, 'broker_category_id');
    }
}
