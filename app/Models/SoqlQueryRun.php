<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoqlQueryRun extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'soql',
        'status',
        'records_count',
        'duration_ms',
        'limit_value',
        'error_message',
        'result_preview',
        'meta',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'records_count' => 'integer',
            'duration_ms' => 'integer',
            'limit_value' => 'integer',
            'result_preview' => 'array',
            'meta' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
