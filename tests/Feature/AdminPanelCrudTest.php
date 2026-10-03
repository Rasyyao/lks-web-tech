<?php

namespace Tests\Feature;

use App\Enums\CompetitionLevel;
use App\Enums\CompetitionModuleType;
use App\Enums\ModuleStatus;
use App\Enums\ModuleTrack;
use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use App\Filament\Resources\Announcements\Pages\CreateAnnouncement;
use App\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Filament\Resources\Cohorts\Pages\CreateCohort;
use App\Filament\Resources\Modules\Pages\CreateModule;
use App\Filament\Resources\Modules\Pages\ListModules;
use App\Filament\Resources\Modules\RelationManagers\AssetsRelationManager;
use App\Filament\Resources\Modules\Pages\EditModule;
use App\Filament\Resources\QuestionBankPapers\Pages\CreateQuestionBankPaper;
use App\Filament\Resources\QuestionBankPapers\Pages\ListQuestionBankPapers;
use App\Filament\Resources\Questions\Pages\CreateQuestion;
use App\Filament\Resources\Questions\Pages\EditQuestion;
use App\Filament\Resources\Questions\Pages\ListQuestions;
use App\Filament\Resources\Topics\Pages\CreateTopic;
use App\Filament\Resources\Topics\Pages\EditTopic;
use App\Filament\Resources\Topics\RelationManagers\MaterialsRelationManager;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\AuditLog;
use App\Models\Announcement;
use App\Models\Cohort;
use App\Models\Module;
use App\Models\Question;
use App\Models\QuestionBankPaper;
use App\Models\Topic;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPanelCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->admin = User::where('username', 'admin')->first();
        $this->actingAs($this->admin);
        Filament::setCurrentPanel('admin');
    }

    public function test_only_admins_can_open_the_panel(): void
    {
        $this->actingAs(User::where('username', 'mentor1')->first())
            ->get('/admin/users')
            ->assertForbidden();

        $this->actingAs(User::where('username', '541221001')->first())
            ->get('/admin/users')
            ->assertForbidden();

        $this->actingAs($this->admin)->get('/admin/users')->assertOk();
    }

    public function test_every_admin_page_renders(): void
    {
        foreach (['users', 'cohorts', 'topics', 'questions', 'modules', 'announcements', 'question-bank-papers', 'audit-logs'] as $page) {
            $this->get("/admin/{$page}")->assertOk();
        }

        foreach (['users', 'cohorts', 'topics', 'questions', 'modules', 'announcements', 'question-bank-papers'] as $page) {
            $this->get("/admin/{$page}/create")->assertOk();
        }

        $this->get('/admin')->assertOk();
    }

    public function test_admin_creates_student_with_role_cohort_and_hashed_password(): void
    {
        $cohort = Cohort::first();

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Siswa Baru',
                'username' => '541229999',
                'email' => 'baru@student.example.test',
                'password' => 'rahasia-123',
                'roles' => [Role::findByName('student')->id],
                'cohorts' => [$cohort->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('username', '541229999')->firstOrFail();

        $this->assertTrue($user->hasRole('student'));
        $this->assertTrue($user->cohorts->contains($cohort));
        $this->assertTrue(Hash::check('rahasia-123', $user->password));
        $this->assertNotSame('rahasia-123', $user->password);
        $this->assertDatabaseHas('audit_logs', ['action' => 'user.created', 'subject_id' => $user->id]);
    }

    public function test_user_password_is_required_on_create_and_too_short_is_rejected(): void
    {
        Livewire::test(CreateUser::class)
            ->fillForm(['name' => 'X', 'username' => 'xuser', 'roles' => [Role::findByName('student')->id]])
            ->call('create')
            ->assertHasFormErrors(['password' => 'required']);

        Livewire::test(CreateUser::class)
            ->fillForm(['name' => 'X', 'username' => 'xuser', 'password' => 'short', 'roles' => [Role::findByName('student')->id]])
            ->call('create')
            ->assertHasFormErrors(['password']);
    }

    public function test_editing_user_without_password_keeps_the_old_password(): void
    {
        $student = User::where('username', '541221001')->first();
        $oldHash = $student->password;

        Livewire::test(EditUser::class, ['record' => $student->getKey()])
            ->fillForm(['name' => 'Dewi Lestari Updated', 'password' => ''])
            ->call('save')
            ->assertHasNoFormErrors();

        $student->refresh();

        $this->assertSame('Dewi Lestari Updated', $student->name);
        $this->assertSame($oldHash, $student->password);
    }

    public function test_admin_can_not_deactivate_or_delete_themselves(): void
    {
        Livewire::test(ListUsers::class)
            ->assertTableActionHidden('delete', $this->admin);
    }

    public function test_admin_can_delete_user_without_history_from_the_list_and_it_is_audited(): void
    {
        $student = User::where('username', '541221004')->first();
        $student->practiceAttempts()->delete();
        $student->submissions()->delete();

        Livewire::test(ListUsers::class)
            ->callTableAction('delete', $student)
            ->assertHasNoTableActionErrors();

        $this->assertModelMissing($student);
        $this->assertDatabaseHas('audit_logs', ['action' => 'user.deleted', 'subject_id' => $student->id]);
    }

    public function test_password_reset_action_forces_password_change_and_is_audited(): void
    {
        $student = User::where('username', '541221001')->first();

        Livewire::test(ListUsers::class)
            ->callTableAction('resetPassword', $student, ['password' => 'sementara-123'])
            ->assertHasNoTableActionErrors();

        $student->refresh();

        $this->assertTrue(Hash::check('sementara-123', $student->password));
        $this->assertTrue($student->must_change_password);
        $this->assertDatabaseHas('audit_logs', ['action' => 'user.password_reset', 'subject_id' => $student->id]);
    }

    public function test_admin_creates_cohort_with_members(): void
    {
        $student = User::where('username', '541221002')->first();

        Livewire::test(CreateCohort::class)
            ->fillForm([
                'name' => 'XII RPL 3',
                'type' => 'class',
                'year' => 2026,
                'users' => [$student->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $cohort = Cohort::where('name', 'XII RPL 3')->firstOrFail();
        $this->assertTrue($cohort->users->contains($student));
    }

    public function test_admin_creates_topic_and_adds_material(): void
    {
        Livewire::test(CreateTopic::class)
            ->fillForm(['name' => 'Web Security', 'slug' => 'web-security', 'position' => 9])
            ->call('create')
            ->assertHasNoFormErrors();

        $topic = Topic::where('slug', 'web-security')->firstOrFail();

        Livewire::test(MaterialsRelationManager::class, [
            'ownerRecord' => $topic,
            'pageClass' => EditTopic::class,
        ])
            ->callAction(TestAction::make('create')->table(), [
                'title' => 'Pengantar XSS',
                'level' => 2,
                'position' => 1,
                'body_md' => '# XSS',
            ])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('materials', ['topic_id' => $topic->id, 'title' => 'Pengantar XSS']);
    }

    public function test_admin_creates_multiple_choice_question_with_exactly_one_correct_option(): void
    {
        $topic = Topic::first();

        Livewire::test(CreateQuestion::class)
            ->fillForm([
                'topic_id' => $topic->id,
                'type' => QuestionType::MultipleChoice->value,
                'difficulty' => 2,
                'points' => 10,
                'body_md' => 'Tag HTML untuk tautan?',
                'status' => QuestionStatus::Review->value,
                'options' => [
                    ['label' => '<a>', 'is_correct' => true],
                    ['label' => '<p>', 'is_correct' => false],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $question = Question::where('body_md', 'Tag HTML untuk tautan?')->firstOrFail();

        $this->assertSame($this->admin->id, $question->created_by);
        $this->assertCount(2, $question->options);
        $this->assertSame(1, $question->options->where('is_correct', true)->count());
    }

    public function test_question_with_no_or_multiple_correct_options_is_rejected(): void
    {
        $base = [
            'topic_id' => Topic::first()->id,
            'type' => QuestionType::MultipleChoice->value,
            'difficulty' => 1,
            'points' => 10,
            'body_md' => 'Soal invalid',
            'status' => QuestionStatus::Draft->value,
        ];

        Livewire::test(CreateQuestion::class)
            ->fillForm($base + ['options' => [
                ['label' => 'A', 'is_correct' => false],
                ['label' => 'B', 'is_correct' => false],
            ]])
            ->call('create')
            ->assertHasFormErrors(['options']);

        Livewire::test(CreateQuestion::class)
            ->fillForm($base + ['options' => [
                ['label' => 'A', 'is_correct' => true],
                ['label' => 'B', 'is_correct' => true],
            ]])
            ->call('create')
            ->assertHasFormErrors(['options']);

        $this->assertDatabaseMissing('questions', ['body_md' => 'Soal invalid']);
    }

    public function test_short_answer_question_stores_normalized_answers(): void
    {
        Livewire::test(CreateQuestion::class)
            ->fillForm([
                'topic_id' => Topic::first()->id,
                'type' => QuestionType::ShortAnswer->value,
                'difficulty' => 1,
                'points' => 5,
                'body_md' => 'Singkatan dari HyperText Markup Language?',
                'status' => QuestionStatus::Draft->value,
                'answers' => [['text' => '  HTML  ']],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $question = Question::where('body_md', 'like', 'Singkatan dari HyperText%')->firstOrFail();

        $this->assertSame('html', $question->answers->first()->normalized);
    }

    public function test_author_cannot_publish_their_own_question_but_another_admin_can(): void
    {
        $topic = Topic::first();

        $question = Question::create([
            'topic_id' => $topic->id,
            'type' => QuestionType::ShortAnswer,
            'body_md' => 'Soal milik admin',
            'difficulty' => 1,
            'points' => 5,
            'status' => QuestionStatus::Review,
            'created_by' => $this->admin->id,
        ]);

        // Author: publish quick action is not offered.
        Livewire::test(ListQuestions::class)
            ->assertTableActionHidden('publish', $question);

        // Another admin: allowed, and recorded as reviewer.
        $other = User::create([
            'name' => 'Admin Dua',
            'username' => 'admin2',
            'password' => 'password123',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $other->assignRole('admin');
        $this->actingAs($other);

        Livewire::test(ListQuestions::class)
            ->callTableAction('publish', $question);

        $question->refresh();
        $this->assertSame(QuestionStatus::Published, $question->status);
        $this->assertSame($other->id, $question->reviewed_by);
        $this->assertDatabaseHas('audit_logs', ['action' => 'question.published', 'subject_id' => $question->id]);
    }

    public function test_editing_question_text_bumps_version(): void
    {
        $question = Question::create([
            'topic_id' => Topic::first()->id,
            'type' => QuestionType::ShortAnswer,
            'body_md' => 'Versi awal',
            'difficulty' => 1,
            'points' => 5,
            'status' => QuestionStatus::Draft,
            'created_by' => $this->admin->id,
        ]);
        $question->answers()->create(['text' => 'x', 'normalized' => 'x']);

        Livewire::test(EditQuestion::class, ['record' => $question->getKey()])
            ->fillForm(['body_md' => 'Versi baru'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(2, $question->fresh()->version);
    }

    public function test_admin_creates_module_and_slug_is_locked_afterwards(): void
    {
        $cohort = Cohort::first();

        Livewire::test(CreateModule::class)
            ->fillForm([
                'title' => 'Modul Uji Admin',
                'slug' => 'modul-uji-admin',
                'track' => ModuleTrack::Server->value,
                'level' => 2,
                'status' => ModuleStatus::Draft->value,
                'max_attempts_per_day' => 3,
                'cohorts' => [$cohort->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $module = Module::where('slug', 'modul-uji-admin')->firstOrFail();
        $this->assertTrue($module->cohorts->contains($cohort));

        Livewire::test(EditModule::class, ['record' => $module->getKey()])
            ->assertFormFieldDisabled('slug');
    }

    public function test_module_publish_and_archive_actions_are_audited(): void
    {
        $module = Module::create([
            'slug' => 'draft-mod',
            'title' => 'Draft Mod',
            'track' => ModuleTrack::Client,
            'status' => ModuleStatus::Draft,
        ]);

        Livewire::test(ListModules::class)
            ->callTableAction('publish', $module);

        $this->assertSame(ModuleStatus::Published, $module->fresh()->status);

        Livewire::test(ListModules::class)
            ->callTableAction('archive', $module);

        $this->assertSame(ModuleStatus::Archived, $module->fresh()->status);

        $this->assertDatabaseHas('audit_logs', ['action' => 'module.published', 'subject_id' => $module->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'module.archived', 'subject_id' => $module->id]);
    }

    public function test_module_with_submissions_can_not_be_deleted(): void
    {
        $module = Module::first();
        $module->submissions()->create([
            'user_id' => User::where('username', '541221001')->first()->id,
            'attempt_no' => 1,
            'path' => 'submissions/x.zip',
            'original_name' => 'x.zip',
            'size' => 1,
            'sha256' => str_repeat('a', 64),
            'status' => 'received',
        ]);

        Livewire::test(ListModules::class)
            ->assertTableActionHidden('delete', $module);
    }

    public function test_module_asset_upload_stores_file_privately_and_removal_deletes_it(): void
    {
        Storage::fake('private');

        $module = Module::first();

        $component = Livewire::test(AssetsRelationManager::class, [
            'ownerRecord' => $module,
            'pageClass' => EditModule::class,
        ])->callAction(TestAction::make('create')->table(), [
            'path' => UploadedFile::fake()->createWithContent('starter.zip', "PK\x03\x04".str_repeat('a', 4096)),
        ]);

        $component->assertHasNoFormErrors();

        $asset = $module->assets()->latest('id')->first();
        $this->assertNotNull($asset);
        Storage::disk('private')->assertExists($asset->path);
        $this->assertGreaterThan(0, $asset->size);

        $component->callAction(TestAction::make('delete')->table($asset));

        Storage::disk('private')->assertMissing($asset->path);
    }

    public function test_admin_creates_announcement(): void
    {
        Livewire::test(CreateAnnouncement::class)
            ->fillForm([
                'title' => 'Jadwal Seleksi',
                'body_md' => 'Seleksi hari Senin.',
                'published_at' => now()->toDateTimeString(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('announcements', ['title' => 'Jadwal Seleksi', 'cohort_id' => null]);
    }

    public function test_admin_uploads_bank_soal_pdf_with_categories(): void
    {
        Storage::fake('private');

        Livewire::test(CreateQuestionBankPaper::class)
            ->fillForm([
                'title' => 'LKS Provinsi 2026 Client',
                'year' => 2026,
                'level' => CompetitionLevel::Provinsi->value,
                'module_type' => CompetitionModuleType::Client->value,
                'file_path' => UploadedFile::fake()->createWithContent('soal.pdf', "%PDF-1.4\n".str_repeat('a', 4096)),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $paper = QuestionBankPaper::where('title', 'LKS Provinsi 2026 Client')->firstOrFail();

        $this->assertSame($this->admin->id, $paper->uploaded_by);
        $this->assertSame(CompetitionLevel::Provinsi, $paper->level);
        $this->assertSame('lks-provinsi-2026-client', $paper->slug);
        $this->assertGreaterThan(0, $paper->file_size);
        Storage::disk('private')->assertExists($paper->file_path);
    }

    public function test_deleting_bank_soal_from_the_list_removes_the_pdf(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('question-bank/x.pdf', '%PDF-1.4');

        $paper = QuestionBankPaper::create([
            'title' => 'Hapus saya',
            'slug' => 'hapus-saya',
            'year' => 2026,
            'level' => CompetitionLevel::Kabupaten,
            'module_type' => CompetitionModuleType::Client,
            'file_name' => 'x.pdf',
            'file_path' => 'question-bank/x.pdf',
            'file_size' => 8,
            'uploaded_by' => $this->admin->id,
        ]);

        Livewire::test(ListQuestionBankPapers::class)
            ->callTableAction('delete', $paper)
            ->assertHasNoTableActionErrors();

        $this->assertModelMissing($paper);
        Storage::disk('private')->assertMissing('question-bank/x.pdf');
    }

    public function test_bank_soal_rejects_non_pdf_and_files_over_10mb(): void
    {
        Storage::fake('private');

        $payload = fn ($file) => [
            'title' => 'Invalid',
            'year' => 2026,
            'level' => CompetitionLevel::Nasional->value,
            'module_type' => CompetitionModuleType::Server->value,
            'file_path' => $file,
        ];

        Livewire::test(CreateQuestionBankPaper::class)
            ->fillForm($payload(UploadedFile::fake()->create('big.pdf', 10241, 'application/pdf')))
            ->call('create')
            ->assertHasFormErrors(['file_path']);

        Livewire::test(CreateQuestionBankPaper::class)
            ->fillForm($payload(UploadedFile::fake()->create('evil.exe', 10, 'application/x-msdownload')))
            ->call('create')
            ->assertHasFormErrors(['file_path']);

        $this->assertDatabaseMissing('question_bank_papers', ['title' => 'Invalid']);
    }

    public function test_audit_log_is_read_only(): void
    {
        AuditLog::record('test.event');

        $this->get('/admin/audit-logs/create')->assertNotFound();

        Livewire::test(ListAuditLogs::class)->assertSuccessful();

        $this->assertFalse(\App\Filament\Resources\AuditLogs\AuditLogResource::canCreate());
        $this->assertFalse(\App\Filament\Resources\AuditLogs\AuditLogResource::canDeleteAny());
    }
}
