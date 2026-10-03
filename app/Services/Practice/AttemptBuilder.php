<?php

namespace App\Services\Practice;

use App\Enums\QuestionStatus;
use App\Models\PracticeAttempt;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Collection;

class AttemptBuilder
{
    /**
     * Build a new practice attempt for a student.
     *
     * @param array{topic_ids?: array, difficulty_min?: int, difficulty_max?: int} $filters
     */
    public function build(
        User $user,
        int $questionCount,
        array $filters = [],
        ?int $timeLimitSeconds = null,
    ): PracticeAttempt {
        $questions = $this->drawQuestions($questionCount, $filters);

        $attempt = PracticeAttempt::create([
            'user_id' => $user->id,
            'filters' => $filters,
            'question_count' => $questions->count(),
            'time_limit_seconds' => $timeLimitSeconds,
            'started_at' => now(),
        ]);

        foreach ($questions as $index => $question) {
            $attempt->items()->create([
                'question_id' => $question->id,
                'question_version' => $question->version,
                'snapshot' => $question->buildSnapshot(),
                'position' => $index,
            ]);
        }

        return $attempt;
    }

    /**
     * Draw random published questions matching the filters.
     */
    private function drawQuestions(int $count, array $filters): Collection
    {
        $query = Question::where('status', QuestionStatus::Published);

        if (! empty($filters['topic_ids'])) {
            $query->whereIn('topic_id', $filters['topic_ids']);
        }

        if (isset($filters['difficulty_min'])) {
            $query->where('difficulty', '>=', $filters['difficulty_min']);
        }

        if (isset($filters['difficulty_max'])) {
            $query->where('difficulty', '<=', $filters['difficulty_max']);
        }

        return $query->with(['options', 'answers'])
            ->inRandomOrder()
            ->limit($count)
            ->get();
    }
}
