<?php

namespace App\Services\Leaderboard;

use App\Models\CheckpointMark;
use App\Models\DailyActivity;
use App\Models\ExerciseAttempt;
use App\Models\LeaderboardEntry;
use App\Models\LevelProgress;
use App\Models\PracticeAttemptItem;
use App\Models\SectionRead;
use App\Models\Setting;
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

        // Activity points: learning roadmap progress & active study
        $checkpointsCount = CheckpointMark::where('user_id', $userId)->where('is_understood', true)->count();
        $sectionsReadCount = SectionRead::where('user_id', $userId)->count();
        $levelsCompletedCount = LevelProgress::where('user_id', $userId)->where('is_completed', true)->count();
        $exercisesPassedCount = ExerciseAttempt::where('user_id', $userId)->where('passed', true)->distinct('exercise_id')->count('exercise_id');
        $activeSeconds = (int) DailyActivity::where('user_id', $userId)->sum('active_seconds');
        $activeMinutes = (int) round($activeSeconds / 60);

        $activityPoints = ($checkpointsCount * 2)
            + ($sectionsReadCount * 1)
            + ($levelsCompletedCount * 15)
            + ($exercisesPassedCount * 10)
            + (int) floor($activeMinutes / 5);

        $weightActivity = (int) Setting::get('weight_activity', 30);
        $weightModules = (int) Setting::get('weight_modules', 70);

        $moduleScorePart = ($questionPoints + $modulePoints) * ($weightModules / 100.0);
        $activityPart = $activityPoints * ($weightActivity / 100.0);
        $total = (int) round($moduleScorePart + $activityPart);

        $breakdown = [
            'sections_read' => $sectionsReadCount,
            'checkpoints_marked' => $checkpointsCount,
            'levels_completed' => $levelsCompletedCount,
            'exercises_passed' => $exercisesPassedCount,
            'active_minutes' => $activeMinutes,
            'activity_raw_points' => $activityPoints,
            'weight_activity' => $weightActivity,
            'weight_modules' => $weightModules,
            'module_score_total' => $questionPoints + $modulePoints,
        ];

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
                'activity_points' => $activityPoints,
                'total' => $total,
                'breakdown' => $breakdown,
                'reached_at' => $reachedAt,
            ],
        );
    }
}
