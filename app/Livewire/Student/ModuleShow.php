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
    public ?array $testSuite = null;

    public function mount(Module $module): void
    {
        $this->module = $module->load(['assets', 'cohorts']);

        // Load automated API test suite JSON if available (Postman format)
        $suitePath = match ($module->slug) {
            'modul-a-server-side-api' => base_path('modules/modul-a/test-suite.json'),
            default => null,
        };

        if ($suitePath && file_exists($suitePath)) {
            $this->testSuite = json_decode(file_get_contents($suitePath), true);
        }
    }

    public function submitZip(
        ZipUploadValidator $validator,
        SubmissionStorage $storage,
        \App\Services\Submissions\AutomatedJudgeService $judge
    ): void {
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

        $submission = $storage->store($this->zipFile, $this->module, $user);

        // Run internal automated evaluation in background (invisible to students)
        $judge->evaluate($submission);

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
            'testSuite' => $this->testSuite,
        ])->title($this->module->title);
    }
}
