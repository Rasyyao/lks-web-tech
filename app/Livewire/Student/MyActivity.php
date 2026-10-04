<?php

namespace App\Livewire\Student;

use App\Models\ActivityEvent;
use App\Models\CheckpointMark;
use App\Models\DailyActivity;
use App\Models\ExerciseAttempt;
use App\Models\LeaderboardEntry;
use App\Models\LevelProgress;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class MyActivity extends Component
{
    public function render(): View
    {
        $user = Auth::user();

        $dailyActivities = DailyActivity::where('user_id', $user->id)
            ->orderByDesc('date')
            ->limit(30)
            ->get();

        $totalActiveSeconds = (int) DailyActivity::where('user_id', $user->id)->sum('active_seconds');
        $totalActiveMinutes = (int) round($totalActiveSeconds / 60);

        $recentEvents = ActivityEvent::where('user_id', $user->id)
            ->orderByDesc('occurred_at')
            ->limit(20)
            ->get();

        $progresses = LevelProgress::where('user_id', $user->id)
            ->with(['page'])
            ->get();

        $leaderboardEntry = LeaderboardEntry::find($user->id);

        $checkpointsCount = CheckpointMark::where('user_id', $user->id)->where('is_understood', true)->count();
        $exercisesPassedCount = ExerciseAttempt::where('user_id', $user->id)->where('passed', true)->distinct('exercise_id')->count('exercise_id');

        return view('livewire.student.my-activity', [
            'dailyActivities' => $dailyActivities,
            'totalActiveMinutes' => $totalActiveMinutes,
            'recentEvents' => $recentEvents,
            'progresses' => $progresses,
            'leaderboardEntry' => $leaderboardEntry,
            'checkpointsCount' => $checkpointsCount,
            'exercisesPassedCount' => $exercisesPassedCount,
        ])->title('Aktivitas Belajar Saya — LKS Web Technology');
    }
}
