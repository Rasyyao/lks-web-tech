<?php

namespace App\Livewire\Student;

use App\Models\DailyActivity;
use App\Models\LevelProgress;
use App\Models\RoadmapPage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class RoadmapOverview extends Component
{
    public function render(): View
    {
        $user = Auth::user();

        $levels = RoadmapPage::where('kind', 'level')
            ->orderBy('position')
            ->with(['checkpoints', 'exercises', 'sections'])
            ->get();

        $references = RoadmapPage::whereIn('kind', ['reference', 'guide'])
            ->orderBy('position')
            ->get();

        $userProgress = LevelProgress::where('user_id', $user->id)
            ->get()
            ->keyBy('level_slug');

        $totalLevels = $levels->count();
        $completedLevels = $userProgress->where('is_completed', true)->count();
        $overallPercent = $totalLevels > 0 ? (int) round(($completedLevels / $totalLevels) * 100) : 0;

        $totalActiveSeconds = (int) DailyActivity::where('user_id', $user->id)->sum('active_seconds');
        $activeHours = round($totalActiveSeconds / 3600, 1);

        // Find next unfinished level to resume
        $nextLevel = null;
        foreach ($levels as $lvl) {
            $prog = $userProgress->get($lvl->slug);
            if (! $prog || ! $prog->is_completed) {
                $nextLevel = $lvl;
                break;
            }
        }

        return view('livewire.student.roadmap-overview', [
            'levels' => $levels,
            'references' => $references,
            'userProgress' => $userProgress,
            'completedLevels' => $completedLevels,
            'totalLevels' => $totalLevels,
            'overallPercent' => $overallPercent,
            'activeHours' => $activeHours,
            'nextLevel' => $nextLevel ?? $levels->first(),
        ])->title('Roadmap Belajar Client-Side: Pin Map — SMK Telkom Purwokerto');
    }
}
