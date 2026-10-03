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

            {{-- Panel: Automated System Evaluation (Admin & Mentor Engine: Playwright E2E & API Runner) --}}
            <x-panel class="space-y-4 border-2 border-brand/20 bg-sheet">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-rule pb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-ink">
                                Evaluasi Otomatis Sistem
                            </h2>
                            <span class="px-2 py-0.5 rounded-cell font-mono text-[10px] font-bold bg-tint text-brand-deep border border-brand-deep/20">
                                Playwright E2E & Postman Engine
                            </span>
                        </div>
                        <p class="text-xs text-ink-muted mt-0.5">
                            Sistem pengujian otomatis internal juri (tertutup dan tidak terlihat oleh peserta).
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            wire:click="rerunJudge"
                            wire:loading.attr="disabled"
                            wire:target="rerunJudge"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-cell text-xs font-semibold bg-paper hover:bg-sheet text-ink border border-rule transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                        >
                            <svg wire:loading.remove wire:target="rerunJudge" class="w-3.5 h-3.5 text-brand-deep" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <svg wire:loading wire:target="rerunJudge" class="w-3.5 h-3.5 animate-spin text-brand-deep" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="rerunJudge">Uji Ulang Sistem</span>
                            <span wire:loading wire:target="rerunJudge">Menjalankan Uji...</span>
                        </button>

                        @if($submission->test_score !== null)
                            <button
                                type="button"
                                wire:click="applyTestScore"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-cell text-xs font-bold bg-brand text-sheet hover:bg-brand-deep transition-colors shadow-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Terapkan Skor ({{ $submission->test_score }})</span>
                            </button>
                        @endif
                    </div>
                </div>

                @if($submission->test_results)
                    {{-- Summary Metrics --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-3 bg-paper rounded-cell border border-rule">
                            <span class="text-[11px] text-ink-muted uppercase font-semibold block">Skor Sistem</span>
                            <span class="text-xl font-bold font-mono text-brand-deep tabular-nums">
                                {{ $submission->test_score ?? '—' }} <span class="text-xs font-normal text-ink-muted">/ 100</span>
                            </span>
                        </div>

                        <div class="p-3 bg-paper rounded-cell border border-rule">
                            <span class="text-[11px] text-ink-muted uppercase font-semibold block">Kriteria Lulus</span>
                            <span class="text-xl font-bold font-mono text-pass tabular-nums">
                                {{ $submission->test_results['passed'] ?? 0 }} <span class="text-xs font-normal text-ink-muted">/ {{ $submission->test_results['total'] ?? 0 }} Skenario</span>
                            </span>
                        </div>

                        <div class="p-3 bg-paper rounded-cell border border-rule">
                            <span class="text-[11px] text-ink-muted uppercase font-semibold block">Persentase Sukses</span>
                            <span class="text-xl font-bold font-mono text-ink tabular-nums">
                                {{ $submission->test_results['percentage'] ?? 0 }}%
                            </span>
                        </div>
                    </div>

                    {{-- Breakdown Categories --}}
                    @if(isset($submission->test_results['details']))
                        <div class="space-y-2">
                            <h3 class="text-xs font-bold text-ink uppercase tracking-wider">
                                Rincian Pengujian API & Skenario Playwright E2E:
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-mono">
                                @foreach($submission->test_results['details'] as $category => $statusVal)
                                    <div class="flex items-center justify-between p-2 rounded-cell bg-paper border border-rule">
                                        <span class="text-ink-muted capitalize">{{ str_replace('_', ' ', $category) }}</span>
                                        <span class="font-bold text-ink bg-sheet px-2 py-0.5 rounded-cell border border-rule">
                                            {{ $statusVal }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Admin Diagnostics Logs --}}
                    @if(isset($submission->test_results['admin_logs']))
                        <div x-data="{ openLogs: false }" class="space-y-1.5 pt-1">
                            <button
                                type="button"
                                x-on:click="openLogs = !openLogs"
                                class="text-xs font-medium text-brand-deep hover:underline flex items-center gap-1 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                            >
                                <svg class="w-3.5 h-3.5 transition-transform" :class="openLogs ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                                <span>Lihat Log Diagnostik Runner Internal (Admin/Mentor Only)</span>
                            </button>

                            <div x-show="openLogs" class="p-3 bg-sheet rounded-cell border border-rule text-xs font-mono space-y-1 overflow-x-auto text-ink">
                                @foreach($submission->test_results['admin_logs'] as $logKey => $logVal)
                                    <div class="flex justify-between border-b border-rule/30 py-0.5">
                                        <span class="text-ink-muted">{{ $logKey }}:</span>
                                        <span class="font-bold {{ is_bool($logVal) ? ($logVal ? 'text-pass' : 'text-brand-deep') : 'text-ink' }}">
                                            {{ is_bool($logVal) ? ($logVal ? 'TRUE' : 'FALSE') : (is_array($logVal) ? json_encode($logVal) : $logVal) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="p-4 bg-paper rounded-cell border border-rule text-center space-y-2">
                        <p class="text-xs text-ink-muted">
                            Arsip pengumpulan belum pernah dievaluasi otomatis oleh runner pengujian sistem.
                        </p>
                        <button
                            type="button"
                            wire:click="rerunJudge"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-cell text-xs font-semibold bg-brand text-sheet hover:bg-brand-deep transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                        >
                            Jalankan Pengujian Sistem Sekarang
                        </button>
                    </div>
                @endif
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
