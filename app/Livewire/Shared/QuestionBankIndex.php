<?php

namespace App\Livewire\Shared;

use App\Enums\CompetitionLevel;
use App\Enums\CompetitionModuleType;
use App\Models\AuditLog;
use App\Models\QuestionBankPaper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class QuestionBankIndex extends Component
{
    use WithFileUploads, WithPagination;

    // Filters
    public string $search = '';

    public string $yearFilter = '';

    public string $levelFilter = '';

    public string $moduleTypeFilter = '';

    // Modal state for Admin/Mentor Upload & Edit
    public bool $showModal = false;

    public ?int $editingId = null;

    // Form fields
    public string $title = '';

    public ?int $year = null;

    public string $level = 'nasional';

    public string $module_type = 'client';

    public string $description = '';

    public $pdfFile = null;

    public function mount(): void
    {
        $this->year = (int) date('Y');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedYearFilter(): void
    {
        $this->resetPage();
    }

    public function updatedLevelFilter(): void
    {
        $this->resetPage();
    }

    public function updatedModuleTypeFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->yearFilter = '';
        $this->levelFilter = '';
        $this->moduleTypeFilter = '';
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->authorizeAdminOrMentor();
        $this->resetForm();
        $this->year = (int) date('Y');
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeAdminOrMentor();
        $paper = QuestionBankPaper::findOrFail($id);

        $this->editingId = $paper->id;
        $this->title = $paper->title;
        $this->year = $paper->year;
        $this->level = $paper->level->value;
        $this->module_type = $paper->module_type->value;
        $this->description = $paper->description ?? '';
        $this->pdfFile = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorizeAdminOrMentor();

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:2000', 'max:2035'],
            'level' => ['required', 'string', 'in:kabupaten,provinsi,nasional'],
            'module_type' => ['required', 'string', 'in:client,server'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];

        if ($this->editingId) {
            $rules['pdfFile'] = ['nullable', 'file', 'mimes:pdf', 'max:10240']; // 10MB max
        } else {
            $rules['pdfFile'] = ['required', 'file', 'mimes:pdf', 'max:10240']; // 10MB max
        }

        $messages = [
            'title.required' => 'Judul berkas soal wajib diisi.',
            'year.required' => 'Tahun lomba wajib diisi.',
            'year.integer' => 'Tahun harus berupa angka.',
            'level.required' => 'Jenjang tingkat lomba wajib dipilih.',
            'module_type.required' => 'Tipe modul (client/server) wajib dipilih.',
            'pdfFile.required' => 'Berkas PDF soal wajib diunggah.',
            'pdfFile.mimes' => 'Berkas harus berupa dokumen PDF (.pdf).',
            'pdfFile.max' => 'Ukuran berkas PDF maksimal adalah 10 MB.',
        ];

        $this->validate($rules, $messages);

        $user = Auth::user();

        if ($this->editingId) {
            $paper = QuestionBankPaper::findOrFail($this->editingId);

            $updateData = [
                'title' => $this->title,
                'year' => (int) $this->year,
                'level' => CompetitionLevel::from($this->level),
                'module_type' => CompetitionModuleType::from($this->module_type),
                'description' => trim($this->description) ?: null,
            ];

            if ($this->pdfFile) {
                // Delete previous file if exists
                if ($paper->file_path && Storage::disk('private')->exists($paper->file_path)) {
                    Storage::disk('private')->delete($paper->file_path);
                }

                $originalName = $this->pdfFile->getClientOriginalName();
                $storedPath = $this->pdfFile->store('question-bank', 'private');

                $updateData['file_path'] = $storedPath;
                $updateData['file_name'] = $originalName;
                $updateData['file_size'] = $this->pdfFile->getSize();
            }

            $paper->update($updateData);

            AuditLog::record(
                action: 'question_bank.updated',
                subject: $paper,
                meta: ['title' => $paper->title, 'year' => $paper->year],
            );

            session()->flash('success', "Berkas soal \"{$paper->title}\" berhasil diperbarui.");
        } else {
            $slug = QuestionBankPaper::generateUniqueSlug($this->title.'-'.$this->year.'-'.$this->module_type);
            $originalName = $this->pdfFile->getClientOriginalName();
            $storedPath = $this->pdfFile->store('question-bank', 'private');
            $fileSize = $this->pdfFile->getSize();

            $paper = QuestionBankPaper::create([
                'title' => $this->title,
                'slug' => $slug,
                'year' => (int) $this->year,
                'level' => CompetitionLevel::from($this->level),
                'module_type' => CompetitionModuleType::from($this->module_type),
                'description' => trim($this->description) ?: null,
                'file_path' => $storedPath,
                'file_name' => $originalName,
                'file_size' => $fileSize,
                'uploaded_by' => $user->id,
                'download_count' => 0,
            ]);

            AuditLog::record(
                action: 'question_bank.created',
                subject: $paper,
                meta: ['title' => $paper->title, 'year' => $paper->year, 'size' => $fileSize],
            );

            session()->flash('success', "Berkas soal \"{$paper->title}\" berhasil diunggah.");
        }

        $this->resetForm();
        $this->showModal = false;
    }

    public function delete(int $id): void
    {
        $this->authorizeAdminOrMentor();
        $paper = QuestionBankPaper::findOrFail($id);

        if ($paper->file_path && Storage::disk('private')->exists($paper->file_path)) {
            Storage::disk('private')->delete($paper->file_path);
        }

        $title = $paper->title;
        $paper->delete();

        AuditLog::record(
            action: 'question_bank.deleted',
            subject: null,
            meta: ['title' => $title],
        );

        session()->flash('success', "Berkas soal \"{$title}\" berhasil dihapus.");
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->title = '';
        $this->year = (int) date('Y');
        $this->level = 'nasional';
        $this->module_type = 'client';
        $this->description = '';
        $this->pdfFile = null;
    }

    protected function authorizeAdminOrMentor(): void
    {
        if (! Auth::check() || ! Auth::user()->hasRole(['admin', 'mentor'])) {
            abort(403, 'Aksi ini hanya dapat dilakukan oleh Admin atau Mentor.');
        }
    }

    public function render()
    {
        $query = QuestionBankPaper::query()->with('uploader');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%')
                    ->orWhere('file_name', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->yearFilter !== '') {
            $query->where('year', (int) $this->yearFilter);
        }

        if ($this->levelFilter !== '') {
            $query->where('level', $this->levelFilter);
        }

        if ($this->moduleTypeFilter !== '') {
            $query->where('module_type', $this->moduleTypeFilter);
        }

        $papers = $query->orderByDesc('year')
            ->orderBy('title')
            ->paginate(9);

        // Stats and filter choices
        $years = QuestionBankPaper::query()->select('year')->distinct()->orderByDesc('year')->pluck('year');
        $totalCount = QuestionBankPaper::count();
        $clientCount = QuestionBankPaper::where('module_type', 'client')->count();
        $serverCount = QuestionBankPaper::where('module_type', 'server')->count();

        return view('livewire.shared.question-bank-index', [
            'papers' => $papers,
            'years' => $years,
            'totalCount' => $totalCount,
            'clientCount' => $clientCount,
            'serverCount' => $serverCount,
            'canManage' => Auth::check() && Auth::user()->hasRole(['admin', 'mentor']),
        ])->title('Bank Soal LKS Web Technologies');
    }
}
