<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoadmapPage extends Model
{
    protected $fillable = [
        'slug',
        'kind',
        'position',
        'title',
        'goal',
        'estimated_time',
        'content_hash',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(RoadmapSection::class)->orderBy('position');
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(RoadmapCheckpoint::class)->orderBy('position');
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class, 'level_slug', 'slug')->orderBy('position');
    }
}
