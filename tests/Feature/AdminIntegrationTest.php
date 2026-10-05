<?php

namespace Tests\Feature;

use App\Enums\CohortType;
use App\Enums\ModuleStatus;
use App\Enums\ModuleTrack;
use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use App\Filament\Resources\Cohorts\Pages\CreateCohort;
use App\Filament\Resources\Modules\Pages\CreateModule;
use App\Filament\Resources\Questions\Pages\CreateQuestion;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Models\Cohort;
use App\Models\Module;
use App\Models\Question;
use App\Models\Setting;
use App\Models\Topic;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('username', 'admin')->first();
        Filament::setCurrentPanel('admin');
    }

    public function test_admin_authentication_and_panel_access(): void
    {
        // Admin logs in and is redirected to admin panel
        $response = $this->post('/masuk', [
            'username' => 'admin',
            'password' => 'password123',
        ]);
        $response->assertRedirect(url('/admin'));

        // Admin can access all resource indexes
        $this->actingAs($this->admin);
        $this->get('/admin')->assertOk();
        $this->get('/admin/users')->assertOk();
        $this->get('/admin/cohorts')->assertOk();
        $this->get('/admin/modules')->assertOk();
        $this->get('/admin/questions')->assertOk();
        $this->get('/admin/question-bank-papers')->assertOk();
        $this->get('/admin/announcements')->assertOk();
        $this->get('/admin/audit-logs')->assertOk();
        $this->get('/admin/manage-settings')->assertOk();
    }

    public function test_admin_user_management_flow(): void
    {
        $this->actingAs($this->admin);
        $cohort = Cohort::first();
        $studentRole = Role::findByName('student');
        $mentorRole = Role::findByName('mentor');

        // 1. Admin creates a new student assigned to a cohort
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Siswa Integrasi Baru',
                'username' => '541229001',
                'email' => 'siswa.integrasi@student.smktelkom-pwt.sch.id',
                'password' => 'password123',
                'roles' => [$studentRole->id],
                'cohorts' => [$cohort->id],
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $student = User::where('username', '541229001')->firstOrFail();
        $this->assertTrue($student->hasRole('student'));
        $this->assertTrue($student->cohorts->contains($cohort));
        $this->assertTrue(Hash::check('password123', $student->password));

        // 2. Admin creates a new mentor
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Mentor Integrasi Baru',
                'username' => 'mentor_integrasi',
                'email' => 'mentor.integrasi@smktelkom-pwt.sch.id',
                'password' => 'password123',
                'roles' => [$mentorRole->id],
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $mentor = User::where('username', 'mentor_integrasi')->firstOrFail();
        $this->assertTrue($mentor->hasRole('mentor'));

        // 3. Verify audit logs recorded for user creation
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.created',
            'subject_id' => $student->id,
        ]);
    }

    public function test_admin_cohort_management_flow(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreateCohort::class)
            ->fillForm([
                'name' => 'Angkatan LKS Nasional 2026',
                'type' => CohortType::Selection->value,
                'year' => 2026,
                'description' => 'Grup bimbingan intensif persiapan lomba nasional.',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $cohort = Cohort::where('name', 'Angkatan LKS Nasional 2026')->firstOrFail();
        $this->assertEquals(CohortType::Selection, $cohort->type);
        $this->assertEquals(2026, $cohort->year);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'cohort.created',
            'subject_id' => $cohort->id,
        ]);
    }

    public function test_admin_module_management_with_cohort_restriction_and_assets(): void
    {
        $this->actingAs($this->admin);
        Storage::fake('private');

        $cohort = Cohort::first();

        // 1. Create a module with cohort restriction
        Livewire::test(CreateModule::class)
            ->fillForm([
                'title' => 'Modul REST API LKS Super',
                'slug' => 'modul-rest-api-lks-super',
                'track' => ModuleTrack::Server->value,
                'level' => 3,
                'duration_minutes' => 120,
                'summary' => 'Ringkasan modul integrasi server',
                'brief_md' => '# Brief Modul REST API',
                'status' => ModuleStatus::Published->value,
                'version' => 1,
                'max_attempts_per_day' => 2,
                'cohorts' => [$cohort->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $module = Module::where('slug', 'modul-rest-api-lks-super')->firstOrFail();
        $this->assertEquals(ModuleStatus::Published, $module->status);
        $this->assertTrue($module->cohorts->contains($cohort));

        // 2. Attach an asset to the module
        $fakeZip = UploadedFile::fake()->create('starter-code.zip', 500, 'application/zip');
        $asset = $module->assets()->create([
            'label' => 'Starter Project Code',
            'path' => $fakeZip->store('module-assets', 'private'),
            'type' => 'starter',
            'filename' => 'starter-code.zip',
            'size_bytes' => 500 * 1024,
            'is_starter' => true,
        ]);

        $this->assertDatabaseHas('module_assets', [
            'module_id' => $module->id,
            'label' => 'Starter Project Code',
        ]);
        Storage::disk('private')->assertExists($asset->path);
    }

    public function test_admin_question_bank_management_flow(): void
    {
        $this->actingAs($this->admin);
        $topic = Topic::first();

        Livewire::test(CreateQuestion::class)
            ->fillForm([
                'topic_id' => $topic->id,
                'type' => QuestionType::MultipleChoice->value,
                'difficulty' => 2,
                'status' => QuestionStatus::Review->value,
                'body_md' => 'Manakah perintah flexbox untuk memusatkan item secara vertikal?',
                'explanation_md' => 'align-items memusatkan item sepanjang cross axis.',
                'points' => 10,
                'options' => [
                    ['label' => 'align-items: center', 'is_correct' => true],
                    ['label' => 'justify-content: center', 'is_correct' => false],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $question = Question::where('body_md', 'Manakah perintah flexbox untuk memusatkan item secara vertikal?')->firstOrFail();
        $this->assertEquals(QuestionStatus::Review, $question->status);
        $this->assertEquals(2, $question->options()->count());
        $this->assertTrue($question->options()->where('is_correct', true)->exists());
    }

    public function test_admin_settings_management(): void
    {
        $this->actingAs($this->admin);

        Setting::set('platform_name', 'LKS Web Tech SMK Telkom');
        Setting::set('max_daily_attempts', 3);

        $this->assertEquals('LKS Web Tech SMK Telkom', Setting::get('platform_name'));
        $this->assertEquals(3, (int) Setting::get('max_daily_attempts'));
    }

    public function test_admin_has_unrestricted_access_to_all_application_routes(): void
    {
        $this->actingAs($this->admin);

        // Admin can view shared & mentor routes
        $this->get('/peringkat')->assertOk();
        $this->get('/bank-soal')->assertOk();
        $this->get('/profil')->assertOk();
        $this->get('/mentor/pengumpulan')->assertOk();

        // Visiting student-exclusive route /belajar is forbidden for admin
        $this->get('/belajar')->assertForbidden();
    }
}
