<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\User;
use App\Services\Roadmap\ProgressTracker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoadmapActivityController extends Controller
{
    /**
     * Heartbeat endpoint to track active learning seconds and lease single tab.
     */
    public function heartbeat(Request $request, ProgressTracker $tracker): JsonResponse
    {
        $request->validate([
            'tab_id' => 'required|string|max:64',
        ]);

        /** @var User $user */
        $user = $request->user();

        $result = $tracker->recordHeartbeat($user, $request->input('tab_id'));

        return response()->json($result);
    }

    /**
     * Record a learning event (page view, scroll, runner run, etc.)
     */
    public function event(Request $request, ProgressTracker $tracker): JsonResponse
    {
        $request->validate([
            'event_type' => 'required|string|max:64',
            'metadata' => 'nullable|array',
        ]);

        /** @var User $user */
        $user = $request->user();

        $event = $tracker->recordEvent(
            $user,
            $request->input('event_type'),
            $request->input('metadata', [])
        );

        return response()->json([
            'success' => true,
            'event_id' => $event->id,
        ]);
    }

    /**
     * Mark a section as read.
     */
    public function markSectionRead(Request $request, ProgressTracker $tracker): JsonResponse
    {
        $request->validate([
            'section_slug' => 'required|string|max:128',
        ]);

        /** @var User $user */
        $user = $request->user();

        $tracker->markSectionRead($user, $request->input('section_slug'));

        return response()->json(['success' => true]);
    }

    /**
     * Toggle checkpoint understanding.
     */
    public function toggleCheckpoint(Request $request, ProgressTracker $tracker): JsonResponse
    {
        $request->validate([
            'checkpoint_slug' => 'required|string|max:128',
            'is_understood' => 'required|boolean',
        ]);

        /** @var User $user */
        $user = $request->user();

        $tracker->toggleCheckpoint(
            $user,
            $request->input('checkpoint_slug'),
            (bool) $request->input('is_understood')
        );

        return response()->json(['success' => true]);
    }

    /**
     * Submit answer to a checkpoint quiz.
     */
    public function answerCheckpoint(Request $request, ProgressTracker $tracker): JsonResponse
    {
        $request->validate([
            'checkpoint_slug' => 'required|string|max:128',
            'selected_answer' => 'required|string|max:16',
        ]);

        /** @var User $user */
        $user = $request->user();

        $result = $tracker->answerCheckpoint(
            $user,
            $request->input('checkpoint_slug'),
            $request->input('selected_answer')
        );

        return response()->json($result);
    }

    /**
     * Record an exercise attempt from in-browser evaluation.
     */
    public function submitExercise(Request $request, Exercise $exercise, ProgressTracker $tracker): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:65536',
            'passed' => 'required|boolean',
            'results' => 'nullable|array',
            'duration_ms' => 'nullable|integer|min:0|max:60000',
        ]);

        /** @var User $user */
        $user = $request->user();

        $attempt = $tracker->recordExerciseAttempt(
            $user,
            $exercise,
            $request->input('code'),
            (bool) $request->input('passed'),
            $request->input('results', []),
            (int) $request->input('duration_ms', 0)
        );

        return response()->json([
            'success' => true,
            'attempt_id' => $attempt->id,
            'passed' => $attempt->passed,
        ]);
    }
}
