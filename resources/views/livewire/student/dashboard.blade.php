{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Student Dashboard (/beranda) Redesigned --}}
{{-- Primary job of the screen: Provide students with a comprehensive, motivating learning command center displaying their module submission status, learning roadmap progress, active study hours, and leaderboard rank alongside urgent tasks and deadlines. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, tint, pass, paper, gold --}}
{{-- Aesthetic: Official academic exam platform with modern clean card hierarchy, subtle micro-accents, and clear status indicators. --}}

<div class="space-y-7">
    {{-- Header title with academic student context --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-rule pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight">
                {{ __('general.dashboard') }} Siswa
            </h1>
            <p class="text-xs sm:text-sm text-ink-muted mt-0.5">
                {{ $user->name }} &bull; <span class="font-mono text-ink">{{ $user->username }}</span> &bull; {{ $user->cohorts->pluck('name')->join(', ') ?: 'Peserta Seleksi' }}
            </p>
        </div>
        <div class="text-xs text-ink-muted flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-pass"></span>
            <span class="font-medium text-ink tabular-nums">{{ now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

    {{-- Announcements (if any) --}}
    @if($announcements->isNotEmpty())
        <div class="space-y-3">
            @foreach($announcements as $announcement)
                <div class="bg-sheet border border-rule border-l-4 border-l-brand rounded-panel p-4 shadow-sm space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-deep">Pengumuman Resmi</span>
                        <time class="text-xs text-ink-muted tabular-nums">
                            {{ $announcement->published_at?->translatedFormat('d M Y') }}
                        </time>
                    </div>
                    <h2 class="text-base font-bold text-ink">
                        {{ $announcement->title }}
                    </h2>
                    <div class="text-sm text-ink-muted prose max-w-none">
                        {!! Str::markdown($announcement->body_md) !!}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- 4-Card Student Statistics Grid --}}
    <section aria-labelledby="stats-heading" class="space-y-3">
        <h2 id="stats-heading" class="text-xs font-bold uppercase tracking-wider text-ink-muted">
            Ringkasan Statistik Pembelajaran
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Stat 1: Modul Selesai --}}
            <x-panel class="flex flex-col justify-between p-4 bg-sheet border border-rule shadow-sm relative overflow-hidden group hover:border-rule/80 transition-colors">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-ink-muted">Modul Selesai</span>
                        <div class="w-7 h-7 rounded-cell bg-pass/10 flex items-center justify-center text-pass">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl sm:text-3xl font-extrabold text-ink tabular-nums">
                            {{ $submittedModulesCount }}
                        </span>
                        <span class="text-xs font-medium text-ink-muted">
                            / {{ $totalModulesCount }} Modul
                        </span>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="w-full bg-paper rounded-full h-1.5 overflow-hidden border border-rule/50">
                        <div class="bg-pass h-1.5 rounded-full transition-all duration-500" style="width: {{ $moduleProgressPercent }}%"></div>
                    </div>
                </div>

                <div class="pt-3 mt-2 border-t border-rule/60 flex items-center justify-between text-[11px]">
                    <span class="text-ink-muted">
                        {{ $gradedModulesCount }} Dinilai &bull; {{ $pendingModulesCount }} Review
                    </span>
                    <span class="font-bold text-pass tabular-nums bg-pass/10 px-1.5 py-0.5 rounded-cell">
                        {{ $moduleProgressPercent }}%
                    </span>
                </div>
            </x-panel>

            {{-- Stat 2: Progres Belajar (Roadmap) --}}
            <x-panel class="flex flex-col justify-between p-4 bg-sheet border border-rule shadow-sm relative overflow-hidden group hover:border-rule/80 transition-colors">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-ink-muted">Roadmap Belajar</span>
                        <div class="w-7 h-7 rounded-cell bg-brand/10 flex items-center justify-center text-brand-deep">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl sm:text-3xl font-extrabold text-ink tabular-nums">
                            {{ $completedLevelsCount }}
                        </span>
                        <span class="text-xs font-medium text-ink-muted">
                            / {{ $totalLevelsCount }} Level
                        </span>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="w-full bg-paper rounded-full h-1.5 overflow-hidden border border-rule/50">
                        <div class="bg-brand h-1.5 rounded-full transition-all duration-500" style="width: {{ $roadmapPercent }}%"></div>
                    </div>
                </div>

                <div class="pt-3 mt-2 border-t border-rule/60 flex items-center justify-between text-[11px]">
                    <span class="text-ink-muted truncate max-w-[140px]" title="{{ $currentLevel?->title ?? 'Semua Tuntas' }}">
                        {{ $currentLevel ? $currentLevel->title : 'Tuntas' }}
                    </span>
                    <a href="{{ $currentLevel ? route('roadmap.level', $currentLevel->slug) : route('roadmap.index') }}" class="font-bold text-brand-deep hover:underline focus-visible:outline-2 focus-visible:outline-brand-deep">
                        Lanjut &rarr;
                    </a>
                </div>
            </x-panel>

            {{-- Stat 3: Keaktifan Belajar --}}
            <x-panel class="flex flex-col justify-between p-4 bg-sheet border border-rule shadow-sm relative overflow-hidden group hover:border-rule/80 transition-colors">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-ink-muted">Keaktifan Belajar</span>
                        <div class="w-7 h-7 rounded-cell bg-amber-500/10 flex items-center justify-center text-amber-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl sm:text-3xl font-extrabold text-ink tabular-nums">
                            {{ $activeHours }}
                        </span>
                        <span class="text-xs font-medium text-ink-muted">
                            Jam Belajar
                        </span>
                    </div>

                    <div class="text-[11px] text-ink-muted flex items-center gap-1.5">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-pass"></span>
                        <span>{{ $activeDaysCount }} Hari aktif tercatat</span>
                    </div>
                </div>

                <div class="pt-3 mt-2 border-t border-rule/60 flex items-center justify-between text-[11px]">
                    <span class="text-ink-muted">Sesi hari ini:</span>
                    <span class="font-semibold text-ink tabular-nums">
                        {{ $todayActiveMinutes }} Menit
                    </span>
                </div>
            </x-panel>

            {{-- Stat 4: Peringkat Siswa --}}
            <x-panel class="flex flex-col justify-between p-4 bg-sheet border border-rule shadow-sm relative overflow-hidden group hover:border-rule/80 transition-colors">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-ink-muted">Posisi Peringkat</span>
                        <div class="w-7 h-7 rounded-cell bg-gold/15 flex items-center justify-center text-amber-700">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl sm:text-3xl font-extrabold text-brand-deep tabular-nums">
                            #{{ $rank ?? '-' }}
                        </span>
                        <span class="text-xs font-medium text-ink-muted">
                            dari {{ $totalStudentsCount }} Siswa
                        </span>
                    </div>

                    <div class="text-[11px] text-ink-muted">
                        Total: <strong class="text-ink tabular-nums">{{ $studentEntry?->total ?? 0 }}</strong> Poin Akumulasi
                    </div>
                </div>

                <div class="pt-3 mt-2 border-t border-rule/60 flex items-center justify-between text-[11px]">
                    <span class="text-ink-muted">
                        Modul: {{ $studentEntry?->module_points ?? 0 }} &bull; Kuis: {{ $studentEntry?->question_points ?? 0 }}
                    </span>
                    <a href="{{ route('leaderboard') }}" class="font-bold text-brand-deep hover:underline focus-visible:outline-2 focus-visible:outline-brand-deep">
                        Papan Skor &rarr;
                    </a>
                </div>
            </x-panel>
        </div>
    </section>

    {{-- Modul Berjalan & Tenggat (Redesigned with Client/Server Category, Timestamps & Submission Indicators) --}}
    <section aria-labelledby="modules-heading" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 id="modules-heading" class="text-lg font-bold text-ink">
                    Modul Praktik & Tenggat Tugas
                </h2>
                <p class="text-xs text-ink-muted">
                    Modul seleksi LKS Web Technologies yang ditugaskan ke kelas Anda
                </p>
            </div>
            <a href="{{ route('modules.index') }}" class="text-xs font-semibold text-brand-deep hover:underline inline-flex items-center gap-1 focus-visible:outline-2 focus-visible:outline-brand-deep">
                <span>Lihat Semua Modul</span>
                <span>&rarr;</span>
            </a>
        </div>

        @if($modules->isEmpty())
            <x-panel class="text-center py-8 text-sm text-ink-muted">
                Tidak ada modul yang sedang ditugaskan saat ini.
            </x-panel>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                @foreach($modules as $mod)
                    @php
                        $mySubmission = $mod->submissions->sortByDesc('manual_score')->first();
                        $isSubmitted = $mod->submissions->isNotEmpty();
                        $isServer = $mod->track === \App\Enums\ModuleTrack::Server;
                    @endphp
                    <div class="flex flex-col justify-between rounded-panel p-5 transition-all duration-200 {{ $isSubmitted ? 'bg-sheet border-2 border-pass/40 shadow-sm ring-1 ring-pass/10' : 'bg-sheet border border-rule shadow-sm hover:border-rule/90' }}">
                        <div class="space-y-3">
                            {{-- Top Header Badges: Category Pill & Submission State --}}
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5">
                                    {{-- Track Category Badge --}}
                                    @if($isServer)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-cell text-[11px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2" />
                                            </svg>
                                            Server-side
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-cell text-[11px] font-bold uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            Client-side
                                        </span>
                                    @endif

                                    <span class="text-[11px] font-mono font-medium text-ink-muted bg-paper px-1.5 py-0.5 rounded-cell border border-rule">
                                        Lvl {{ $mod->level }}
                                    </span>
                                </div>

                                {{-- Submission Status Badge --}}
                                @if($isSubmitted)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-cell bg-pass/10 text-pass text-[11px] font-bold border border-pass/30 shrink-0">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Sudah Dikumpulkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-cell bg-paper text-ink-muted text-[11px] font-medium border border-rule shrink-0">
                                        Belum Dikumpulkan
                                    </span>
                                @endif
                            </div>

                            {{-- Title & Summary --}}
                            <div>
                                <h3 class="text-base font-bold text-ink hover:text-brand-deep transition-colors line-clamp-2">
                                    <a href="{{ route('modules.show', $mod->slug) }}" class="focus-visible:outline-2 focus-visible:outline-brand-deep">
                                        {{ $mod->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-ink-muted line-clamp-2 mt-1 leading-relaxed">
                                    {{ $mod->summary }}
                                </p>
                            </div>

                            {{-- Timestamp Metadata Details --}}
                            <div class="p-2.5 rounded-cell bg-paper border border-rule/70 space-y-1.5 text-xs">
                                <div class="flex items-center justify-between text-ink-muted">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-ink-muted shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        Diupload:
                                    </span>
                                    <time class="font-medium text-ink tabular-nums">
                                        {{ $mod->opens_at?->translatedFormat('l, d M Y') ?? '—' }}
                                    </time>
                                </div>

                                <div class="flex items-center justify-between text-ink-muted border-t border-rule/40 pt-1">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-brand-deep shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Batas Tenggat:
                                    </span>
                                    <time class="font-bold text-brand-deep tabular-nums">
                                        {{ $mod->closes_at?->translatedFormat('l, d M Y, H:i') ?? '—' }}
                                    </time>
                                </div>
                            </div>
                        </div>

                        {{-- Footer with Score/Status & Action Button --}}
                        <div class="pt-4 mt-3 border-t border-rule flex items-center justify-between gap-2">
                            <div>
                                @if($mySubmission)
                                    @if($mySubmission->status === \App\Enums\SubmissionStatus::Graded && $mySubmission->manual_score !== null)
                                        <span class="text-[10px] text-ink-muted block uppercase tracking-wider font-semibold">Nilai Mentor</span>
                                        <span class="text-sm font-extrabold text-pass tabular-nums">
                                            {{ $mySubmission->manual_score }} / 100
                                        </span>
                                    @else
                                        <span class="text-[10px] text-ink-muted block uppercase tracking-wider font-semibold">Status Tugas</span>
                                        <span class="text-xs font-semibold text-ink">
                                            Menunggu Review
                                        </span>
                                    @endif
                                @else
                                    <span class="text-[10px] text-ink-muted block uppercase tracking-wider font-semibold">Durasi Modul</span>
                                    <span class="text-xs font-medium text-ink tabular-nums">
                                        {{ $mod->duration_minutes }} Menit
                                    </span>
                                @endif
                            </div>

                            <div>
                                @if($isSubmitted)
                                    <x-button variant="secondary" size="sm" :href="route('modules.show', $mod->slug)">
                                        Lihat Kiriman
                                    </x-button>
                                @else
                                    <a
                                        href="{{ route('modules.show', $mod->slug) }}"
                                        class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-cell transition-colors"
                                        style="color: #ffffff !important; background-color: #c92a2a !important;"
                                    >
                                        Buka Modul &rarr;
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Bottom Section: Percobaan Latihan Terakhir & Topik Latihan --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Percobaan Latihan Terakhir (7 cols) --}}
        <div class="lg:col-span-7 space-y-3">
            <h2 class="text-base font-bold text-ink">
                Percobaan Latihan Terakhir
            </h2>

            @if($latestAttempt)
                <x-panel class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 text-xs text-ink-muted">
                            <span class="font-medium text-ink">Latihan Mandiri</span>
                            <span>&bull;</span>
                            <time class="tabular-nums">
                                {{ $latestAttempt->submitted_at?->translatedFormat('d M Y, H:i') }}
                            </time>
                        </div>
                        <p class="text-sm font-semibold text-ink">
                            {{ $latestAttempt->question_count }} soal latihan telah diselesaikan
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-ink-muted block">Skor Latihan</span>
                            <span class="text-xl font-extrabold text-ink tabular-nums">
                                {{ $latestAttempt->points_earned }} <span class="text-xs font-normal text-ink-muted">poin</span>
                            </span>
                        </div>
                        <x-button variant="secondary" size="sm" :href="route('practice.result', $latestAttempt->id)">
                            Lembar Jawaban
                        </x-button>
                    </div>
                </x-panel>
            @else
                <x-panel class="text-center py-6 text-sm text-ink-muted">
                    Belum ada latihan mandiri yang diselesaikan.
                    <div class="mt-2">
                        <x-button :href="route('practice.start')" size="sm">
                            Mulai Latihan
                        </x-button>
                    </div>
                </x-panel>
            @endif
        </div>

        {{-- Topik Disarankan (5 cols) --}}
        <div class="lg:col-span-5 space-y-3">
            <h2 class="text-base font-bold text-ink">
                Latihan Bank Soal
            </h2>

            @if($recommendedTopic)
                <x-panel class="flex flex-col justify-between p-4 space-y-3">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-bold text-brand-deep">Topik Disarankan</span>
                        <h3 class="text-base font-bold text-ink mt-0.5">
                            {{ $recommendedTopic->name }}
                        </h3>
                        <p class="text-xs text-ink-muted mt-1">
                            {{ $recommendedTopic->questions_count }} soal tersedia untuk melatih pemahaman konsep teknis.
                        </p>
                    </div>

                    <div class="pt-2 border-t border-rule flex justify-end">
                        <x-button :href="route('practice.start', ['topic' => $recommendedTopic->id])" size="sm">
                            Mulai Latihan Topik Ini
                        </x-button>
                    </div>
                </x-panel>
            @endif
        </div>
    </div>
</div>
