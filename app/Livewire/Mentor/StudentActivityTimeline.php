<?php

namespace App\Livewire\Mentor;

use App\Models\ActivityEvent;
use App\Models\ActivityFlag;
use App\Models\CheckpointMark;
use App\Models\DailyActivity;
use App\Models\ExerciseAttempt;
use App\Models\LeaderboardEntry;
use App\Models\LevelProgress;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class StudentActivityTimeline extends Component
{
    public User $student;

    public string $flagReason = '';

    public function mount(User $user): void
    {
        if (! $user->hasRole('student')) {
            abort(404, 'Pengguna bukan siswa.');
        }

        $this->student = $user;
    }

    public function flagActivity(): void
    {
        $this->validate([
            'flagReason' => 'required|string|min:5|max:255',
        ]);

        ActivityFlag::create([
            'user_id' => $this->student->id,
            'reason' => $this->flagReason,
            'resolved_by' => null,
            'resolved_at' => null,
        ]);

        $this->reset('flagReason');
        session()->flash('success', 'Tanda peringatan aktivitas berhasil ditambahkan.');
    }

    public function resolveFlag(int $flagId): void
    {
        $flag = ActivityFlag::where('user_id', $this->student->id)->findOrFail($flagId);
        $flag->update([
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
        ]);

        session()->flash('success', 'Peringatan aktivitas berhasil diselesaikan.');
    }

    public function render(): View
    {
        $dailyActivities = DailyActivity::where('user_id', $this->student->id)
            ->orderByDesc('date')
            ->limit(30)
            ->get();

        $totalActiveSeconds = (int) DailyActivity::where('user_id', $this->student->id)->sum('active_seconds');
        $totalActiveMinutes = (int) round($totalActiveSeconds / 60);

        $events = ActivityEvent::where('user_id', $this->student->id)
            ->orderByDesc('occurred_at')
            ->limit(50)
            ->get();

        $flags = ActivityFlag::where('user_id', $this->student->id)
            ->with('resolver')
            ->orderByDesc('created_at')
            ->get();

        $progresses = LevelProgress::where('user_id', $this->student->id)
            ->with('page')
            ->get();

        $leaderboardEntry = LeaderboardEntry::find($this->student->id);

        $checkpointsCount = CheckpointMark::where('user_id', $this->student->id)->where('is_understood', true)->count();
        $exercisesPassedCount = ExerciseAttempt::where('user_id', $this->student->id)->where('passed', true)->distinct('exercise_id')->count('exercise_id');

        return view('livewire.mentor.student-activity-timeline', [
            'student' => $this->student,
            'dailyActivities' => $dailyActivities,
            'totalActiveMinutes' => $totalActiveMinutes,
            'events' => $events,
            'flags' => $flags,
            'progresses' => $progresses,
            'leaderboardEntry' => $leaderboardEntry,
            'checkpointsCount' => $checkpointsCount,
            'exercisesPassedCount' => $exercisesPassedCount,
        ])->title('Aktivitas Siswa: '.$this->student->name.' — Mentor LKS');
    }
}
