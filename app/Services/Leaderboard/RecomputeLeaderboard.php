<?php

namespace App\Services\Leaderboard;

use App\Models\LeaderboardEntry;
use App\Models\PracticeAttemptItem;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RecomputeLeaderboard
{
    /**
     * Recompute leaderboard entry for a specific student.
     *
     * Question points: sum of points for each distinct question answered correctly at least once.
     * Module points: sum of best mentor score for each module.
     * Total: question_points + module_points.
     * Tie-break: reached_at (when the current total was first achieved).
     */
    public function forUser(int $userId): void
    {
        $user = User::find($userId);

        if (! $user || ! $user->hasRole('student')) {
            return;
        }

        // Question points: distinct questions answered correctly at least once
        $questionPoints = PracticeAttemptItem::query()
            ->whereHas('attempt', fn ($q) => $q->where('user_id', $userId)->whereNotNull('submitted_at'))
            ->where('is_correct', true)
            ->select('question_id', DB::raw('MAX(points) as max_points'))
            ->groupBy('question_id')
            ->get()
            ->sum('max_points');

        // Module points: best mentor score per module
        $modulePoints = Submission::query()
            ->where('user_id', $userId)
            ->whereNotNull('manual_score')
            ->select('module_id', DB::raw('MAX(manual_score) as best_score'))
            ->groupBy('module_id')
            ->get()
            ->sum('best_score');

        $total = $questionPoints + $modulePoints;

        // Check if total changed to update reached_at
        $existing = LeaderboardEntry::find($userId);
        $reachedAt = $existing?->reached_at ?? now();

        if ($existing && $existing->total !== $total) {
            $reachedAt = now();
        }

        LeaderboardEntry::updateOrCreate(
            ['user_id' => $userId],
            [
                'question_points' => $questionPoints,
                'module_points' => $modulePoints,
                'total' => $total,
                'reached_at' => $reachedAt,
            ],
        );
    }
}
