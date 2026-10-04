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
class RoadmapQuiz extends Component
{
    public string $slug;

    public ?RoadmapPage $page = null;

    public int $currentIndex = 0;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->page = RoadmapPage::where('slug', $slug)
            ->where('kind', 'level')
            ->with(['checkpoints', 'exercises'])
            ->firstOrFail();

        $user = Auth::user();
        if ($user && ! $this->page->isUnlockedFor($user)) {
            session()->flash('warning', 'Level ini masih terkunci. Anda harus menyelesaikan Level '.($this->page->position - 1).' terlebih dahulu.');
            $this->redirect(route('roadmap.index'), navigate: true);

            return;
        }
    }

    public function setQuestion(int $index): void
    {
        if ($index >= 0 && $this->page && $index < $this->page->checkpoints->count()) {
            $this->currentIndex = $index;
        }
    }

    public function nextQuestion(): void
    {
        if ($this->page && $this->currentIndex < $this->page->checkpoints->count() - 1) {
            $this->currentIndex++;
        }
    }

    public function prevQuestion(): void
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        }
    }

    public function answerQuiz(string $checkpointSlug, string $selectedAnswer, ProgressTracker $tracker): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $result = $tracker->answerCheckpoint($user, $checkpointSlug, $selectedAnswer);

        if ($result['is_correct']) {
            session()->flash('quiz_feedback_'.$checkpointSlug, [
                'is_correct' => true,
                'message' => 'Jawaban Benar! '.($result['explanation'] ?? ''),
            ]);
        } else {
            session()->flash('quiz_feedback_'.$checkpointSlug, [
                'is_correct' => false,
                'message' => 'Jawaban Belum Tepat. '.($result['explanation'] ?? 'Coba pelajari kembali materi terkait.'),
            ]);
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

        $passedExerciseCount = ExerciseAttempt::where('user_id', $user->id)
            ->whereIn('exercise_id', $this->page->exercises->pluck('id'))
            ->where('passed', true)
            ->distinct('exercise_id')
            ->count('exercise_id');

        $allLevels = RoadmapPage::where('kind', 'level')->orderBy('position')->get();
        $currentIndex = $allLevels->search(fn ($p) => $p->id === $this->page->id);
        $nextLevel = $currentIndex < ($allLevels->count() - 1) ? $allLevels->get($currentIndex + 1) : null;

        return view('livewire.student.roadmap-quiz', [
            'page' => $this->page,
            'progress' => $progress,
            'checkpointMarks' => $checkpointMarks,
            'markedCheckpointSlugs' => $markedCheckpointSlugs,
            'passedExerciseCount' => $passedExerciseCount,
            'nextLevel' => $nextLevel,
        ])->title('Kuis: '.$this->page->title.' — LKS Web Technology');
    }
}
