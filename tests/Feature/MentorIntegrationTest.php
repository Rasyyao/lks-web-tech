<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Livewire\Mentor\StudentActivityTimeline;
use App\Livewire\Mentor\SubmissionInbox;
use App\Livewire\Mentor\SubmissionReview;
use App\Models\ActivityFlag;
use App\Models\Cohort;
use App\Models\Module;
use App\Models\Submission;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MentorIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $mentor;

    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->mentor = User::where('username', 'mentor1')->first();
        $this->student = User::where('username', '541221001')->first();
    }

    public function test_mentor_login_redirects_to_submission_inbox(): void
    {
        $response = $this->post('/masuk', [
            'username' => 'mentor1',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/mentor/pengumpulan');
    }

    public function test_mentor_cannot_access_filament_admin_panel(): void
    {
        $this->actingAs($this->mentor);

        $this->get('/admin')->assertForbidden();
        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/modules')->assertForbidden();
        $this->get('/admin/cohorts')->assertForbidden();
        $this->get('/admin/audit-logs')->assertForbidden();
    }

    public function test_mentor_views_submission_inbox_and_filters_submissions(): void
    {
        $this->actingAs($this->mentor);

        // Access the submission inbox view directly
        $this->get('/mentor/pengumpulan')
            ->assertOk()
            ->assertSee('Daftar Pengumpulan');

        $module = Module::first();
        $cohort = Cohort::first();

        // Test Livewire component reactivity and filtering
        Livewire::test(SubmissionInbox::class)
            ->assertOk()
            ->assertViewHas('submissions')
            ->assertViewHas('pendingCount')
            ->assertViewHas('gradedCount')
            ->set('status', 'graded')
            ->assertOk()
            ->set('module_id', (string) $module->id)
            ->assertOk()
            ->set('cohort_id', (string) $cohort->id)
            ->assertOk();
    }

    public function test_mentor_reviews_and_grades_student_submission(): void
    {
        $this->actingAs($this->mentor);
        Storage::fake('private');

        $module = Module::first();

        // Create a pending submission for the student
        $fakeZip = UploadedFile::fake()->create('jawaban-proyek.zip', 200, 'application/zip');
        $storedPath = $fakeZip->store('submissions', 'private');

        $submission = Submission::create([
            'user_id' => $this->student->id,
            'module_id' => $module->id,
            'path' => $storedPath,
            'original_name' => 'jawaban-proyek.zip',
            'size_bytes' => 200 * 1024,
            'mime_type' => 'application/zip',
            'sha256' => hash('sha256', 'dummy-zip-content'),
            'status' => SubmissionStatus::Received,
            'attempt_number' => 1,
            'submitted_at' => now(),
        ]);

        // Mentor opens the review page
        $this->get("/mentor/pengumpulan/{$submission->id}")
            ->assertOk()
            ->assertSee($this->student->name)
            ->assertSee($module->title);

        // Mentor grades the submission using the Livewire review component
        Livewire::test(SubmissionReview::class, ['submission' => $submission])
            ->set('status', 'graded')
            ->set('manual_score', 92)
            ->set('feedback_md', 'Struktur folder rapi, semantic HTML sangat baik dan CSS responsive berjalan sempurna.')
            ->call('saveReview')
            ->assertHasNoErrors();

        $submission->refresh();
        $this->assertEquals(SubmissionStatus::Graded, $submission->status);
        $this->assertEquals(92, $submission->manual_score);
        $this->assertEquals('Struktur folder rapi, semantic HTML sangat baik dan CSS responsive berjalan sempurna.', $submission->feedback_md);
        $this->assertEquals($this->mentor->id, $submission->reviewed_by);
        $this->assertNotNull($submission->reviewed_at);

        // Verify audit log recorded
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'submission.reviewed',
            'actor_id' => $this->mentor->id,
            'subject_id' => $submission->id,
        ]);
    }

    public function test_mentor_rejects_submission_with_required_reason(): void
    {
        $this->actingAs($this->mentor);
        Storage::fake('private');

        $module = Module::first();
        $submission = Submission::create([
            'user_id' => $this->student->id,
            'module_id' => $module->id,
            'path' => 'submissions/broken.zip',
            'original_name' => 'broken.zip',
            'size_bytes' => 100,
            'mime_type' => 'application/zip',
            'sha256' => hash('sha256', 'broken-zip'),
            'status' => SubmissionStatus::Received,
            'attempt_number' => 1,
            'submitted_at' => now(),
        ]);

        // Trying to reject without feedback fails validation
        Livewire::test(SubmissionReview::class, ['submission' => $submission])
            ->set('status', 'rejected')
            ->set('feedback_md', '')
            ->call('saveReview')
            ->assertHasErrors(['feedback_md']);

        // Providing valid rejection reason succeeds
        Livewire::test(SubmissionReview::class, ['submission' => $submission])
            ->set('status', 'rejected')
            ->set('feedback_md', 'File ZIP kosong atau corrupt, silakan periksa dan unggah kembali arsip proyek yang valid.')
            ->call('saveReview')
            ->assertHasNoErrors();

        $submission->refresh();
        $this->assertEquals(SubmissionStatus::Rejected, $submission->status);
        $this->assertNull($submission->manual_score);
    }

    public function test_mentor_monitors_student_activity_and_flags_intervention(): void
    {
        $this->actingAs($this->mentor);

        // View student activity timeline
        $this->get("/mentor/aktivitas/{$this->student->id}")
            ->assertOk()
            ->assertSee($this->student->name)
            ->assertSee('Aktivitas Siswa:');

        // Flag student needing attention
        Livewire::test(StudentActivityTimeline::class, ['user' => $this->student])
            ->set('flagReason', 'Siswa mengalami kesulitan pada kuis seleksi tahap 2.')
            ->call('flagActivity')
            ->assertHasNoErrors();

        $flag = ActivityFlag::where('user_id', $this->student->id)->latest('id')->firstOrFail();
        $this->assertEquals('Siswa mengalami kesulitan pada kuis seleksi tahap 2.', $flag->reason);
        $this->assertNull($flag->resolved_at);

        // Resolve the flag after intervention
        Livewire::test(StudentActivityTimeline::class, ['user' => $this->student])
            ->call('resolveFlag', $flag->id)
            ->assertHasNoErrors();

        $flag->refresh();
        $this->assertNotNull($flag->resolved_at);
        $this->assertEquals($this->mentor->id, $flag->resolved_by);
    }

    public function test_mentor_can_access_question_bank_and_leaderboard(): void
    {
        $this->actingAs($this->mentor);

        $this->get('/bank-soal')
            ->assertOk()
            ->assertSee('Bank Soal');

        $this->get('/peringkat')
            ->assertOk()
            ->assertSee('Papan Peringkat');

        $this->get('/profil')
            ->assertOk()
            ->assertSee($this->mentor->name);
    }
}
