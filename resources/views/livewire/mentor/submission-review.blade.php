{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Mentor Submission Review Widescreen (/mentor/pengumpulan/{id}) --}}
{{-- Primary job of the screen: Comprehensive grading interface for mentors with automated judge test evaluation, Playwright E2E diagnostic logs, student history, and manual score/feedback submission. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, pass, paper, tint, gold --}}
{{-- Type roles: Schibsted Grotesk for form and headings; tabular-nums for scores and hashes; JetBrains Mono for filenames and SHA-256 --}}
{{-- Layout idea: Widescreen 1400px two-column layout matching student module view (8-col automated evaluation and grading form on left, 4-col sticky student info, file download, and history on right). Margin left and right identical to student view. --}}

<div class="space-y-6">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="Breadcrumb">
        <ol class="flex items-center gap-1.5 text-xs text-ink-muted">
            <li>
                <a href="{{ route('mentor.submissions') }}" class="hover:text-brand-deep transition-colors inline-flex items-center gap-1 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ __('submissions.title') }}</span>
                </a>
            </li>
            <li aria-hidden="true" class="text-rule">/</li>
            <li class="text-ink font-medium truncate max-w-xs">
                {{ $submission->user->name }} — Percobaan #{{ $submission->attempt_no }}
            </li>
        </ol>
    </nav>

    {{-- Header Banner --}}
    <x-panel class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-ink">
                        Penilaian Tugas: {{ $submission->user->name }}
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-cell font-mono text-xs font-bold bg-sheet border border-rule text-ink">
                        Percobaan #{{ $submission->attempt_no }}
                    </span>
                </div>
                <p class="text-xs text-ink-muted mt-1">
                    Modul: <strong class="text-ink">{{ $submission->module->title }}</strong> &bull;
                    Waktu Pengumpulan: <time class="tabular-nums font-medium text-ink">{{ $submission->created_at->translatedFormat('d F Y, H:i') }}</time>
                </p>
            </div>

            <div>
                @if($submission->status === \App\Enums\SubmissionStatus::Received)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-cell text-xs font-bold bg-tint text-brand-deep border border-brand-deep/20">
                        {{ __('submissions.waiting_review') }}
                    </span>
                @elseif($submission->status === \App\Enums\SubmissionStatus::Graded)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-cell text-xs font-bold bg-pass/10 text-pass border border-pass/20">
                        {{ __('submissions.status_graded') }}: {{ $submission->manual_score }}/100
                    </span>
                @elseif($submission->status === \App\Enums\SubmissionStatus::Rejected)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-cell text-xs font-bold bg-sheet text-brand-deep border border-rule">
                        {{ __('submissions.status_rejected') }}
                    </span>
                @endif
            </div>
        </div>
    </x-panel>

    {{-- Two Column Widescreen Layout (8 Cols Left, 4 Cols Right) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {{-- LEFT COLUMN: Automated Evaluation & Mentor Scoring Form (8 Cols) --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Panel: Automated System Evaluation (Admin & Mentor Engine: Playwright E2E & API Runner) --}}
            <x-panel class="space-y-5 border-2 border-brand/20 bg-sheet">
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
                            Hasil eksekusi otomatis skenario pengujian server-side & client-side (tertutup dari sisi siswa).
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
                        <div class="p-3.5 bg-paper rounded-cell border border-rule">
                            <span class="text-[11px] text-ink-muted uppercase font-semibold block">Skor Sistem</span>
                            <span class="text-2xl font-bold font-mono text-brand-deep tabular-nums">
                                {{ $submission->test_score ?? '—' }} <span class="text-xs font-normal text-ink-muted">/ 100</span>
                            </span>
                        </div>

                        <div class="p-3.5 bg-paper rounded-cell border border-rule">
                            <span class="text-[11px] text-ink-muted uppercase font-semibold block">Kriteria Lulus</span>
                            <span class="text-2xl font-bold font-mono text-pass tabular-nums">
                                {{ $submission->test_results['passed'] ?? 0 }} <span class="text-xs font-normal text-ink-muted">/ {{ $submission->test_results['total'] ?? 0 }} Skenario</span>
                            </span>
                        </div>

                        <div class="p-3.5 bg-paper rounded-cell border border-rule">
                            <span class="text-[11px] text-ink-muted uppercase font-semibold block">Tingkat Kelulusan</span>
                            <span class="text-2xl font-bold font-mono text-ink tabular-nums">
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
                                    <div class="flex items-center justify-between p-2.5 rounded-cell bg-paper border border-rule">
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
                        <div x-data="{ openLogs: false }" class="space-y-2 pt-2 border-t border-rule/50">
                            <button
                                type="button"
                                x-on:click="openLogs = !openLogs"
                                class="text-xs font-medium text-brand-deep hover:underline flex items-center gap-1.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                            >
                                <svg class="w-3.5 h-3.5 transition-transform" :class="openLogs ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                                <span>Lihat Log Diagnostik Runner Internal (Admin/Mentor Only)</span>
                            </button>

                            <div x-show="openLogs" class="p-3 bg-sheet rounded-cell border border-rule text-xs font-mono space-y-1.5 overflow-x-auto text-ink">
                                @foreach($submission->test_results['admin_logs'] as $logKey => $logVal)
                                    <div class="flex justify-between border-b border-rule/30 py-1">
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
                    <div class="p-5 bg-paper rounded-cell border border-rule text-center space-y-3">
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

            {{-- Panel: Scoring and Feedback Form --}}
            <x-panel class="space-y-6">
                <div class="flex items-center justify-between border-b border-rule pb-3">
                    <div>
                        <h2 class="text-base font-bold text-ink">
                            Formulir Penilaian Mentor
                        </h2>
                        <p class="text-xs text-ink-muted">
                            Simpan nilai resmi dan catatan feedback mentor untuk memperbarui skor di papan peringkat.
                        </p>
                    </div>

                    @if($submission->test_score !== null)
                        <button
                            type="button"
                            wire:click="applyTestScore"
                            class="text-xs text-brand-deep hover:underline font-semibold flex items-center gap-1"
                        >
                            <span>Salin Skor Otomatis ({{ $submission->test_score }})</span>
                        </button>
                    @endif
                </div>

                <form wire:submit="saveReview" class="space-y-5">
                    {{-- Status Choice --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-ink">
                            Keputusan Mentor:
                        </label>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer text-sm font-medium">
                                <input
                                    type="radio"
                                    wire:model.live="status"
                                    value="graded"
                                    class="text-brand focus:ring-brand-deep"
                                >
                                <span>Beri Nilai (Sudah Dinilai)</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer text-sm font-medium">
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
                        <div class="space-y-2 p-4 bg-paper rounded-cell border border-rule">
                            <div class="flex items-center justify-between">
                                <label for="manual_score" class="block text-sm font-semibold text-ink">
                                    {{ __('submissions.score') }} Resmi (0–100)
                                    <span class="text-brand-deep">*</span>
                                </label>
                                <span class="text-xs text-ink-muted">Nilai ini masuk ke akumulasi papan peringkat siswa</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <input
                                    type="number"
                                    id="manual_score"
                                    wire:model="manual_score"
                                    min="0"
                                    max="100"
                                    class="w-32 rounded-cell border border-rule px-3 py-2 text-base bg-sheet text-ink tabular-nums font-bold focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                                    placeholder="0-100"
                                >
                                <span class="text-xs text-ink-muted">/ 100 poin maksimal</span>
                            </div>
                            @error('manual_score')
                                <p class="text-xs text-brand-deep mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    {{-- Feedback / Catatan Mentor --}}
                    <div class="space-y-2">
                        <label for="feedback_md" class="block text-sm font-semibold text-ink">
                            {{ $status === 'rejected' ? 'Alasan Penolakan' : __('submissions.feedback') }}
                            @if($status === 'rejected')
                                <span class="text-brand-deep">*</span>
                            @endif
                        </label>
                        <p class="text-xs text-ink-muted">
                            Catatan ini akan tampil langsung di halaman riwayat modul siswa (format Markdown didukung).
                        </p>
                        <textarea
                            id="feedback_md"
                            wire:model="feedback_md"
                            rows="6"
                            placeholder="Tuliskan catatan teknis mengenai kode sumber, efisiensi algoritma, arsitektur database, dan poin perbaikan untuk siswa..."
                            class="w-full rounded-cell border border-rule px-3 py-2.5 text-sm bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep font-sans"
                        ></textarea>
                        @error('feedback_md')
                            <p class="text-xs text-brand-deep mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-rule">
                        <a href="{{ route('mentor.submissions') }}" class="text-xs text-ink-muted hover:text-ink">
                            Batal & Kembali
                        </a>
                        <x-button type="submit">
                            {{ __('submissions.save_score') }}
                        </x-button>
                    </div>
                </form>
            </x-panel>
        </div>

        {{-- RIGHT COLUMN: Sticky Sidebar: Student Profile, File Info & History (4 Cols) --}}
        <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6">
            {{-- Panel 1: File & Student Metadata --}}
            <x-panel class="space-y-4">
                <div class="border-b border-rule pb-2">
                    <h2 class="text-sm font-bold text-ink">
                        {{ __('submissions.file_info') }} & Siswa
                    </h2>
                    <p class="text-[11px] text-ink-muted">Identitas pengirim dan arsip kode sumber</p>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-ink-muted block text-[11px]">Nama Siswa:</span>
                        <strong class="text-ink text-sm block">{{ $submission->user->name }}</strong>
                        <span class="text-ink-muted font-mono text-[11px]">{{ $submission->user->username }} (NIS)</span>
                    </div>

                    <div class="pt-2 border-t border-rule/50">
                        <span class="text-ink-muted block text-[11px]">Kelas / Cohort:</span>
                        <span class="text-ink font-medium">{{ $submission->user->cohorts->pluck('name')->join(', ') ?: '—' }}</span>
                    </div>

                    <div class="pt-2 border-t border-rule/50">
                        <span class="text-ink-muted block text-[11px]">Nama Berkas Proyek:</span>
                        <span class="text-ink font-mono break-all font-semibold">{{ $submission->original_name }}</span>
                    </div>

                    <div class="pt-2 border-t border-rule/50 flex justify-between items-center">
                        <span class="text-ink-muted text-[11px]">Ukuran Berkas:</span>
                        <span class="text-ink tabular-nums font-semibold">{{ number_format($submission->size / 1024, 1) }} KB</span>
                    </div>

                    <div class="pt-2 border-t border-rule/50">
                        <span class="text-ink-muted block text-[11px]">Hash SHA-256:</span>
                        <span class="text-ink-muted font-mono text-[10px] break-all select-all block bg-paper p-1.5 rounded-cell border border-rule mt-0.5">
                            {{ $submission->sha256 }}
                        </span>
                    </div>
                </div>

                <div class="pt-3 border-t border-rule">
                    <x-button :href="route('download.submission', $submission->id)" class="w-full text-center">
                        {{ __('submissions.download_zip') }}
                    </x-button>
                </div>
            </x-panel>

            {{-- Panel 2: Riwayat Pengumpulan Siswa --}}
            <x-panel class="space-y-3">
                <div class="flex items-center justify-between border-b border-rule pb-2">
                    <div>
                        <h2 class="text-sm font-bold text-ink">
                            {{ __('submissions.history') }} Siswa
                        </h2>
                        <p class="text-[11px] text-ink-muted">Percobaan lain pada modul ini</p>
                    </div>
                    <span class="text-[11px] font-mono text-ink-muted">{{ $history->count() }} Percobaan Lain</span>
                </div>

                @if($history->isEmpty())
                    <p class="text-xs text-ink-muted py-3 text-center">
                        Ini adalah percobaan pertama siswa untuk modul ini.
                    </p>
                @else
                    <ul class="divide-y divide-rule" role="list">
                        @foreach($history as $item)
                            <li class="py-2.5 space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-ink">
                                        Percobaan #{{ $item->attempt_no }}
                                    </span>
                                    <time class="text-ink-muted tabular-nums text-[11px]">
                                        {{ $item->created_at->translatedFormat('d M, H:i') }}
                                    </time>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-ink-muted text-[11px]">
                                        Nilai: <strong class="text-ink tabular-nums">{{ $item->manual_score !== null ? $item->manual_score : '—' }}</strong>
                                    </span>
                                    <a
                                        href="{{ route('mentor.submission.review', $item->id) }}"
                                        class="text-brand-deep hover:underline text-[11px] font-medium"
                                    >
                                        Buka Review &rarr;
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-panel>

            {{-- Panel 3: Rubrik & Aturan Modul --}}
            <x-panel class="space-y-2.5 bg-paper/50">
                <h3 class="text-xs font-bold text-ink uppercase tracking-wider">
                    Pedoman Penilaian Juri LKS
                </h3>
                <ul class="text-xs text-ink-muted space-y-1.5 list-disc list-inside">
                    <li>Gunakan hasil uji otomatis Playwright & API sebagai acuan dasar (0–100).</li>
                    <li>Periksa kerapian struktur kode, modularitas, dan best practices.</li>
                    <li>Nilai mentor otomatis memperbarui akumulasi skor pada Leaderboard.</li>
                </ul>
            </x-panel>
        </div>
    </div>
</div>
