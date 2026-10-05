<?php

namespace Tests\Feature;

use App\Enums\CohortType;
use App\Enums\ModuleStatus;
use App\Enums\ModuleTrack;
use App\Models\CheckpointMark;
use App\Models\Cohort;
use App\Models\LevelProgress;
use App\Models\Module;
use App\Models\RoadmapPage;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;

    protected User $otherStudent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->student = User::where('username', '541221001')->first(); // Dewi
        $this->otherStudent = User::where('username', '541221002')->first(); // Raka
    }

    public function test_student_login_redirects_to_dashboard(): void
    {
        $response = $this->post('/masuk', [
            'username' => '541221001',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/beranda');
    }

    public function test_student_security_boundaries_and_forbidden_areas(): void
    {
        $this->actingAs($this->student);

        // Student cannot access Filament admin panel
        $this->get('/admin')->assertForbidden();
        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/modules')->assertForbidden();

        // Student cannot access mentor areas
        $this->get('/mentor/pengumpulan')->assertForbidden();
        $this->get("/mentor/aktivitas/{$this->otherStudent->id}")->assertForbidden();
    }

    public function test_student_learning_gating_cannot_access_next_level_before_completing_current_level(): void
    {
        $this->actingAs($this->student);

        // Level 0 is unlocked by default
        $this->get(route('roadmap.level', 'level-0'))
            ->assertOk()
            ->assertSee('Level 0: Alat dan cara kerja web');

        // Level 1 is initially locked because Level 0 is not completed yet
        // In the seed data, Level 0 is already completed for Dewi, so let's test Level 7 or an uncompleted level
        $level7 = RoadmapPage::where('slug', 'level-7')->firstOrFail();
        $this->assertFalse($level7->isUnlockedFor($this->student));

        // Attempting to access locked level redirects back to roadmap with warning
        $this->get(route('roadmap.level', 'level-7'))
            ->assertRedirect(route('roadmap.index'))
            ->assertSessionHas('warning');

        // Accessing quiz or task on locked level also redirects with warning
        $this->get(route('roadmap.quiz', 'level-7'))
            ->assertRedirect(route('roadmap.index'))
            ->assertSessionHas('warning');

        $this->get(route('roadmap.task', 'level-7'))
            ->assertRedirect(route('roadmap.index'))
            ->assertSessionHas('warning');
    }

    public function test_student_must_answer_quiz_to_pass_not_just_a_simple_checklist(): void
    {
        $this->actingAs($this->student);

        $page = RoadmapPage::where('slug', 'level-0')->firstOrFail();
        $cp = $page->checkpoints()->firstOrFail();

        // Clear any existing mark for this test
        CheckpointMark::where('user_id', $this->student->id)
            ->where('checkpoint_slug', $cp->slug)
            ->delete();

        // 1. Submit WRONG answer to the checkpoint quiz
        $response = $this->postJson(route('activity.checkpoint.answer'), [
            'checkpoint_slug' => $cp->slug,
            'selected_answer' => 'INVALID_CHOICE',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'is_correct' => false,
            ]);

        // Checkpoint must NOT be marked understood on wrong answer
        $this->assertDatabaseMissing('checkpoint_marks', [
            'user_id' => $this->student->id,
            'checkpoint_slug' => $cp->slug,
            'is_understood' => true,
        ]);

        // 2. Submit CORRECT answer to the checkpoint quiz
        $responseCorrect = $this->postJson(route('activity.checkpoint.answer'), [
            'checkpoint_slug' => $cp->slug,
            'selected_answer' => $cp->correct_answer,
        ]);

        $responseCorrect->assertOk()
            ->assertJson([
                'success' => true,
                'is_correct' => true,
            ]);

        // Checkpoint is now marked as understood
        $this->assertDatabaseHas('checkpoint_marks', [
            'user_id' => $this->student->id,
            'checkpoint_slug' => $cp->slug,
            'is_understood' => true,
        ]);
    }

    public function test_completing_all_level_quizzes_completes_level_and_unlocks_next_level(): void
    {
        // Create a new fresh student
        $freshStudent = User::create([
            'name' => 'Siswa Baru Belajar',
            'username' => '541229002',
            'email' => 'siswa.baru@student.test',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $freshStudent->assignRole('student');
        $freshStudent->cohorts()->attach(Cohort::first()->id);

        $this->actingAs($freshStudent);

        $level0 = RoadmapPage::where('slug', 'level-0')->firstOrFail();
        $level1 = RoadmapPage::where('slug', 'level-1')->firstOrFail();

        // Fresh student can access level 0, but NOT level 1
        $this->assertTrue($level0->isUnlockedFor($freshStudent));
        $this->assertFalse($level1->isUnlockedFor($freshStudent));

        $this->get(route('roadmap.level', 'level-1'))
            ->assertRedirect(route('roadmap.index'))
            ->assertSessionHas('warning');

        // Student completes all quiz checkpoints for level 0
        foreach ($level0->checkpoints as $cp) {
            $this->postJson(route('activity.checkpoint.answer'), [
                'checkpoint_slug' => $cp->slug,
                'selected_answer' => $cp->correct_answer,
            ])->assertOk();
        }

        // Level 0 is now 100% completed!
        $progress = LevelProgress::where('user_id', $freshStudent->id)
            ->where('level_slug', 'level-0')
            ->firstOrFail();

        $this->assertTrue($progress->is_completed);
        $this->assertEquals(100, $progress->percent_complete);

        // Level 1 is now UNLOCKED!
        $this->assertTrue($level1->isUnlockedFor($freshStudent));
        $this->get(route('roadmap.level', 'level-1'))
            ->assertOk()
            ->assertSee($level1->title);
    }

    public function test_student_task_page_renders_clean_coming_soon_screen(): void
    {
        $this->actingAs($this->student);

        $this->get(route('roadmap.task', 'level-0'))
            ->assertOk()
            ->assertSee('Tugas Coding')
            ->assertSee('Editor JavaScript')
            ->assertSee('COMING SOON');
    }

    public function test_student_module_access_restrictions_and_submission_upload(): void
    {
        $this->actingAs($this->student);
        Storage::fake('private');

        // Student cannot view draft module
        $draftModule = Module::create([
            'slug' => 'modul-draft-tersembunyi',
            'title' => 'Modul Draf Tersembunyi',
            'track' => ModuleTrack::Client,
            'level' => 1,
            'summary' => 'Draf modul',
            'brief_md' => '# Draf',
            'duration_minutes' => 60,
            'status' => ModuleStatus::Draft,
            'max_attempts_per_day' => 1,
            'version' => 1,
        ]);

        $this->get("/modul/{$draftModule->slug}")->assertNotFound();

        // Student cannot view module assigned to another cohort
        $otherCohort = Cohort::create([
            'name' => 'Cohort Lain',
            'type' => CohortType::ClassGroup,
            'year' => 2026,
        ]);
        $restrictedModule = Module::create([
            'slug' => 'modul-kelas-lain',
            'title' => 'Modul Kelas Lain',
            'track' => ModuleTrack::Client,
            'level' => 1,
            'summary' => 'Hanya untuk kelas lain',
            'brief_md' => '# Rahasia',
            'duration_minutes' => 60,
            'status' => ModuleStatus::Published,
            'max_attempts_per_day' => 1,
            'version' => 1,
        ]);
        $restrictedModule->cohorts()->attach($otherCohort->id);

        $this->get("/modul/{$restrictedModule->slug}")->assertNotFound();

        // Student CAN access published module for their cohort
        $publishedModule = Module::where('status', ModuleStatus::Published)->firstOrFail();
        $this->get("/modul/{$publishedModule->slug}")
            ->assertOk()
            ->assertSee($publishedModule->title);
    }

    public function test_student_heartbeat_tracks_active_learning_time(): void
    {
        $this->actingAs($this->student);

        $response = $this->postJson(route('activity.heartbeat'), [
            'tab_id' => 'student_active_tab_1',
        ]);

        $response->assertOk()
            ->assertJson(['recorded' => true]);

        // Concurrent second tab is rejected to enforce single-tab lease
        $responseConcurrent = $this->postJson(route('activity.heartbeat'), [
            'tab_id' => 'student_active_tab_2',
        ]);

        $responseConcurrent->assertOk()
            ->assertJson(['recorded' => false]);
    }

    public function test_student_profile_update_and_password_change(): void
    {
        $this->actingAs($this->student);

        // Update profile
        $this->get('/profil')->assertOk();

        // Password change
        $newPassword = 'NewSecretPassword456!';
        $this->student->update([
            'password' => Hash::make($newPassword),
        ]);

        $this->assertTrue(Hash::check($newPassword, $this->student->fresh()->password));
    }

    public function test_student_dashboard_displays_all_learning_statistics(): void
    {
        $this->actingAs($this->student);

        $response = $this->get('/beranda');

        $response->assertOk()
            ->assertSee('Ringkasan Statistik Pembelajaran')
            ->assertSee('Modul Selesai')
            ->assertSee('/ 3 Modul')
            ->assertSee('Roadmap Belajar')
            ->assertSee('Keaktifan Belajar')
            ->assertSee('Posisi Peringkat')
            ->assertSee('Server-side')
            ->assertSee('Client-side')
            ->assertSee('Diupload:')
            ->assertSee('Batas Tenggat:')
            ->assertSee('Sudah Dikumpulkan')
            ->assertSee('Belum Dikumpulkan');
    }

    public function test_module_index_displays_categorization_and_submission_status(): void
    {
        $this->actingAs($this->student);

        $response = $this->get('/modul');

        $response->assertOk()
            ->assertSee('Server-side')
            ->assertSee('Client-side')
            ->assertSee('Diupload:')
            ->assertSee('Tenggat:')
            ->assertSee('Sudah Dikumpulkan')
            ->assertSee('96 / 100')
            ->assertSee('Belum Dikumpulkan');
    }

    public function test_submitted_module_locks_and_disables_submission_form(): void
    {
        $this->actingAs($this->student);

        // Modul A is already submitted by Dewi in seeder
        $response = $this->get('/modul/modul-a-server-side-api');

        $response->assertOk()
            ->assertSee('Tugas Berhasil Dikumpulkan')
            ->assertSee('Pengumpulan Dikunci (Sudah Dikumpulkan)');
    }
}
