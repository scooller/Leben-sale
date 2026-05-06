<?php

namespace App\Models;

use App\Support\LogsModelActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BrokerCategory extends Model
{
    /** @use HasFactory<\Database\Factories\BrokerCategoryFactory> */
    use HasFactory;

    use LogsModelActivity;

    protected $fillable = [
        'name',
        'slug',
        'headline',
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

    public function brokers(): HasMany
    {
        return $this->hasMany(Broker::class, 'broker_category_id');
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(BrokerBenefit::class)->orderBy('sort_order');
    }
}
