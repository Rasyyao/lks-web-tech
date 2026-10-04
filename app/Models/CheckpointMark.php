<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckpointMark extends Model
{
    protected $fillable = [
        'user_id',
        'roadmap_checkpoint_id',
        'checkpoint_slug',
        'selected_answer',
        'is_understood',
        'marked_at',
    ];

    protected function casts(): array
    {
        return [
            'is_understood' => 'boolean',
            'marked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(RoadmapCheckpoint::class, 'roadmap_checkpoint_id');
    }
}
