{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Mentor Submission Review (/mentor/pengumpulan/{id}) --}}
{{-- Primary job of the screen: Review student project metadata, download ZIP, input manual score (0-100) and Markdown feedback. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, pass, paper, tint --}}
{{-- Type roles: Schibsted Grotesk for form and headings; tabular-nums for scores and hashes; JetBrains Mono for filenames and SHA-256 --}}
{{-- Layout idea: Two-column layout: Left has student & file details and scoring form, right has previous submission history --}}
{{-- What I changed after the "would any app have this?" check: Banned fancy confetti or grading sliders; used explicit number field and teacher feedback notepad. --}}

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('mentor.submissions') }}" class="text-xs text-ink-muted hover:text-brand-deep inline-flex items-center gap-1 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
            ← {{ __('submissions.title') }}
        </a>
    </div>

    <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-rule pb-4">
        <div>
            <h1 class="text-2xl font-bold text-ink">
                Penilaian Tugas Siswa
            </h1>
            <p class="text-xs text-ink-muted">
                {{ $submission->user->name }} — {{ $submission->module->title }} (Percobaan #{{ $submission->attempt_no }})
            </p>
        </div>

        <div>
            @if($submission->status === \App\Enums\SubmissionStatus::Received)
                <span class="inline-flex items-center px-3 py-1 rounded-cell text-xs font-semibold bg-tint text-brand-deep">
                    {{ __('submissions.waiting_review') }}
                </span>
            @elseif($submission->status === \App\Enums\SubmissionStatus::Graded)
                <span class="inline-flex items-center px-3 py-1 rounded-cell text-xs font-semibold bg-pass/10 text-pass">
                    {{ __('submissions.status_graded') }}: {{ $submission->manual_score }}/100
                </span>
            @elseif($submission->status === \App\Enums\SubmissionStatus::Rejected)
                <span class="inline-flex items-center px-3 py-1 rounded-cell text-xs font-semibold bg-paper text-ink border border-rule">
                    {{ __('submissions.status_rejected') }}
                </span>
            @endif
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left 2-cols: File details and Grading form --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Student and file info --}}
            <x-panel class="space-y-4">
                <h2 class="text-base font-bold text-ink border-b border-rule pb-2">
                    {{ __('submissions.file_info') }}
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-ink-muted block">{{ __('submissions.student') }}:</span>
                        <strong class="text-ink text-sm">{{ $submission->user->name }}</strong>
                        <span class="text-ink-muted font-mono block">{{ $submission->user->username }}</span>
                    </div>

                    <div>
                        <span class="text-ink-muted block">Kelas:</span>
                        <span class="text-ink font-medium">{{ $submission->user->cohorts->pluck('name')->join(', ') ?: '—' }}</span>
                    </div>

                    <div>
                        <span class="text-ink-muted block">{{ __('submissions.file_name') }}:</span>
                        <span class="text-ink font-mono">{{ $submission->original_name }}</span>
                    </div>

                    <div>
                        <span class="text-ink-muted block">{{ __('submissions.file_size') }}:</span>
                        <span class="text-ink tabular-nums">{{ number_format($submission->size / 1024, 1) }} KB</span>
                    </div>

                    <div class="sm:col-span-2">
                        <span class="text-ink-muted block">SHA-256:</span>
                        <span class="text-ink-muted font-mono text-[11px] break-all select-all">{{ $submission->sha256 }}</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-rule">
                    <x-button :href="route('download.submission', $submission->id)">
                        {{ __('submissions.download_zip') }}
                    </x-button>
                </div>
            </x-panel>

            {{-- Scoring and Feedback Form --}}
            <x-panel class="space-y-6">
                <div>
                    <h2 class="text-base font-bold text-ink">
                        Formulir Penilaian Mentor
                    </h2>
                    <p class="text-xs text-ink-muted">
                        Simpan nilai dan feedback untuk memperbarui skor di papan peringkat.
                    </p>
                </div>

                <form wire:submit="saveReview" class="space-y-4 border-t border-rule pt-4">
                    {{-- Status Choice --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-ink">
                            Keputusan:
                        </label>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer text-sm">
                                <input
                                    type="radio"
                                    wire:model.live="status"
                                    value="graded"
                                    class="text-brand focus:ring-brand-deep"
                                >
                                <span>Beri Nilai (Sudah Dinilai)</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer text-sm">
                                <input
                                    type="radio"
                                    wire:model.live="status"
                                    value="rejected"
                                    class="text-brand-deep focus:ring-brand-deep"
                                >
                                <span>Tolak Pengumpulan</span>
                            </label>
                        </div>
                    </div>

                    {{-- Score Field if graded --}}
                    @if($status === 'graded')
                        <div class="space-y-1">
                            <label for="manual_score" class="block text-sm font-medium text-ink">
                                {{ __('submissions.score') }} (0–100)
                                <span class="text-brand-deep">*</span>
                            </label>
                            <input
                                type="number"
                                id="manual_score"
                                wire:model="manual_score"
                                min="0"
                                max="100"
                                class="w-32 rounded-cell border border-rule px-3 py-2 text-sm bg-sheet text-ink tabular-nums font-bold focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                            >
                            @error('manual_score')
                                <p class="text-xs text-brand-deep mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    {{-- Feedback in Markdown --}}
                    <div class="space-y-1">
                        <label for="feedback_md" class="block text-sm font-medium text-ink">
                            {{ $status === 'rejected' ? 'Alasan Penolakan' : __('submissions.feedback') }}
                            @if($status === 'rejected')
                                <span class="text-brand-deep">*</span>
                            @endif
                        </label>
                        <textarea
                            id="feedback_md"
                            wire:model="feedback_md"
                            rows="5"
                            placeholder="Tuliskan catatan teknis, kelebihan, dan bagian yang perlu diperbaiki (format Markdown didukung)..."
                            class="w-full rounded-cell border border-rule px-3 py-2 text-sm bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep font-sans"
                        ></textarea>
                        @error('feedback_md')
                            <p class="text-xs text-brand-deep mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <x-button type="submit">
                            {{ __('submissions.save_score') }}
                        </x-button>
                    </div>
                </form>
            </x-panel>
        </div>

        {{-- Right column: Past Submissions History --}}
        <div>
            <x-panel class="space-y-4">
                <div>
                    <h2 class="text-base font-bold text-ink">
                        {{ __('submissions.history') }} Siswa
                    </h2>
                    <p class="text-xs text-ink-muted">
                        Percobaan lain pada modul ini
                    </p>
                </div>

                @if($history->isEmpty())
                    <p class="text-xs text-ink-muted py-4 text-center border-t border-rule">
                        Ini adalah percobaan pertama siswa untuk modul ini.
                    </p>
                @else
                    <ul class="divide-y divide-rule border-t border-rule" role="list">
                        @foreach($history as $item)
                            <li class="py-3 space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-ink">
                                        Percobaan #{{ $item->attempt_no }}
                                    </span>
                                    <time class="text-ink-muted tabular-nums">
                                        {{ $item->created_at->translatedFormat('d M, H:i') }}
                                    </time>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-ink-muted">
                                        Nilai: <strong class="text-ink tabular-nums">{{ $item->manual_score !== null ? $item->manual_score : '—' }}</strong>
                                    </span>
                                    <a
                                        href="{{ route('mentor.submission.review', $item->id) }}"
                                        class="text-brand-deep hover:underline"
                                    >
                                        Buka
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-panel>
        </div>
    </div>
</div>
