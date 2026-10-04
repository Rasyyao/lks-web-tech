<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\LeaderboardEntry;
use App\Models\LevelProgress;
use App\Models\RoadmapPage;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadmapClientTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_content_import_command_imports_all_pages_and_is_idempotent(): void
    {
        $this->artisan('content:import')
            ->assertExitCode(0);

        $this->assertDatabaseHas('roadmap_pages', ['slug' => 'level-0']);
        $this->assertDatabaseHas('roadmap_pages', ['slug' => 'level-7']);
        $this->assertDatabaseHas('roadmap_pages', ['slug' => 'peta-besar']);

        $pageCount = RoadmapPage::count();
        $this->assertGreaterThanOrEqual(14, $pageCount);

        // Run again to ensure idempotency
        $this->artisan('content:import')
            ->assertExitCode(0);

        $this->assertEquals($pageCount, RoadmapPage::count());
    }

    public function test_student_can_view_roadmap_overview_and_level_page(): void
    {
        $student = User::where('username', '541221001')->first();

        $this->actingAs($student)
            ->get(route('roadmap.index'))
            ->assertOk()
            ->assertSee('Roadmap Belajar Mandiri: Pin Map')
            ->assertSee('Level 0:')
            ->assertSee('Level 7:');

        $this->actingAs($student)
            ->get(route('roadmap.level', 'level-7'))
            ->assertOk()
            ->assertSee('Level 7: Graph, BFS, DFS, dan pencarian rute')
            ->assertSee('7.1 Graph');
    }

    public function test_student_can_toggle_checkpoint_understanding(): void
    {
        $student = User::where('username', '541221001')->first();
        $cp = RoadmapPage::where('slug', 'level-1')->first()->checkpoints->first();

        $this->actingAs($student)
            ->postJson(route('activity.checkpoint.toggle'), [
                'checkpoint_slug' => $cp->slug,
                'is_understood' => true,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('checkpoint_marks', [
            'user_id' => $student->id,
            'checkpoint_slug' => $cp->slug,
            'is_understood' => true,
        ]);
    }

    public function test_submitting_exercise_attempt_records_result_and_updates_progress(): void
    {
        $student = User::where('username', '541221003')->first();
        $exercise = Exercise::where('slug', 'l3-format-durasi')->first();

        $this->actingAs($student)
            ->postJson(route('roadmap.exercise.submit', $exercise->id), [
                'code' => 'function formatDurasi(j) { return `${Math.floor(j)}j ${Math.round((j-Math.floor(j))*60)}m`; }',
                'passed' => true,
                'duration_ms' => 45,
                'results' => [['passed' => true]],
            ])
            ->assertOk()
            ->assertJson(['success' => true, 'passed' => true]);

        $this->assertDatabaseHas('exercise_attempts', [
            'user_id' => $student->id,
            'exercise_slug' => 'l3-format-durasi',
            'passed' => true,
        ]);

        $progress = LevelProgress::where('user_id', $student->id)
            ->where('level_slug', 'level-3')
            ->first();

        $this->assertNotNull($progress);
        $this->assertGreaterThan(0, $progress->exercises_passed);
    }

    public function test_heartbeat_updates_daily_active_seconds_with_single_tab_lease(): void
    {
        $student = User::where('username', '541221001')->first();

        $response1 = $this->actingAs($student)
            ->postJson(route('activity.heartbeat'), [
                'tab_id' => 'tab_alpha',
            ]);

        $response1->assertOk()
            ->assertJson(['recorded' => true]);

        // Attempt heartbeat from second concurrent tab -> should be rejected by lease
        $response2 = $this->actingAs($student)
            ->postJson(route('activity.heartbeat'), [
                'tab_id' => 'tab_beta',
            ]);

        $response2->assertOk()
            ->assertJson(['recorded' => false]);
    }

    public function test_activity_points_contribute_to_leaderboard_entry(): void
    {
        $student = User::where('username', '541221001')->first();
        $entry = LeaderboardEntry::find($student->id);

        $this->assertNotNull($entry);
        $this->assertGreaterThan(0, $entry->activity_points);
        $this->assertNotNull($entry->breakdown);
        $this->assertArrayHasKey('weight_activity', $entry->breakdown);
        $this->assertArrayHasKey('weight_modules', $entry->breakdown);
    }

    public function test_mentor_can_view_student_activity_timeline_and_add_flag(): void
    {
        $mentor = User::where('username', 'mentor1')->first();
        $student = User::where('username', '541221001')->first();

        $this->actingAs($mentor)
            ->get(route('mentor.activity.timeline', $student->id))
            ->assertOk()
            ->assertSee($student->name)
            ->assertSee('Aktivitas Siswa:');

        // Student should not have access to mentor activity route
        $this->actingAs($student)
            ->get(route('mentor.activity.timeline', $student->id))
            ->assertForbidden();
    }
}
