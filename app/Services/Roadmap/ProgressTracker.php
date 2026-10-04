<?php

namespace App\Services\Roadmap;

use App\Jobs\ComputeActivityPoints;
use App\Models\ActivityEvent;
use App\Models\CheckpointMark;
use App\Models\DailyActivity;
use App\Models\Exercise;
use App\Models\ExerciseAttempt;
use App\Models\LevelProgress;
use App\Models\RoadmapCheckpoint;
use App\Models\RoadmapPage;
use App\Models\RoadmapSection;
use App\Models\SectionRead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ProgressTracker
{
    /**
     * Mark a section as read by user.
     */
    public function markSectionRead(User $user, string $sectionSlug): void
    {
        $section = RoadmapSection::where('slug', $sectionSlug)->first();
        if (! $section) {
            return;
        }

        SectionRead::firstOrCreate(
            [
                'user_id' => $user->id,
                'section_slug' => $sectionSlug,
            ],
            [
                'roadmap_section_id' => $section->id,
                'read_at' => now(),
            ]
        );

        $this->recordEvent($user, 'section_read', [
            'section_slug' => $sectionSlug,
            'page_id' => $section->roadmap_page_id,
        ]);

        if ($section->page && $section->page->kind === 'level') {
            $this->recomputeLevelProgress($user, $section->page->slug);
        }
    }

    /**
     * Mark or unmark a checkpoint understanding.
     */
    public function toggleCheckpoint(User $user, string $checkpointSlug, bool $isUnderstood): void
    {
        $checkpoint = RoadmapCheckpoint::where('slug', $checkpointSlug)->first();
        if (! $checkpoint) {
            return;
        }

        CheckpointMark::updateOrCreate(
            [
                'user_id' => $user->id,
                'checkpoint_slug' => $checkpointSlug,
            ],
            [
                'roadmap_checkpoint_id' => $checkpoint->id,
                'is_understood' => $isUnderstood,
                'marked_at' => now(),
            ]
        );

        $this->recordEvent($user, 'checkpoint_toggle', [
            'checkpoint_slug' => $checkpointSlug,
            'is_understood' => $isUnderstood,
        ]);

        if ($checkpoint->page && $checkpoint->page->kind === 'level') {
            $this->recomputeLevelProgress($user, $checkpoint->page->slug);
        }
    }

    /**
     * Submit an answer to a checkpoint quiz.
     *
     * @return array{success: bool, is_correct: bool, correct_answer: ?string, explanation: ?string}
     */
    public function answerCheckpoint(User $user, string $checkpointSlug, string $selectedAnswer): array
    {
        $checkpoint = RoadmapCheckpoint::where('slug', $checkpointSlug)->first();
        if (! $checkpoint) {
            return [
                'success' => false,
                'is_correct' => false,
                'correct_answer' => null,
                'explanation' => null,
            ];
        }

        $isCorrect = (strtolower(trim($selectedAnswer)) === strtolower(trim((string) $checkpoint->correct_answer)));

        CheckpointMark::updateOrCreate(
            [
                'user_id' => $user->id,
                'checkpoint_slug' => $checkpointSlug,
            ],
            [
                'roadmap_checkpoint_id' => $checkpoint->id,
                'selected_answer' => $selectedAnswer,
                'is_understood' => $isCorrect,
                'marked_at' => now(),
            ]
        );

        $this->recordEvent($user, $isCorrect ? 'quiz_pass' : 'quiz_fail', [
            'checkpoint_slug' => $checkpointSlug,
            'selected_answer' => $selectedAnswer,
            'is_correct' => $isCorrect,
        ]);

        if ($checkpoint->page && $checkpoint->page->kind === 'level') {
            $this->recomputeLevelProgress($user, $checkpoint->page->slug);
        }

        return [
            'success' => true,
            'is_correct' => $isCorrect,
            'correct_answer' => $checkpoint->correct_answer,
            'explanation' => $checkpoint->explanation,
        ];
    }

    /**
     * Record a coding exercise attempt.
     *
     * @param  array<string, mixed>  $results
     */
    public function recordExerciseAttempt(
        User $user,
        Exercise $exercise,
        string $code,
        bool $passed,
        array $results = [],
        int $durationMs = 0
    ): ExerciseAttempt {
        $attempt = ExerciseAttempt::create([
            'user_id' => $user->id,
            'exercise_id' => $exercise->id,
            'exercise_slug' => $exercise->slug,
            'code' => $code,
            'results' => $results,
            'passed' => $passed,
            'duration_ms' => $durationMs,
            'verified' => true,
        ]);

        $this->recordEvent($user, $passed ? 'exercise_pass' : 'exercise_run', [
            'exercise_slug' => $exercise->slug,
            'passed' => $passed,
            'duration_ms' => $durationMs,
        ]);

        $this->recomputeLevelProgress($user, $exercise->level_slug);

        return $attempt;
    }

    /**
     * Recompute user's completion progress for a level.
     */
    public function recomputeLevelProgress(User $user, string $levelSlug): LevelProgress
    {
        $page = RoadmapPage::where('slug', $levelSlug)->first();
        if (! $page) {
            throw new \InvalidArgumentException("Roadmap level page not found: {$levelSlug}");
        }

        $sectionsTotal = $page->sections()->count();
        $sectionsRead = SectionRead::where('user_id', $user->id)
            ->whereIn('roadmap_section_id', $page->sections()->pluck('id'))
            ->count();

        $checkpointsTotal = $page->checkpoints()->count();
        $checkpointsMarked = CheckpointMark::where('user_id', $user->id)
            ->whereIn('roadmap_checkpoint_id', $page->checkpoints()->pluck('id'))
            ->where('is_understood', true)
            ->count();

        $exercisesTotal = Exercise::where('level_slug', $levelSlug)->where('is_required', true)->count();
        $exercisesPassed = ExerciseAttempt::where('user_id', $user->id)
            ->whereIn('exercise_id', Exercise::where('level_slug', $levelSlug)->where('is_required', true)->pluck('id'))
            ->where('passed', true)
            ->distinct('exercise_id')
            ->count('exercise_id');

        $quizzesComplete = ($checkpointsTotal === 0 || $checkpointsMarked >= $checkpointsTotal);
        $exercisesComplete = ($exercisesTotal === 0 || $exercisesPassed >= $exercisesTotal);

        // While Tugas Coding feature is on hold (Coming Soon), completing quizzes completes the level
        if ($quizzesComplete) {
            $isCompleted = true;
            $percent = 100;
            $sectionsRead = $sectionsTotal;

            foreach ($page->sections as $sec) {
                SectionRead::firstOrCreate([
                    'user_id' => $user->id,
                    'section_slug' => $sec->slug,
                ], [
                    'roadmap_section_id' => $sec->id,
                    'read_at' => now(),
                ]);
            }
        } else {
            $totalItems = $sectionsTotal + $checkpointsTotal;
            $completedItems = $sectionsRead + $checkpointsMarked;

            $percent = $totalItems > 0 ? (int) round(($completedItems / $totalItems) * 100) : 100;
            $isCompleted = ($percent >= 100);
        }

        $progress = LevelProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'level_slug' => $levelSlug,
            ],
            [
                'sections_total' => $sectionsTotal,
                'sections_read' => $sectionsRead,
                'checkpoints_total' => $checkpointsTotal,
                'checkpoints_marked' => $checkpointsMarked,
                'exercises_total' => $exercisesTotal,
                'exercises_passed' => $exercisesPassed,
                'percent_complete' => min(100, $percent),
                'is_completed' => $isCompleted,
                'completed_at' => $isCompleted ? now() : null,
            ]
        );

        // Dispatch recalculation of activity points
        ComputeActivityPoints::dispatch($user->id);

        return $progress;
    }

    /**
     * Record a heartbeat from student tab (one active tab lease rule).
     *
     * @return array{active_seconds: int, recorded: bool, message: string}
     */
    public function recordHeartbeat(User $user, string $tabId): array
    {
        $today = Carbon::today()->toDateString();
        $leaseKey = "user_active_tab:{$user->id}";

        // Lease valid for 45 seconds
        $currentLease = Cache::get($leaseKey);

        if ($currentLease && $currentLease !== $tabId) {
            // Another tab is active; accept but do not accumulate duplicate seconds
            return [
                'active_seconds' => 0,
                'recorded' => false,
                'message' => 'Tab lain sedang aktif',
            ];
        }

        // Set or renew lease for 45s
        Cache::put($leaseKey, $tabId, 45);

        $daily = DailyActivity::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (! $daily) {
            $daily = DailyActivity::create([
                'user_id' => $user->id,
                'date' => $today,
                'active_seconds' => 0,
                'last_beat_at' => now(),
                'tab_id' => $tabId,
            ]);
        }

        $now = now();
        $secondsToAdd = 15; // default pulse every 15 seconds

        if ($daily->last_beat_at) {
            $diff = $now->diffInSeconds($daily->last_beat_at);
            if ($diff >= 5 && $diff <= 60) {
                $secondsToAdd = $diff;
            } elseif ($diff > 60) {
                $secondsToAdd = 15;
            }
        }

        // Maximum cap per day: 180 minutes = 10,800 seconds
        $newActiveSeconds = min(10800, $daily->active_seconds + $secondsToAdd);

        $daily->update([
            'active_seconds' => $newActiveSeconds,
            'last_beat_at' => $now,
            'tab_id' => $tabId,
        ]);

        return [
            'active_seconds' => $newActiveSeconds,
            'recorded' => true,
            'message' => 'Detak aktif tercatat',
        ];
    }

    /**
     * Record generic activity event for audit & timeline.
     *
     * @param  array<string, mixed>  $metadata
     */
    public function recordEvent(User $user, string $eventType, array $metadata = []): ActivityEvent
    {
        return ActivityEvent::create([
            'user_id' => $user->id,
            'event_type' => $eventType,
            'metadata' => $metadata,
            'occurred_at' => now(),
        ]);
    }
}
