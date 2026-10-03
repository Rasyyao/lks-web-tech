<?php

namespace App\Livewire\Student;

use App\Models\Module;
use App\Models\Submission;
use App\Services\Submissions\SubmissionStorage;
use App\Services\Submissions\ZipUploadValidator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class ModuleShow extends Component
{
    use WithFileUploads;

    public Module $module;
    public $zipFile;

    public function mount(Module $module): void
    {
        $this->module = $module->load(['assets', 'cohorts']);
    }

    public function submitZip(ZipUploadValidator $validator, SubmissionStorage $storage): void
    {
        $user = Auth::user();

        $this->validate([
            'zipFile' => ['required', 'file', 'max:20480'],
        ], [
            'zipFile.required' => __('modules.choose_file_first'),
            'zipFile.max' => __('modules.file_too_large'),
        ]);

        $validationResult = $validator->validate($this->zipFile, $this->module, $user);

        if (! $validationResult['valid']) {
            $this->addError('zipFile', $validationResult['error']);
            return;
        }

        $storage->store($this->zipFile, $this->module, $user);

        $this->reset('zipFile');
        session()->flash('success', __('modules.submitted_success'));
    }

    public function render()
    {
        $user = Auth::user();

        $submissions = Submission::query()
            ->where('user_id', $user->id)
            ->where('module_id', $this->module->id)
            ->orderByDesc('attempt_no')
            ->get();

        $todayAttemptsCount = $this->module->todaySubmissionCount($user);
        $remainingAttempts = max(0, $this->module->max_attempts_per_day - $todayAttemptsCount);

        return view('livewire.student.module-show', [
            'submissions' => $submissions,
            'todayAttemptsCount' => $todayAttemptsCount,
            'remainingAttempts' => $remainingAttempts,
            'isOpen' => $this->module->isOpen(),
        ])->title($this->module->title);
    }
}
