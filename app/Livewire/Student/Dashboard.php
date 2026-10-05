<?php

namespace App\Livewire\Student;

use App\Enums\ModuleStatus;
use App\Enums\SubmissionStatus;
use App\Models\Announcement;
use App\Models\DailyActivity;
use App\Models\LeaderboardEntry;
use App\Models\LevelProgress;
use App\Models\Module;
use App\Models\PracticeAttempt;
use App\Models\RoadmapPage;
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

        // 2. Modules Assigned to Student
        $modules = Module::query()
            ->where('status', ModuleStatus::Published)
            ->whereHas('cohorts', fn ($q) => $q->whereIn('cohorts.id', $cohortIds))
            ->orderBy('level')
            ->orderBy('closes_at')
            ->with(['submissions' => fn ($q) => $q->where('user_id', $user->id)])
            ->get();

        // Module statistics
        $totalModulesCount = $modules->count();
        $submittedModulesCount = $modules->filter(fn ($m) => $m->submissions->isNotEmpty())->count();
        $gradedModulesCount = $modules->filter(fn ($m) => $m->submissions->contains('status', SubmissionStatus::Graded))->count();
        $pendingModulesCount = $modules->filter(fn ($m) => $m->submissions->contains('status', SubmissionStatus::Received))->count();
        $moduleProgressPercent = $totalModulesCount > 0 ? (int) round(($submittedModulesCount / $totalModulesCount) * 100) : 0;

        // 3. Learning Roadmap Progress
        $totalLevelsCount = RoadmapPage::where('kind', 'level')->count();
        $userProgress = LevelProgress::where('user_id', $user->id)->get();
        $completedLevelsCount = $userProgress->where('is_completed', true)->count();
        $roadmapPercent = $totalLevelsCount > 0 ? (int) round(($completedLevelsCount / $totalLevelsCount) * 100) : 0;

        $completedSlugs = $userProgress->where('is_completed', true)->pluck('level_slug')->toArray();
        $currentLevel = RoadmapPage::where('kind', 'level')
            ->whereNotIn('slug', $completedSlugs)
            ->orderBy('position')
            ->first();

        // 4. Learning Activity (Keaktifan)
        $totalActiveSeconds = (int) DailyActivity::where('user_id', $user->id)->sum('active_seconds');
        $activeHours = round($totalActiveSeconds / 3600, 1);
        $activeDaysCount = DailyActivity::where('user_id', $user->id)->where('active_seconds', '>', 0)->count();
        $todaySeconds = (int) (DailyActivity::where('user_id', $user->id)->whereDate('date', today())->value('active_seconds') ?? 0);
        $todayActiveMinutes = (int) round($todaySeconds / 60);

        // 5. Student Rank & Leaderboard
        $totalStudentsCount = LeaderboardEntry::count();
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

        // 6. Latest attempt
        $latestAttempt = PracticeAttempt::query()
            ->where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->first();

        // 7. Recommended topic
        $recommendedTopic = Topic::withCount(['questions' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('position')
            ->first();

        return view('livewire.student.dashboard', [
            'user' => $user,
            'announcements' => $announcements,
            'modules' => $modules,
            'totalModulesCount' => $totalModulesCount,
            'submittedModulesCount' => $submittedModulesCount,
            'gradedModulesCount' => $gradedModulesCount,
            'pendingModulesCount' => $pendingModulesCount,
            'moduleProgressPercent' => $moduleProgressPercent,
            'totalLevelsCount' => $totalLevelsCount,
            'completedLevelsCount' => $completedLevelsCount,
            'roadmapPercent' => $roadmapPercent,
            'currentLevel' => $currentLevel,
            'activeHours' => $activeHours,
            'activeDaysCount' => $activeDaysCount,
            'todayActiveMinutes' => $todayActiveMinutes,
            'studentEntry' => $studentEntry,
            'rank' => $rank,
            'totalStudentsCount' => $totalStudentsCount,
            'latestAttempt' => $latestAttempt,
            'recommendedTopic' => $recommendedTopic,
        ]);
    }
}
