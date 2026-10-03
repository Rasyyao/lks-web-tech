{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Student Dashboard (/beranda) --}}
{{-- Primary job of the screen: Give student immediate status on deadlines, latest attempt score, rank, and announcements. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, tint, pass, paper --}}
{{-- Type roles: Schibsted Grotesk for headings and body; tabular-nums for scores and deadlines; JetBrains Mono for codes --}}
{{-- Layout idea: Strict PRD content order without SaaS heroes or multi-colored stat boxes --}}
{{-- What I changed after the "would any app have this?" check: Removed gradient banners, "Welcome back 👋" cards, and 4-stat colorful tiles; replaced with clean school notice list and exam progression rows. --}}

<div class="space-y-6">
    {{-- Header title without marketing greeting --}}
    <div>
        <h1 class="text-2xl font-bold text-ink">
            {{ __('general.dashboard') }}
        </h1>
        <p class="text-xs text-ink-muted">
            {{ $user->name }} — {{ $user->cohorts->pluck('name')->join(', ') ?: 'Siswa' }}
        </p>
    </div>

    {{-- 1. Announcements (max 2) --}}
    @if($announcements->isNotEmpty())
        <div class="space-y-3">
            @foreach($announcements as $announcement)
                <div class="bg-sheet border border-rule border-l-[3px] border-l-brand rounded-panel p-4 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-brand-deep">Pengumuman</span>
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

    {{-- 2. Modules in progress with deadlines --}}
    <section aria-labelledby="modules-heading" class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 id="modules-heading" class="text-lg font-bold text-ink">
                Modul Berjalan & Tenggat
            </h2>
            <a href="{{ route('modules.index') }}" class="text-xs text-ink-muted hover:text-brand-deep underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                Semua modul
            </a>
        </div>

        @if($modules->isEmpty())
            <x-panel class="text-center py-6 text-sm text-ink-muted">
                Tidak ada modul yang sedang berjalan saat ini.
            </x-panel>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($modules as $mod)
                    @php
                        $mySubmission = $mod->submissions->sortByDesc('manual_score')->first();
                    @endphp
                    <x-panel class="flex flex-col justify-between space-y-3">
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs text-ink-muted">
                                <span class="uppercase font-medium tracking-wider">{{ $mod->track->label() }}</span>
                                <span class="tabular-nums">Level {{ $mod->level }}</span>
                            </div>
                            <h3 class="text-base font-bold text-ink hover:text-brand-deep">
                                <a href="{{ route('modules.show', $mod->slug) }}" class="focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                                    {{ $mod->title }}
                                </a>
                            </h3>
                            <p class="text-xs text-ink-muted line-clamp-2">
                                {{ $mod->summary }}
                            </p>
                        </div>

                        <div class="pt-3 border-t border-rule flex items-center justify-between text-xs">
                            <div>
                                <span class="text-ink-muted block">Tenggat:</span>
                                <time class="font-medium text-ink tabular-nums">
                                    {{ $mod->closes_at?->translatedFormat('d M Y, H:i') }}
                                </time>
                            </div>
                            <div class="text-right">
                                @if($mySubmission)
                                    <span class="text-xs text-pass font-medium block">
                                        {{ $mySubmission->manual_score !== null ? 'Nilai: ' . $mySubmission->manual_score : 'Diterima' }}
                                    </span>
                                @else
                                    <a href="{{ route('modules.show', $mod->slug) }}" class="text-brand-deep font-medium hover:underline">
                                        Buka modul
                                    </a>
                                @endif
                            </div>
                        </div>
                    </x-panel>
                @endforeach
            </div>
        @endif
    </section>

    {{-- 3. Latest attempt --}}
    <section aria-labelledby="attempt-heading" class="space-y-3">
        <h2 id="attempt-heading" class="text-lg font-bold text-ink">
            Percobaan Latihan Terakhir
        </h2>

        @if($latestAttempt)
            <x-panel class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-xs text-ink-muted">
                        <span>Latihan Mandiri</span>
                        <span>•</span>
                        <time class="tabular-nums">
                            {{ $latestAttempt->submitted_at?->translatedFormat('d M Y, H:i') }}
                        </time>
                    </div>
                    <p class="text-sm font-medium text-ink">
                        {{ $latestAttempt->question_count }} soal diselesaikan
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <span class="text-xs text-ink-muted block">Skor</span>
                        <span class="text-xl font-bold text-ink tabular-nums">
                            {{ $latestAttempt->points_earned }} poin
                        </span>
                    </div>
                    <x-button variant="secondary" size="sm" :href="route('practice.result', $latestAttempt->id)">
                        Buka Lembar Jawaban
                    </x-button>
                </div>
            </x-panel>
        @else
            <x-panel class="text-center py-6 text-sm text-ink-muted">
                Belum ada latihan yang diselesaikan.
                <div class="mt-2">
                    <x-button :href="route('practice.start')" size="sm">
                        Mulai Latihan
                    </x-button>
                </div>
            </x-panel>
        @endif
    </section>

    {{-- 4. Rank row --}}
    <section aria-labelledby="rank-heading" class="space-y-3">
        <h2 id="rank-heading" class="text-lg font-bold text-ink">
            Peringkat Anda
        </h2>

        <x-panel class="bg-tint border border-rule border-l-[3px] border-l-brand flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-xs text-brand-deep font-semibold">Posisi Papan Peringkat</span>
                <p class="text-base font-bold text-ink">
                    @if($rank)
                        Peringkat ke-{{ $rank }}
                    @else
                        Belum ada peringkat
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-6">
                <div class="text-right">
                    <span class="text-xs text-ink-muted block">Total Poin</span>
                    <span class="text-lg font-bold text-ink tabular-nums">
                        {{ $studentEntry?->total ?? 0 }}
                    </span>
                </div>
                <x-button variant="secondary" size="sm" :href="route('leaderboard')">
                    Lihat Papan Peringkat
                </x-button>
            </div>
        </x-panel>
    </section>

    {{-- 5. Recommended topic --}}
    @if($recommendedTopic)
        <section aria-labelledby="topic-heading" class="space-y-3">
            <h2 id="topic-heading" class="text-lg font-bold text-ink">
                Topik Disarankan
            </h2>

            <x-panel class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-ink">
                        {{ $recommendedTopic->name }}
                    </h3>
                    <p class="text-xs text-ink-muted">
                        {{ $recommendedTopic->questions_count }} soal tersedia di bank soal
                    </p>
                </div>
                <x-button :href="route('practice.start', ['topic' => $recommendedTopic->id])" size="sm">
                    Latih Topik Ini
                </x-button>
            </x-panel>
        </section>
    @endif
</div>
