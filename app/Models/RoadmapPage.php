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

    public function previousLevel(): ?self
    {
        if ($this->kind !== 'level' || $this->position === 0) {
            return null;
        }

        return self::where('kind', 'level')
            ->where('position', $this->position - 1)
            ->first();
    }

    public function isUnlockedFor(User $user): bool
    {
        if ($user->hasAnyRole(['mentor', 'admin'])) {
            return true;
        }

        if ($this->kind !== 'level' || $this->position === 0) {
            return true;
        }

        $prev = $this->previousLevel();
        if (! $prev) {
            return true;
        }

        $prevProgress = LevelProgress::where('user_id', $user->id)
            ->where('level_slug', $prev->slug)
            ->first();

        return (bool) ($prevProgress?->is_completed ?? false);
    }
}
