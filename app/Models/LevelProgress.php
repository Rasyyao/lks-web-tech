<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LevelProgress extends Model
{
    protected $table = 'level_progress';

    protected $fillable = [
        'user_id',
        'level_slug',
        'sections_total',
        'sections_read',
        'checkpoints_total',
        'checkpoints_marked',
        'exercises_total',
        'exercises_passed',
        'percent_complete',
        'is_completed',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'sections_total' => 'integer',
            'sections_read' => 'integer',
            'checkpoints_total' => 'integer',
            'checkpoints_marked' => 'integer',
            'exercises_total' => 'integer',
            'exercises_passed' => 'integer',
            'percent_complete' => 'integer',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(RoadmapPage::class, 'level_slug', 'slug');
    }
}
