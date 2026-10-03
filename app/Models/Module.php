<?php

namespace App\Models;

use App\Enums\ModuleStatus;
use App\Enums\ModuleTrack;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'track',
        'level',
        'summary',
        'brief_md',
        'rules_md',
        'duration_minutes',
        'opens_at',
        'closes_at',
        'status',
        'max_attempts_per_day',
        'version',
        'rubric_ref',
    ];

    protected function casts(): array
    {
        return [
            'track' => ModuleTrack::class,
            'status' => ModuleStatus::class,
            'level' => 'integer',
            'duration_minutes' => 'integer',
            'opens_at' => 'datetime',
            'closes_at' => 'datetime',
            'max_attempts_per_day' => 'integer',
            'version' => 'integer',
        ];
    }

    public function assets(): HasMany
    {
        return $this->hasMany(ModuleAsset::class);
    }

    public function cohorts(): BelongsToMany
    {
        return $this->belongsToMany(Cohort::class, 'module_cohort');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Check if module is currently open for submissions.
     */
    public function isOpen(): bool
    {
        if ($this->status !== ModuleStatus::Published) {
            return false;
        }

        $now = now();

        if ($this->opens_at && $now->lt($this->opens_at)) {
            return false;
        }

        if ($this->closes_at && $now->gt($this->closes_at)) {
            return false;
        }

        return true;
    }

    /**
     * Check if a student can see this module.
     */
    public function isVisibleTo(User $user): bool
    {
        if ($this->status !== ModuleStatus::Published) {
            return false;
        }

        // If no cohorts assigned, visible to all authenticated users
        if ($this->cohorts()->count() === 0) {
            return true;
        }

        return $user->belongsToCohort(...$this->cohorts()->pluck('cohorts.id')->all());
    }

    /**
     * Count today's submissions for a user on this module.
     */
    public function todaySubmissionCount(User $user): int
    {
        return $this->submissions()
            ->where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();
    }
}
