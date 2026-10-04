<?php

namespace App\Livewire\Mentor;

use App\Enums\SubmissionStatus;
use App\Jobs\RecomputeLeaderboard;
use App\Models\AuditLog;
use App\Models\Submission;
use App\Services\Submissions\AutomatedJudgeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class SubmissionReview extends Component
{
    public Submission $submission;

    public ?int $manual_score = null;

    public string $feedback_md = '';

    public string $status = 'graded';

    public function mount(Submission $submission): void
    {
        $this->submission = $submission->load(['user.cohorts', 'module']);
        $this->manual_score = $submission->manual_score;
        $this->feedback_md = $submission->feedback_md ?? '';
        $this->status = $submission->status->value;
    }

    public function rerunJudge(): void
    {
        $judge = app(AutomatedJudgeService::class);
        $results = $judge->evaluate($this->submission);
        $this->submission->refresh();
        session()->flash('success', 'Evaluasi otomatis sistem (Playwright E2E & Postman API) berhasil dijalankan ulang. Skor: '.($results['score'] ?? 0).'/100');
    }

    public function applyTestScore(): void
    {
        if ($this->submission->test_score !== null) {
            $this->manual_score = $this->submission->test_score;
        }
    }

    public function saveReview(): void
    {
        $mentor = Auth::user();

        if ($this->status === 'graded') {
            $this->validate([
                'manual_score' => ['required', 'integer', 'min:0', 'max:100'],
                'feedback_md' => ['nullable', 'string', 'max:5000'],
            ], [
                'manual_score.required' => 'Nilai wajib diisi (0–100).',
                'manual_score.min' => 'Nilai minimal adalah 0.',
                'manual_score.max' => 'Nilai maksimal adalah 100.',
            ]);
        } else {
            $this->validate([
                'feedback_md' => ['required', 'string', 'min:5'],
            ], [
                'feedback_md.required' => 'Alasan penolakan wajib ditulis di kolom catatan.',
            ]);
        }

        $oldScore = $this->submission->manual_score;
        $newStatus = SubmissionStatus::from($this->status);

        $this->submission->update([
            'status' => $newStatus,
            'manual_score' => $newStatus === SubmissionStatus::Graded ? $this->manual_score : null,
            'feedback_md' => trim($this->feedback_md) ?: null,
            'reviewed_by' => $mentor->id,
            'reviewed_at' => now(),
        ]);

        // Audit log
        AuditLog::record(
            actorId: $mentor->id,
            action: 'submission.reviewed',
            subject: $this->submission,
            meta: [
                'old_score' => $oldScore,
                'new_score' => $this->submission->manual_score,
                'status' => $newStatus->value,
            ],
            ip: request()->ip()
        );

        // Leaderboard recompute job per PRD F8
        RecomputeLeaderboard::dispatch($this->submission->user_id);

        session()->flash('success', __('submissions.score_saved'));
    }

    public function render()
    {
        // Past attempts of this student for this module
        $history = Submission::query()
            ->where('user_id', $this->submission->user_id)
            ->where('module_id', $this->submission->module_id)
            ->where('id', '!=', $this->submission->id)
            ->orderByDesc('attempt_no')
            ->get();

        return view('livewire.mentor.submission-review', [
            'history' => $history,
        ])->title('Penilaian: '.$this->submission->user->name);
    }
}
