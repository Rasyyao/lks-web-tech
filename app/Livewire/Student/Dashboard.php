<?php

namespace App\Livewire\Student;

use App\Enums\ModuleStatus;
use App\Models\Announcement;
use App\Models\LeaderboardEntry;
use App\Models\Module;
use App\Models\PracticeAttempt;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Beranda')]
class Dashboard extends Component
{
    public function mount()
    {
        if (! Auth::user()?->hasRole('student')) {
            return redirect()->to(Auth::user()->homeUrl());
        }
    }

    public function render()
    {
        $user = Auth::user();
        $cohortIds = $user->cohorts->pluck('id');

        // 1. Announcements (max 2)
        $announcements = Announcement::query()
            ->where(function ($q) use ($cohortIds) {
                $q->whereNull('cohort_id')
                    ->orWhereIn('cohort_id', $cohortIds);
            })
            ->latest('published_at')
            ->take(2)
            ->get();

        // 2. Modules in progress with deadlines
        $modules = Module::query()
            ->where('status', ModuleStatus::Published)
            ->whereHas('cohorts', fn ($q) => $q->whereIn('cohorts.id', $cohortIds))
            ->where('closes_at', '>=', now())
            ->orderBy('closes_at')
            ->take(3)
            ->with(['submissions' => fn ($q) => $q->where('user_id', $user->id)])
            ->get();

        // 3. Latest attempt
        $latestAttempt = PracticeAttempt::query()
            ->where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->first();

        // 4. Student Rank
        $studentEntry = LeaderboardEntry::find($user->id);
        $rank = null;
        if ($studentEntry) {
            $rank = LeaderboardEntry::query()
                ->where(function ($q) use ($studentEntry) {
                    $q->where('total', '>', $studentEntry->total)
                        ->orWhere(function ($q2) use ($studentEntry) {
                            $q2->where('total', $studentEntry->total)
                                ->where('reached_at', '<', $studentEntry->reached_at);
                        });
                })
                ->count() + 1;
        }

        // 5. Recommended topic
        $recommendedTopic = Topic::withCount(['questions' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('position')
            ->first();

        return view('livewire.student.dashboard', [
            'user' => $user,
            'announcements' => $announcements,
            'modules' => $modules,
            'latestAttempt' => $latestAttempt,
            'studentEntry' => $studentEntry,
            'rank' => $rank,
            'recommendedTopic' => $recommendedTopic,
        ]);
    }
}
