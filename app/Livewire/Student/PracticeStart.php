<?php

namespace App\Livewire\Student;

use App\Models\PracticeAttempt;
use App\Models\Topic;
use App\Services\Practice\AttemptBuilder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Mulai Latihan')]
class PracticeStart extends Component
{
    public string $topic = 'all';
    public string $difficulty = 'all';
    public int $questionCount = 10;
    public bool $useTimer = false;
    public int $timerMinutes = 15;

    public function mount(): void
    {
        if (request()->has('topic')) {
            $this->topic = (string) request('topic');
        }
    }

    public function start(AttemptBuilder $builder)
    {
        $user = Auth::user();

        $filters = [];
        if ($this->topic !== 'all' && is_numeric($this->topic)) {
            $filters['topic_ids'] = [(int) $this->topic];
        }

        if ($this->difficulty !== 'all' && is_numeric($this->difficulty)) {
            $diff = (int) $this->difficulty;
            $filters['difficulty_min'] = $diff;
            $filters['difficulty_max'] = $diff;
        }

        $timeLimitSeconds = $this->useTimer ? ($this->timerMinutes * 60) : null;

        $attempt = $builder->build(
            user: $user,
            questionCount: $this->questionCount,
            filters: $filters,
            timeLimitSeconds: $timeLimitSeconds
        );

        if ($attempt->items()->count() === 0) {
            $attempt->delete();
            $this->addError('general', __('practice.no_questions_found'));
            return;
        }

        return redirect()->route('practice.attempt', $attempt->id);
    }

    public function render()
    {
        $user = Auth::user();

        $topics = Topic::withCount(['questions' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('position')
            ->get();

        $recentAttempts = PracticeAttempt::query()
            ->where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->take(5)
            ->get();

        return view('livewire.student.practice-start', [
            'topics' => $topics,
            'recentAttempts' => $recentAttempts,
        ]);
    }
}
