<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    protected $fillable = [
        'slug',
        'level_slug',
        'title',
        'type',
        'instructions',
        'starter_code',
        'test_cases',
        'is_required',
        'time_limit_ms',
        'position',
        'hash',
    ];

    protected function casts(): array
    {
        return [
            'test_cases' => 'array',
            'is_required' => 'boolean',
            'time_limit_ms' => 'integer',
            'position' => 'integer',
        ];
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExerciseAttempt::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(RoadmapPage::class, 'level_slug', 'slug');
    }
}
