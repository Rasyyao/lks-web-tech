<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoadmapCheckpoint extends Model
{
    protected $fillable = [
        'roadmap_page_id',
        'slug',
        'position',
        'prompt',
        'options',
        'correct_answer',
        'explanation',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'options' => 'array',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(RoadmapPage::class, 'roadmap_page_id');
    }

    public function marks(): HasMany
    {
        return $this->hasMany(CheckpointMark::class, 'roadmap_checkpoint_id');
    }
}
