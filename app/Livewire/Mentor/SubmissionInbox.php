<?php

namespace App\Livewire\Mentor;

use App\Enums\SubmissionStatus;
use App\Models\Cohort;
use App\Models\Module;
use App\Models\Submission;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Daftar Pengumpulan — Mentor')]
class SubmissionInbox extends Component
{
    use WithPagination;

    #[Url]
    public string $status = 'all';

    #[Url]
    public string $module_id = 'all';

    #[Url]
    public string $cohort_id = 'all';

    public function updated($property): void
    {
        if (in_array($property, ['status', 'module_id', 'cohort_id'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = Submission::query()
            ->with(['user.cohorts', 'module'])
            ->latest();

        if ($this->status !== 'all' && SubmissionStatus::tryFrom($this->status)) {
            $query->where('status', $this->status);
        }

        if ($this->module_id !== 'all' && is_numeric($this->module_id)) {
            $query->where('module_id', (int) $this->module_id);
        }

        if ($this->cohort_id !== 'all' && is_numeric($this->cohort_id)) {
            $cId = (int) $this->cohort_id;
            $query->whereHas('user.cohorts', fn ($q) => $q->where('cohorts.id', $cId));
        }

        $submissions = $query->paginate(20);

        $pendingCount = Submission::where('status', SubmissionStatus::Received)->count();
        $gradedCount = Submission::where('status', SubmissionStatus::Graded)->count();
        $totalCount = Submission::count();

        $modules = Module::orderBy('title')->get();
        $cohorts = Cohort::orderBy('name')->get();

        return view('livewire.mentor.submission-inbox', [
            'submissions' => $submissions,
            'pendingCount' => $pendingCount,
            'gradedCount' => $gradedCount,
            'totalCount' => $totalCount,
            'modules' => $modules,
            'cohorts' => $cohorts,
        ]);
    }
}
