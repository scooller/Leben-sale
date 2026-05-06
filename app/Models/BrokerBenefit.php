<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BrokerBenefit extends Model
{
    /** @use HasFactory<\Database\Factories\BrokerBenefitFactory> */
    use HasFactory;

    use LogsModelActivity;

    protected $fillable = [
        'section',
        'title',
        'description',
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

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(BrokerCategory::class, 'broker_benefit_category')
            ->withPivot('status')
            ->withTimestamps()
            ->orderBy('sort_order');
    }
}
