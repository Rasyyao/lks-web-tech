<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'body_md',
        'cohort_id',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    /**
     * Scope: visible to a given user (global or matching cohort).
     */
    public function scopeVisibleTo($query, User $user)
    {
        $cohortIds = $user->cohorts()->pluck('cohorts.id');

        return $query->whereNotNull('published_at')
            ->where(function ($q) use ($cohortIds) {
                $q->whereNull('cohort_id')
                    ->orWhereIn('cohort_id', $cohortIds);
            })
            ->orderByDesc('published_at');
    }
}
