<?php

namespace App\Services\Practice;

use App\Enums\QuestionType;
use App\Jobs\RecomputeLeaderboard as RecomputeLeaderboardJob;
use App\Models\PracticeAttempt;
use App\Models\PracticeAttemptItem;
use App\Models\QuestionAnswer;

class AttemptScorer
{
    /**
     * Score a practice attempt: grade each item, compute totals, mark as submitted.
     */
    public function score(PracticeAttempt $attempt): void
    {
        if ($attempt->submitted_at !== null) {
            return;
        }

        $totalPoints = 0;
        $maxPoints = 0;

        foreach ($attempt->items as $item) {
            $isCorrect = $this->gradeItem($item);
            $points = $isCorrect ? ($item->snapshot['points'] ?? 0) : 0;

            $item->update([
                'is_correct' => $isCorrect,
                'points' => $points,
            ]);

            $totalPoints += $points;
            $maxPoints += ($item->snapshot['points'] ?? 0);
        }

        $scorePct = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 2) : 0;

        $attempt->update([
            'submitted_at' => now(),
            'points_earned' => $totalPoints,
            'score_pct' => $scorePct,
        ]);

        // Dispatch leaderboard recompute
        RecomputeLeaderboardJob::dispatch($attempt->user_id);
    }

    /**
     * Grade a single attempt item based on its type and snapshot.
     */
    private function gradeItem(PracticeAttemptItem $item): bool
    {
        $answer = $item->answer;

        if (empty($answer)) {
            return false;
        }

        $type = QuestionType::from($item->snapshot['type']);

        return match ($type) {
            QuestionType::MultipleChoice, QuestionType::TrueFalse => $this->gradeChoice($item, $answer),
            QuestionType::ShortAnswer => $this->gradeShortAnswer($item, $answer),
        };
    }

    /**
     * Grade a multiple choice or true/false answer.
     */
    private function gradeChoice(PracticeAttemptItem $item, array $answer): bool
    {
        $selectedOptionId = $answer['option_id'] ?? null;

        if (! $selectedOptionId) {
            return false;
        }

        $options = $item->snapshot['options'] ?? [];

        foreach ($options as $option) {
            if ($option['id'] == $selectedOptionId && $option['is_correct']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Grade a short answer by normalizing and comparing against accepted answers.
     */
    private function gradeShortAnswer(PracticeAttemptItem $item, array $answer): bool
    {
        $studentAnswer = $answer['text'] ?? '';

        if (trim($studentAnswer) === '') {
            return false;
        }

        $normalized = QuestionAnswer::normalize($studentAnswer);
        $acceptedAnswers = $item->snapshot['accepted_answers'] ?? [];

        foreach ($acceptedAnswers as $accepted) {
            if ($accepted['normalized'] === $normalized) {
                return true;
            }
        }

        return false;
    }
}
