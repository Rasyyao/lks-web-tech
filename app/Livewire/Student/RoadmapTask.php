<?php

namespace App\Livewire\Student;

use App\Models\CheckpointMark;
use App\Models\ExerciseAttempt;
use App\Models\RoadmapPage;
use App\Services\Roadmap\ProgressTracker;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class RoadmapTask extends Component
{
    public string $slug;

    public ?RoadmapPage $page = null;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->page = RoadmapPage::where('slug', $slug)
            ->where('kind', 'level')
            ->with(['exercises', 'checkpoints'])
            ->firstOrFail();

        $user = Auth::user();
        if ($user && ! $this->page->isUnlockedFor($user)) {
            session()->flash('warning', 'Level ini masih terkunci. Anda harus menyelesaikan Level '.($this->page->position - 1).' terlebih dahulu.');
            $this->redirect(route('roadmap.index'), navigate: true);

            return;
        }
    }

    public function render(ProgressTracker $tracker): View
    {
        $user = Auth::user();

        $progress = $tracker->recomputeLevelProgress($user, $this->slug);

        $checkpointMarks = CheckpointMark::where('user_id', $user->id)
            ->whereIn('roadmap_checkpoint_id', $this->page->checkpoints->pluck('id'))
            ->get()
            ->keyBy('checkpoint_slug');

        $markedCheckpointSlugs = $checkpointMarks->where('is_understood', true)
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
        $nextLevel = $currentIndex < ($allLevels->count() - 1) ? $allLevels->get($currentIndex + 1) : null;

        return view('livewire.student.roadmap-task', [
            'page' => $this->page,
            'progress' => $progress,
            'markedCheckpointSlugs' => $markedCheckpointSlugs,
            'passedExerciseSlugs' => $passedExerciseSlugs,
            'nextLevel' => $nextLevel,
        ])->title('Tugas Coding: '.$this->page->title.' — LKS Web Technology');
    }
}
