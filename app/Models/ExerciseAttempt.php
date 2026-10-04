<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'exercise_id',
        'exercise_slug',
        'code',
        'results',
        'passed',
        'duration_ms',
        'verified',
    ];

    protected function casts(): array
    {
        return [
            'results' => 'array',
            'passed' => 'boolean',
            'duration_ms' => 'integer',
            'verified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}
