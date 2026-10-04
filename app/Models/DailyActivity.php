<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyActivity extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'active_seconds',
        'last_beat_at',
        'tab_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'active_seconds' => 'integer',
            'last_beat_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
