{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Interactive Client Roadmap Overview (/belajar) --}}
{{-- Aesthetic: Exam syllabus & mastery board for SMK Telkom Purwokerto (white sheets, charcoal ink, brand accents) --}}
<div class="py-6 sm:py-8">
    <div class="mx-auto max-w-7xl xl:max-w-[1400px] px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Page Header --}}
        <div class="bg-sheet border border-rule rounded-panel p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2 max-w-3xl">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold uppercase tracking-wider bg-brand/10 text-brand-deep border border-brand/20">
                            Roadmap Interaktif Client-Side
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-paper text-ink-muted border border-rule">
                            Modul: Pin Map
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight">
                        Roadmap Belajar Mandiri: Pin Map
                    </h1>
                    <p class="text-sm sm:text-base text-ink-muted leading-relaxed">
                        Dari nol sampai siap menyelesaikan modul client LKS Web Technology. Baca materi level demi level, uji pemahamanmu dengan latihan coding langsung di browser, dan pantau progres belajarmu.
                    </p>
                </div>

                {{-- Action & Quick Stats Card --}}
                <div class="bg-paper border border-rule rounded-panel p-4 flex flex-col sm:flex-row lg:flex-col gap-4 min-w-[280px]">
                    <div>
                        <div class="flex justify-between items-baseline mb-1">
                            <span class="text-xs font-medium text-ink-muted uppercase">Kemajuan Belajar</span>
                            <span class="text-sm font-bold text-brand-deep font-mono">{{ $completedLevels }} / {{ $totalLevels }} Level ({{ $overallPercent }}%)</span>
                        </div>
                        <div class="w-full bg-rule h-2 rounded-full overflow-hidden">
                            <div class="bg-brand h-full transition-all duration-300" style="width: {{ $overallPercent }}%"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-rule text-xs text-ink-muted">
                        <span>Waktu Aktif: <strong class="text-ink font-mono">{{ $activeHours }} jam</strong></span>
                        <a href="{{ route('activity.my') }}" class="text-brand-deep font-medium hover:underline inline-flex items-center gap-1">
                            Aktivitas Saya
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    @if($nextLevel)
                        <a href="{{ route('roadmap.level', $nextLevel->slug) }}" class="inline-flex items-center justify-center px-4 py-2 bg-brand text-white text-sm font-medium rounded hover:bg-brand-deep transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                            Lanjutkan: {{ $nextLevel->title }} →
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Level Grid / Progression Line --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg sm:text-xl font-bold text-ink">
                    Tahapan Level (0 — 8)
                </h2>
                <span class="text-xs text-ink-muted">Perkiraan total: ~44 - 57 jam</span>
            </div>

            <div class="space-y-3">
                @foreach($levels as $level)
                    @php
                        $prog = $userProgress->get($level->slug);
                        $isCompleted = $prog?->is_completed ?? false;
                        $percent = $prog?->percent_complete ?? 0;
                        $checkpointsMarked = $prog?->checkpoints_marked ?? 0;
                        $exercisesPassed = $prog?->exercises_passed ?? 0;
                    @endphp
                    <div class="bg-sheet border {{ $isCompleted ? 'border-pass/40 bg-pass/5' : 'border-rule' }} rounded-panel p-5 hover:border-brand/40 transition-colors shadow-sm">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-start gap-4">
                                {{-- Status Indicator Badge --}}
                                <div class="w-10 h-10 shrink-0 rounded-full flex items-center justify-center font-bold text-sm font-mono {{ $isCompleted ? 'bg-pass text-white' : 'bg-paper text-ink border border-rule' }}">
                                    @if($isCompleted)
                                        ✓
                                    @else
                                        {{ $level->position }}
                                    @endif
                                </div>

                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-base sm:text-lg font-bold text-ink">
                                            <a href="{{ route('roadmap.level', $level->slug) }}" class="hover:text-brand-deep transition-colors">
                                                {{ $level->title }}
                                            </a>
                                        </h3>
                                        @if($level->estimated_time)
                                            <span class="text-xs bg-paper border border-rule text-ink-muted px-2 py-0.5 rounded font-mono">
                                                ⏱ {{ $level->estimated_time }}
                                            </span>
                                        @endif
                                        @if($isCompleted)
                                            <span class="text-xs bg-pass/10 text-pass border border-pass/30 px-2 py-0.5 rounded font-medium">
                                                Selesai
                                            </span>
                                        @endif
                                    </div>

                                    @if($level->goal)
                                        <p class="text-sm text-ink-muted leading-snug">
                                            <strong class="text-ink">Tujuan:</strong> {{ $level->goal }}
                                        </p>
                                    @endif

                                    {{-- Sub-metrics --}}
                                    <div class="flex flex-wrap items-center gap-4 text-xs text-ink-muted pt-1">
                                        <span>Materi: <strong>{{ $level->sections->count() }} bagian</strong></span>
                                        <span>Cek Pemahaman: <strong>{{ $checkpointsMarked }}/{{ $level->checkpoints->count() }} dipahami</strong></span>
                                        @if($level->exercises->isNotEmpty())
                                            <span>Latihan Coding: <strong class="text-brand-deep">{{ $exercisesPassed }}/{{ $level->exercises->count() }} lulus</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Right Action / Progress Bar --}}
                            <div class="flex md:flex-col items-center md:items-end justify-between md:justify-center gap-3 shrink-0 min-w-[160px]">
                                <div class="text-right w-full">
                                    <div class="flex justify-between md:justify-end gap-2 text-xs font-mono text-ink-muted mb-1">
                                        <span>Progres:</span>
                                        <span class="font-bold text-ink">{{ $percent }}%</span>
                                    </div>
                                    <div class="w-full bg-rule h-1.5 rounded-full overflow-hidden">
                                        <div class="{{ $isCompleted ? 'bg-pass' : 'bg-brand' }} h-full transition-all duration-300" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>

                                <a href="{{ route('roadmap.level', $level->slug) }}" class="inline-flex items-center gap-1 text-sm font-medium {{ $isCompleted ? 'text-pass hover:underline' : 'text-brand-deep hover:underline' }}">
                                    <span>{{ $isCompleted ? 'Ulas Materi' : ($percent > 0 ? 'Lanjutkan' : 'Mulai Belajar') }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Reference & Guides Section --}}
        <div class="pt-4">
            <h2 class="text-lg sm:text-xl font-bold text-ink mb-3">
                Panduan Pendukung & Referensi Teknis
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($references as $ref)
                    <a href="{{ route('roadmap.reference', $ref->slug) }}" class="bg-sheet border border-rule rounded-panel p-4 hover:border-brand/40 transition-colors shadow-sm block group">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-2 h-2 rounded-full {{ $ref->kind === 'reference' ? 'bg-brand' : 'bg-ink' }}"></span>
                            <span class="text-xs uppercase font-semibold tracking-wider text-ink-muted">
                                {{ $ref->kind === 'reference' ? 'Peta & Konsep' : 'Panduan Lomba' }}
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-ink group-hover:text-brand-deep transition-colors">
                            {{ $ref->title }}
                        </h3>
                        <p class="text-xs text-ink-muted mt-1 line-clamp-2">
                            Buka panduan lengkap dan tabel ringkasan konsep untuk persiapan modul.
                        </p>
                    </a>
                @endforeach
            </div>
        </div>

    </div>
</div>
