<?php

namespace App\Livewire\Student;

use App\Models\CheckpointMark;
use App\Models\ExerciseAttempt;
use App\Models\RoadmapPage;
use App\Models\SectionRead;
use App\Services\Roadmap\ProgressTracker;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class RoadmapLevel extends Component
{
    public string $slug;

    public ?RoadmapPage $page = null;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->page = RoadmapPage::where('slug', $slug)
            ->where('kind', 'level')
            ->with(['sections', 'checkpoints', 'exercises'])
            ->firstOrFail();

        // Mark overview/first section as visited on mount
        $user = Auth::user();
        if ($user && $this->page->sections->isNotEmpty()) {
            app(ProgressTracker::class)->markSectionRead($user, $this->page->sections->first()->slug);
        }
    }

    public function toggleCheckpoint(string $checkpointSlug, ProgressTracker $tracker): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $current = CheckpointMark::where('user_id', $user->id)
            ->where('checkpoint_slug', $checkpointSlug)
            ->first();

        $newState = ! ($current?->is_understood ?? false);
        $tracker->toggleCheckpoint($user, $checkpointSlug, $newState);
    }

    public function markSectionRead(string $sectionSlug, ProgressTracker $tracker): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $tracker->markSectionRead($user, $sectionSlug);
    }

    public function render(ProgressTracker $tracker): View
    {
        $user = Auth::user();

        // Recompute user progress for this level to get latest stats
        $progress = $tracker->recomputeLevelProgress($user, $this->slug);

        $readSectionSlugs = SectionRead::where('user_id', $user->id)
            ->whereIn('roadmap_section_id', $this->page->sections->pluck('id'))
            ->pluck('section_slug')
            ->toArray();

        $markedCheckpointSlugs = CheckpointMark::where('user_id', $user->id)
            ->whereIn('roadmap_checkpoint_id', $this->page->checkpoints->pluck('id'))
            ->where('is_understood', true)
            ->pluck('checkpoint_slug')
            ->toArray();

        $passedExerciseSlugs = ExerciseAttempt::where('user_id', $user->id)
            ->whereIn('exercise_id', $this->page->exercises->pluck('id'))
            ->where('passed', true)
            ->pluck('exercise_slug')
            ->unique()
            ->toArray();

        // Get adjacent levels for navigation
        $allLevels = RoadmapPage::where('kind', 'level')->orderBy('position')->get();
        $currentIndex = $allLevels->search(fn ($p) => $p->id === $this->page->id);
        $prevLevel = $currentIndex > 0 ? $allLevels->get($currentIndex - 1) : null;
        $nextLevel = $currentIndex < ($allLevels->count() - 1) ? $allLevels->get($currentIndex + 1) : null;

        return view('livewire.student.roadmap-level', [
            'page' => $this->page,
            'progress' => $progress,
            'readSectionSlugs' => $readSectionSlugs,
            'markedCheckpointSlugs' => $markedCheckpointSlugs,
            'passedExerciseSlugs' => $passedExerciseSlugs,
            'prevLevel' => $prevLevel,
            'nextLevel' => $nextLevel,
        ])->title($this->page->title.' — LKS Web Technology');
    }
}
