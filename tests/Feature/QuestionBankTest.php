<?php

namespace Tests\Feature;

use App\Enums\CompetitionLevel;
use App\Enums\CompetitionModuleType;
use App\Livewire\Shared\QuestionBankIndex;
use App\Models\QuestionBankPaper;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class QuestionBankTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;

    protected User $admin;

    protected User $mentor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->student = User::where('username', '541221001')->first();
        $this->admin = User::where('username', 'admin')->first();
        $this->mentor = User::where('username', 'mentor1')->first();
    }

    public function test_guest_is_redirected_from_bank_soal(): void
    {
        $response = $this->get('/bank-soal');
        $response->assertRedirect('/masuk');
    }

    public function test_student_can_view_bank_soal_page_and_filter(): void
    {
        QuestionBankPaper::create([
            'title' => 'LKS Nasional 2024 Server',
            'slug' => 'lks-nasional-2024-server',
            'year' => 2024,
            'level' => CompetitionLevel::Nasional,
            'module_type' => CompetitionModuleType::Server,
            'file_path' => 'question-bank/test-server.pdf',
            'file_name' => 'test-server.pdf',
            'file_size' => 1024 * 500,
        ]);

        QuestionBankPaper::create([
            'title' => 'LKS Provinsi 2025 Client',
            'slug' => 'lks-provinsi-2025-client',
            'year' => 2025,
            'level' => CompetitionLevel::Provinsi,
            'module_type' => CompetitionModuleType::Client,
            'file_path' => 'question-bank/test-client.pdf',
            'file_name' => 'test-client.pdf',
            'file_size' => 1024 * 300,
        ]);

        $this->actingAs($this->student);

        Livewire::test(QuestionBankIndex::class)
            ->assertSee('LKS Nasional 2024 Server')
            ->assertSee('LKS Provinsi 2025 Client')
            // Test Filter Tahun
            ->set('yearFilter', '2024')
            ->assertSee('LKS Nasional 2024 Server')
            ->assertDontSee('LKS Provinsi 2025 Client')
            // Test Filter Modul Client
            ->set('yearFilter', '')
            ->set('moduleTypeFilter', 'client')
            ->assertSee('LKS Provinsi 2025 Client')
            ->assertDontSee('LKS Nasional 2024 Server')
            // Test Filter Jenjang Provinsi
            ->set('moduleTypeFilter', '')
            ->set('levelFilter', 'provinsi')
            ->assertSee('LKS Provinsi 2025 Client')
            ->assertDontSee('LKS Nasional 2024 Server');
    }

    public function test_student_cannot_upload_or_delete_question_paper(): void
    {
        $this->actingAs($this->student);

        Livewire::test(QuestionBankIndex::class)
            ->call('openCreateModal')
            ->assertForbidden();
    }

    public function test_admin_can_upload_pdf_file_up_to_10mb(): void
    {
        Storage::fake('private');
        $this->actingAs($this->admin);

        $fakePdf = UploadedFile::fake()->create('lks-2026-soal.pdf', 5000, 'application/pdf'); // 5MB

        Livewire::test(QuestionBankIndex::class)
            ->set('title', 'LKS Nasional XXXIV 2026 - Client Side Map')
            ->set('year', 2026)
            ->set('level', 'nasional')
            ->set('module_type', 'client')
            ->set('description', 'Deskripsi modul client-side peta 2026.')
            ->set('pdfFile', $fakePdf)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('question_bank_papers', [
            'title' => 'LKS Nasional XXXIV 2026 - Client Side Map',
            'year' => 2026,
            'level' => 'nasional',
            'module_type' => 'client',
        ]);
    }

    public function test_upload_fails_if_file_exceeds_10mb(): void
    {
        Storage::fake('private');
        $this->actingAs($this->admin);

        // 12 MB file (exceeds 10240 KB)
        $largePdf = UploadedFile::fake()->create('large-paper.pdf', 12000, 'application/pdf');

        Livewire::test(QuestionBankIndex::class)
            ->set('title', 'LKS Oversized Paper')
            ->set('year', 2026)
            ->set('level', 'nasional')
            ->set('module_type', 'server')
            ->set('pdfFile', $largePdf)
            ->call('save')
            ->assertHasErrors(['pdfFile' => 'max']);
    }

    public function test_user_can_download_question_paper(): void
    {
        Storage::fake('private');
        $this->actingAs($this->student);

        Storage::disk('private')->put('question-bank/sample.pdf', 'dummy-pdf-content');

        $paper = QuestionBankPaper::create([
            'title' => 'LKS Kab 2025',
            'slug' => 'lks-kab-2025',
            'year' => 2025,
            'level' => CompetitionLevel::Kabupaten,
            'module_type' => CompetitionModuleType::Server,
            'file_path' => 'question-bank/sample.pdf',
            'file_name' => 'LKS-Kab-2025.pdf',
            'file_size' => 1024,
            'download_count' => 0,
        ]);

        $response = $this->get(route('download.question-paper', $paper->id));
        $response->assertOk();
        $response->assertDownload('LKS-Kab-2025.pdf');

        $this->assertEquals(1, $paper->fresh()->download_count);
    }
}
