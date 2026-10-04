<?php

namespace App\Livewire\Student;

use App\Enums\QuestionType;
use App\Models\PracticeAttempt as PracticeAttemptModel;
use App\Models\PracticeAttemptItem;
use App\Services\Practice\AttemptScorer;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class PracticeAttempt extends Component
{
    public PracticeAttemptModel $attempt;

    public int $currentIndex = 0;

    public bool $isReviewMode = false;

    public ?int $selectedOptionId = null;

    public string $shortAnswerText = '';

    public function mount(PracticeAttempt $attempt, AttemptScorer $scorer): void
    {
        $this->attempt = $attempt->load(['items' => fn ($q) => $q->orderBy('position')]);

        // Auto-submit if expired
        if ($this->attempt->isTimerExpired()) {
            $scorer->score($this->attempt);
            $this->redirectRoute('practice.result', $this->attempt->id);

            return;
        }

        $this->loadCurrentItemState();
    }

    public function loadCurrentItemState(): void
    {
        $item = $this->currentItem;
        if (! $item) {
            return;
        }

        $answer = $item->answer;
        $this->selectedOptionId = $answer['option_id'] ?? null;
        $this->shortAnswerText = $answer['text'] ?? '';
    }

    public function getCurrentItemProperty(): ?PracticeAttemptItem
    {
        return $this->attempt->items->get($this->currentIndex);
    }

    public function selectOption(int $optionId, AttemptScorer $scorer): void
    {
        $this->checkTimer($scorer);

        $this->selectedOptionId = $optionId;
        $this->saveAnswer();
    }

    public function updatedShortAnswerText(): void
    {
        $this->saveAnswer();
    }

    public function saveAnswer(): void
    {
        $item = $this->currentItem;
        if (! $item) {
            return;
        }

        $type = QuestionType::from($item->snapshot['type']);
        $answerData = [];

        if ($type === QuestionType::MultipleChoice || $type === QuestionType::TrueFalse) {
            if ($this->selectedOptionId !== null) {
                $answerData = ['option_id' => $this->selectedOptionId];
            }
        } elseif ($type === QuestionType::ShortAnswer) {
            if (trim($this->shortAnswerText) !== '') {
                $answerData = ['text' => trim($this->shortAnswerText)];
            }
        }

        $item->update(['answer' => ! empty($answerData) ? $answerData : null]);

        // Refresh model in memory
        $this->attempt->load(['items' => fn ($q) => $q->orderBy('position')]);
    }

    public function nextQuestion(AttemptScorer $scorer): void
    {
        $this->checkTimer($scorer);
        $this->saveAnswer();

        if ($this->currentIndex < $this->attempt->items->count() - 1) {
            $this->currentIndex++;
            $this->isReviewMode = false;
            $this->loadCurrentItemState();
        } else {
            $this->isReviewMode = true;
        }
    }

    public function prevQuestion(AttemptScorer $scorer): void
    {
        $this->checkTimer($scorer);
        $this->saveAnswer();

        if ($this->isReviewMode) {
            $this->isReviewMode = false;
            $this->loadCurrentItemState();
        } elseif ($this->currentIndex > 0) {
            $this->currentIndex--;
            $this->loadCurrentItemState();
        }
    }

    public function goToQuestion(int $index, AttemptScorer $scorer): void
    {
        $this->checkTimer($scorer);
        $this->saveAnswer();

        if ($index >= 0 && $index < $this->attempt->items->count()) {
            $this->currentIndex = $index;
            $this->isReviewMode = false;
            $this->loadCurrentItemState();
        }
    }

    public function openReview(AttemptScorer $scorer): void
    {
        $this->checkTimer($scorer);
        $this->saveAnswer();
        $this->isReviewMode = true;
    }

    public function finishAttempt(AttemptScorer $scorer): void
    {
        $this->saveAnswer();
        $scorer->score($this->attempt);

        $this->redirectRoute('practice.result', $this->attempt->id);
    }

    public function checkTimer(AttemptScorer $scorer): void
    {
        if ($this->attempt->isTimerExpired()) {
            $scorer->score($this->attempt);
            session()->flash('warning', __('practice.auto_submitted'));
            $this->redirectRoute('practice.result', $this->attempt->id);
        }
    }

    public function render(AttemptScorer $scorer)
    {
        $this->checkTimer($scorer);

        $remainingSeconds = $this->attempt->remainingSeconds();

        return view('livewire.student.practice-attempt', [
            'currentItem' => $this->currentItem,
            'items' => $this->attempt->items,
            'remainingSeconds' => $remainingSeconds,
        ])->title(__('practice.attempt_title'));
    }
}
