<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaderboardEntry extends Model
{
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'question_points',
        'module_points',
        'activity_points',
        'total',
        'breakdown',
        'reached_at',
    ];

    protected function casts(): array
    {
        return [
            'question_points' => 'integer',
            'module_points' => 'integer',
            'activity_points' => 'integer',
            'total' => 'integer',
            'breakdown' => 'array',
            'reached_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
