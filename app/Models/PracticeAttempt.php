<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PracticeAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'filters',
        'question_count',
        'time_limit_seconds',
        'started_at',
        'submitted_at',
        'points_earned',
        'score_pct',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'question_count' => 'integer',
            'time_limit_seconds' => 'integer',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'points_earned' => 'integer',
            'score_pct' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PracticeAttemptItem::class, 'attempt_id')->orderBy('position');
    }

    /**
     * Check if the attempt is still open (not submitted and timer not expired).
     */
    public function isOpen(): bool
    {
        if ($this->submitted_at !== null) {
            return false;
        }

        if ($this->time_limit_seconds && $this->started_at) {
            return now()->lt($this->started_at->addSeconds($this->time_limit_seconds));
        }

        return true;
    }

    /**
     * Check if the timer has expired.
     */
    public function isTimerExpired(): bool
    {
        if (! $this->time_limit_seconds || ! $this->started_at) {
            return false;
        }

        return now()->gte($this->started_at->addSeconds($this->time_limit_seconds));
    }

    /**
     * Get remaining seconds on the timer.
     */
    public function remainingSeconds(): ?int
    {
        if (! $this->time_limit_seconds || ! $this->started_at) {
            return null;
        }

        $remaining = $this->started_at->addSeconds($this->time_limit_seconds)->diffInSeconds(now(), false);

        return max(0, -$remaining);
    }
}
