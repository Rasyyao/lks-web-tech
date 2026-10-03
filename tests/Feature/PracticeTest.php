<?php

namespace Tests\Feature;

use App\Enums\QuestionType;
use App\Models\Question;
use App\Models\User;
use App\Services\Practice\AnswerNormalizer;
use App\Services\Practice\AttemptBuilder;
use App\Services\Practice\AttemptScorer;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PracticeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_practice_attempt_stores_question_snapshot_immutably(): void
    {
        $dewi = User::where('username', '541221001')->first();
        $builder = new AttemptBuilder();
        $scorer = new AttemptScorer();

        $attempt = $builder->build($dewi, 3);
        $item = $attempt->items->first();
        $question = Question::find($item->question_id);

        $originalBody = $question->body_md;
        $this->assertEquals($originalBody, $item->snapshot['body_md']);

        // Now an author edits the question in the bank soal
        $question->update([
            'body_md' => 'Teks soal telah diperbarui secara radikal oleh mentor.',
            'points' => 999,
        ]);

        // Attempt item snapshot must NOT change
        $item->refresh();
        $this->assertEquals($originalBody, $item->snapshot['body_md']);
        $this->assertNotEquals(999, $item->snapshot['points']);
    }

    public function test_short_answer_normalizer_handles_spaces_and_casing(): void
    {
        $accepted = ['flexbox', 'display: flex'];

        $this->assertTrue(AnswerNormalizer::matches('flexbox', $accepted));
        $this->assertTrue(AnswerNormalizer::matches('  FLEXBOX  ', $accepted));
        $this->assertTrue(AnswerNormalizer::matches('display:   flex', $accepted));
        $this->assertFalse(AnswerNormalizer::matches('grid', $accepted));
    }

    public function test_attempt_scorer_grades_snapshot_correctly(): void
    {
        $dimas = User::where('username', '541221003')->first();
        $builder = new AttemptBuilder();
        $scorer = new AttemptScorer();

        $attempt = $builder->build($dimas, 2);

        foreach ($attempt->items as $item) {
            $type = QuestionType::from($item->snapshot['type']);
            if ($type === QuestionType::MultipleChoice || $type === QuestionType::TrueFalse) {
                $correctOpt = collect($item->snapshot['options'])->firstWhere('is_correct', true);
                $item->update(['answer' => ['option_id' => $correctOpt['id']]]);
            } elseif ($type === QuestionType::ShortAnswer) {
                $acc = $item->snapshot['accepted_answers'][0]['text'] ?? '';
                $item->update(['answer' => ['text' => $acc]]);
            }
        }

        $scorer->score($attempt);

        $attempt->refresh();
        $this->assertNotNull($attempt->submitted_at);
        $this->assertGreaterThan(0, $attempt->points_earned);
        $this->assertEquals(100.0, (float) $attempt->score_pct);
    }
}
