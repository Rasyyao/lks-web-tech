<?php

namespace App\Livewire\Student;

use App\Models\PracticeAttempt;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AnswerSheet extends Component
{
    public PracticeAttempt $attempt;

    public function mount(PracticeAttempt $attempt): void
    {
        $this->attempt = $attempt->load(['items' => fn ($q) => $q->orderBy('position')]);
    }

    public function render()
    {
        $items = $this->attempt->items;

        $correctCount = $items->where('is_correct', true)->count();
        $wrongCount = $items->where('is_correct', false)->count();

        // Wrong answers sorted first, then correct ones per DESIGN_RULES section 5
        $sortedItems = $items->sortBy([
            fn ($a, $b) => ($a->is_correct ? 1 : 0) <=> ($b->is_correct ? 1 : 0),
            ['position', 'asc'],
        ]);

        $durationMinutes = 0;
        if ($this->attempt->started_at && $this->attempt->submitted_at) {
            $durationMinutes = max(1, (int) round($this->attempt->started_at->diffInMinutes($this->attempt->submitted_at)));
        }

        return view('livewire.student.answer-sheet', [
            'sortedItems' => $sortedItems,
            'correctCount' => $correctCount,
            'wrongCount' => $wrongCount,
            'durationMinutes' => $durationMinutes,
        ])->title(__('practice.result_title'));
    }
}
