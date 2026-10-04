<?php

namespace Tests\Feature;

use App\Models\LeaderboardEntry;
use App\Models\Question;
use App\Models\User;
use App\Services\Leaderboard\RecomputeLeaderboard;
use App\Services\Practice\AttemptBuilder;
use App\Services\Practice\AttemptScorer;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_distinct_questions_scored_only_once_for_leaderboard(): void
    {
        $dimas = User::where('username', '541221003')->first();
        $question = Question::first();

        $builder = new AttemptBuilder;
        $scorer = new AttemptScorer;
        $recompute = new RecomputeLeaderboard;

        // Attempt 1: Dimas answers $question correctly
        $attempt1 = $builder->build($dimas, 1, ['topic_ids' => [$question->topic_id]]);
        $item1 = $attempt1->items->first();

        if (! empty($item1->snapshot['options'])) {
            $correctOpt = collect($item1->snapshot['options'])->firstWhere('is_correct', true);
            $answer = ['option_id' => $correctOpt['id']];
        } else {
            $acc = $item1->snapshot['accepted_answers'][0]['text'] ?? 'margin';
            $answer = ['text' => $acc];
        }

        $item1->update(['answer' => $answer]);
        $scorer->score($attempt1);

        $recompute->forUser($dimas->id);
        $entry1 = LeaderboardEntry::find($dimas->id);
        $initialQPoints = $entry1->question_points;
        $this->assertGreaterThan(0, $initialQPoints);

        // Attempt 2: Dimas answers the SAME question again correctly
        // (Force item to point to the same question)
        $attempt2 = $builder->build($dimas, 1, ['topic_ids' => [$question->topic_id]]);
        $item2 = $attempt2->items->first();
        $item2->update([
            'question_id' => $item1->question_id,
            'answer' => $answer,
        ]);
        $scorer->score($attempt2);

        $recompute->forUser($dimas->id);
        $entry2 = LeaderboardEntry::find($dimas->id);

        // Score must NOT double; it must remain the same distinct points!
        $this->assertEquals($initialQPoints, $entry2->question_points);
    }

    public function test_tie_breaking_prioritizes_earlier_reached_at(): void
    {
        $dewi = User::where('username', '541221001')->first();
        $raka = User::where('username', '541221002')->first();

        // Set identical points for both
        LeaderboardEntry::updateOrCreate(
            ['user_id' => $dewi->id],
            ['total' => 200, 'question_points' => 100, 'module_points' => 100, 'reached_at' => now()->subHours(2)]
        );

        LeaderboardEntry::updateOrCreate(
            ['user_id' => $raka->id],
            ['total' => 200, 'question_points' => 100, 'module_points' => 100, 'reached_at' => now()->subHour()]
        );

        $topEntries = LeaderboardEntry::orderByDesc('total')->orderBy('reached_at')->get();

        // Dewi reached 200 earlier than Raka, so Dewi must be rank 1
        $this->assertEquals($dewi->id, $topEntries->first()->user_id);
        $this->assertEquals($raka->id, $topEntries->get(1)->user_id);
    }
}
