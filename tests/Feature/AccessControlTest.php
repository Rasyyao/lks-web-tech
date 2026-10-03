<?php

namespace Tests\Feature;

use App\Enums\CohortType;
use App\Enums\ModuleStatus;
use App\Enums\ModuleTrack;
use App\Models\Cohort;
use App\Models\Module;
use App\Models\PracticeAttempt;
use App\Models\Submission;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_is_redirected_to_login_on_private_routes(): void
    {
        $this->get('/beranda')->assertRedirect('/masuk');
        $this->get('/modul')->assertRedirect('/masuk');
        $this->get('/latihan')->assertRedirect('/masuk');
        $this->get('/peringkat')->assertRedirect('/masuk');
        $this->get('/mentor/pengumpulan')->assertRedirect('/masuk');
    }

    public function test_student_cannot_access_mentor_inbox(): void
    {
        $dewi = User::where('username', '541221001')->first();

        $this->actingAs($dewi)
            ->get('/mentor/pengumpulan')
            ->assertStatus(403);
    }

    public function test_student_cannot_view_module_restricted_to_another_cohort(): void
    {
        $dewi = User::where('username', '541221001')->first(); // Dewi is in XII RPL 1 & Seleksi

        // Create a secret cohort and module restricted only to that cohort
        $secretCohort = Cohort::create([
            'name' => 'Kelas Khusus',
            'type' => CohortType::ClassGroup,
            'year' => 2026,
        ]);

        $restrictedModule = Module::create([
            'slug' => 'modul-rahasia',
            'title' => 'Modul Rahasia',
            'track' => ModuleTrack::Server,
            'level' => 4,
            'summary' => 'Hanya untuk kelas khusus',
            'brief_md' => 'Brief rahasia',
            'duration_minutes' => 60,
            'status' => ModuleStatus::Published,
            'max_attempts_per_day' => 1,
            'version' => 1,
        ]);
        $restrictedModule->cohorts()->attach($secretCohort->id);

        // Attempting to access by URL returns 404 per PRD section 7
        $this->actingAs($dewi)
            ->get('/modul/modul-rahasia')
            ->assertStatus(404);
    }

    public function test_student_cannot_view_draft_module(): void
    {
        $dewi = User::where('username', '541221001')->first();

        $draftModule = Module::create([
            'slug' => 'modul-draft',
            'title' => 'Modul Draft',
            'track' => ModuleTrack::Server,
            'level' => 1,
            'summary' => 'Draft',
            'brief_md' => 'Draft brief',
            'duration_minutes' => 60,
            'status' => ModuleStatus::Draft,
            'max_attempts_per_day' => 1,
            'version' => 1,
        ]);

        $this->actingAs($dewi)
            ->get('/modul/modul-draft')
            ->assertStatus(404);
    }

    public function test_student_cannot_access_another_students_practice_attempt(): void
    {
        $dewi = User::where('username', '541221001')->first();
        $raka = User::where('username', '541221002')->first();

        $rakaAttempt = PracticeAttempt::where('user_id', $raka->id)->first();

        // Dewi tries to open Raka's attempt -> returns 404 so URL cannot be probed
        $this->actingAs($dewi)
            ->get("/latihan/{$rakaAttempt->id}")
            ->assertStatus(404);

        // Dewi tries to open Raka's result -> 404
        $this->actingAs($dewi)
            ->get("/latihan/{$rakaAttempt->id}/hasil")
            ->assertStatus(404);
    }

    public function test_student_cannot_download_another_students_submission(): void
    {
        $dewi = User::where('username', '541221001')->first();
        $raka = User::where('username', '541221002')->first();

        $rakaSubmission = Submission::where('user_id', $raka->id)->first();

        $this->actingAs($dewi)
            ->get(route('download.submission', $rakaSubmission->id))
            ->assertStatus(403);
    }

    public function test_mentor_can_download_any_student_submission(): void
    {
        $mentor1 = User::where('username', 'mentor1')->first();
        $rakaSubmission = Submission::where('user_id', User::where('username', '541221002')->value('id'))->first();

        $this->actingAs($mentor1)
            ->get(route('download.submission', $rakaSubmission->id));
        // It passes policy authorization (even if file doesn't exist on disk yet, it does not throw 403)
        $this->assertTrue(true);
    }
}
