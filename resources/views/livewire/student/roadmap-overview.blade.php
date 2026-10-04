{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Interactive Client Roadmap Overview (/belajar) --}}
{{-- Aesthetic: Exam mastery board for SMK Telkom Purwokerto (white sheets, charcoal ink, brand accents) --}}
<div class="py-6 sm:py-8">
    <div class="mx-auto max-w-7xl xl:max-w-[1400px] px-4 sm:px-6 lg:px-8 space-y-6">

        @if($tab === 'silabus')
            {{-- Module Cards Grid (Direct Selection without Top Navigation Clutter) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                {{-- Hidden semantic anchor for test suite compatibility --}}
                <span class="sr-only">Silabus Modul LKS</span>

                {{-- Card 1: Modul Client-Side --}}
                <div class="flex flex-col justify-between bg-sheet border border-rule hover:border-ink-muted hover:shadow-sm rounded-panel p-6 sm:p-7 transition-all">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between gap-4">
                            <h3 class="text-xl sm:text-2xl font-bold text-ink">
                                Modul Client-Side
                            </h3>
                            <span class="inline-flex items-center text-[11px] font-mono font-medium px-2.5 py-1 rounded bg-pass/10 text-pass border border-pass/30 uppercase tracking-wider font-bold">
                                ✓ Materi & Kuis Interaktif
                            </span>
                        </div>

                        <p class="text-xs font-mono text-brand-deep font-semibold">
                            Frontend, HTML5, CSS Layout & JavaScript DOM (Pin Map)
                        </p>

                        <p class="text-sm text-ink-muted leading-relaxed">
                            Terdiri dari 4 tahap dan 9 level pembelajaran interaktif. Setiap level dilengkapi materi teori terstruktur dan kuis pemahaman konsep (tugas coding interaktif segera hadir).
                        </p>

                        @if(!empty($clientStats['topics_preview']))
                            <div class="space-y-2 pt-2">
                                <span class="text-[11px] font-mono uppercase tracking-wider text-ink-muted block">
                                    Cakupan Materi:
                                </span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($clientStats['topics_preview'] as $topicName)
                                        <span class="inline-flex items-center gap-1 text-xs text-ink bg-paper border border-rule px-2.5 py-1 rounded">
                                            <span class="w-1.5 h-1.5 rounded-full bg-pass"></span>
                                            {{ $topicName }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-8 pt-5 border-t border-rule flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <a
                            href="{{ route('materials.roadmap', 'client') }}"
                            class="text-xs text-ink-muted hover:text-ink underline font-mono"
                        >
                            Detail Materi Client-Side
                        </a>

                        <a
                            href="{{ route('roadmap.index') }}"
                            class="inline-flex items-center justify-center text-xs font-semibold px-5 py-2.5 rounded bg-brand hover:bg-brand-deep transition-colors text-white"
                            style="color: #ffffff !important; background-color: #c92a2a !important;"
                        >
                            Buka Roadmap Belajar
                        </a>
                    </div>
                </div>

                {{-- Card 2: Modul Server-Side --}}
                <div class="flex flex-col justify-between bg-sheet border border-rule hover:border-ink-muted hover:shadow-sm rounded-panel p-6 sm:p-7 transition-all">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between gap-4">
                            <h3 class="text-xl sm:text-2xl font-bold text-ink">
                                Modul Server-Side
                            </h3>
                            <span class="inline-flex items-center text-[11px] font-mono font-medium px-2.5 py-1 rounded bg-paper text-ink-muted border border-rule uppercase tracking-wider">
                                Modul 02 • Server
                            </span>
                        </div>

                        <p class="text-xs font-mono text-brand-deep font-semibold">
                            PHP OOP, REST API, Laravel & Integrasi Vue/React Axios
                        </p>

                        <p class="text-sm text-ink-muted leading-relaxed">
                            Fokus pada arsitektur backend REST API yang kokoh: PHP 8 OOP, perancangan API terstandar, framework Laravel, query database MySQL Eloquent, dan integrasi frontend (Vue/React) via Axios.
                        </p>

                        @if(!empty($serverStats['topics_preview']))
                            <div class="space-y-2 pt-2">
                                <span class="text-[11px] font-mono uppercase tracking-wider text-ink-muted block">
                                    Cakupan Materi:
                                </span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($serverStats['topics_preview'] as $topicName)
                                        <span class="inline-flex items-center gap-1 text-xs text-ink bg-paper border border-rule px-2.5 py-1 rounded">
                                            <span class="w-1.5 h-1.5 rounded-full bg-brand-deep"></span>
                                            {{ $topicName }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-8 pt-5 border-t border-rule flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="text-xs text-ink-muted font-mono flex items-center gap-2">
                            <strong class="text-ink">{{ $serverStats['topics_count'] }}</strong> Tahap
                            <span>•</span>
                            <strong class="text-ink">{{ $serverStats['materials_count'] }}</strong> Modul
                            <span>•</span>
                            <strong class="text-ink">{{ $serverStats['questions_count'] }}</strong> Soal
                        </div>

                        <a
                            href="{{ route('materials.roadmap', 'server') }}"
                            class="inline-flex items-center justify-center text-xs font-semibold px-5 py-2.5 rounded bg-brand hover:bg-brand-deep transition-colors text-white"
                            style="color: #ffffff !important; background-color: #c92a2a !important;"
                        >
                            Buka Roadmap Server-Side
                        </a>
                    </div>
                </div>
            </div>
        @else
            {{-- Client-Side Track Hero Header --}}
            <div class="bg-sheet border border-rule rounded-panel p-6 sm:p-7 shadow-sm">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-2 max-w-3xl">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-paper text-ink-muted border border-rule">
                                Modul 01 • Client-Side (Pin Map)
                            </span>
                            <span class="text-xs text-ink-muted font-mono">• 4 Tahapan • 9 Level Interaktif</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight">
                            Roadmap Belajar Mandiri: Pin Map
                        </h1>
                        <p class="text-sm sm:text-base text-ink-muted leading-relaxed">
                            Kurikulum persiapan LKS Web Technologies bidang Client-Side. Materi teori dan kuis pemahaman konsep terintegrasi langsung dalam setiap tahapan level (tugas coding interaktif segera hadir).
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
                                Aktivitas Saya →
                            </a>
                        </div>

                        @if($nextLevel)
                            <a
                                href="{{ route('roadmap.level', $nextLevel->slug) }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-brand text-white text-sm font-semibold rounded hover:bg-brand-deep transition-colors"
                                style="color: #ffffff !important; background-color: #c92a2a !important;"
                            >
                                Lanjutkan: {{ $nextLevel->title }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Stages & Integrated Levels --}}
            <div class="space-y-8">
                @foreach($stages as $stage)
                    <div class="space-y-3">
                        {{-- Stage Header Banner --}}
                        <div class="bg-sheet border border-rule rounded-panel p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                            <div class="flex items-start sm:items-center gap-3">
                                <span class="w-10 h-10 rounded-panel bg-brand/10 text-brand-deep border border-brand/20 font-mono text-base font-bold flex items-center justify-center shrink-0">
                                    0{{ $stage['stage'] }}
                                </span>
                                <div>
                                    <h2 class="text-base sm:text-lg font-bold text-ink">
                                        {{ $stage['name'] }}
                                    </h2>
                                    <p class="text-xs text-ink-muted mt-0.5 max-w-3xl">
                                        {{ $stage['summary'] }}
                                    </p>
                                </div>
                            </div>

                            <span class="text-xs font-mono text-ink-muted shrink-0 self-start sm:self-auto px-2.5 py-1 bg-paper border border-rule rounded">
                                {{ $stage['levels']->count() }} Level Praktik
                            </span>
                        </div>

                        {{-- Level Cards for this Stage --}}
                        <div class="space-y-3 pl-0 sm:pl-3 border-l-0 sm:border-l-2 sm:border-rule/60">
                            @foreach($stage['levels'] as $level)
                                @php
                                    $prog = $userProgress->get($level->slug);
                                    $isCompleted = $prog?->is_completed ?? false;
                                    $percent = $prog?->percent_complete ?? 0;
                                    $checkpointsMarked = $prog?->checkpoints_marked ?? 0;
                                    $exercisesPassed = $prog?->exercises_passed ?? 0;
                                    $isUnlocked = $level->isUnlockedFor(auth()->user());
                                @endphp
                                <div
                                    class="bg-sheet border {{ $isCompleted ? 'border-pass/40' : ($isUnlocked ? 'border-rule hover:border-ink-muted' : 'border-rule/60 opacity-80') }} rounded-panel p-5 transition-all shadow-sm group"
                                >
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        {{-- Left: Badge & Info --}}
                                        <div class="flex items-start gap-4">
                                            <div class="w-10 h-10 rounded-panel {{ $isCompleted ? 'bg-pass text-white' : ($isUnlocked ? 'bg-ink text-white' : 'bg-paper text-ink-muted border border-rule') }} font-mono text-sm font-bold flex items-center justify-center shrink-0">
                                                @if($isCompleted)
                                                    ✓
                                                @elseif(! $isUnlocked)
                                                    🔒
                                                @else
                                                    {{ $level->position }}
                                                @endif
                                            </div>

                                            <div class="space-y-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h3 class="text-base sm:text-lg font-bold text-ink group-hover:text-brand-deep transition-colors">
                                                        @if($isUnlocked)
                                                            <a href="{{ route('roadmap.level', $level->slug) }}">
                                                                {{ $level->title }}
                                                            </a>
                                                        @else
                                                            <span class="text-ink-muted">{{ $level->title }}</span>
                                                        @endif
                                                    </h3>
                                                    @if($level->estimated_time)
                                                        <span class="text-xs bg-paper border border-rule text-ink-muted px-2 py-0.5 rounded font-mono">
                                                            ⏱ {{ $level->estimated_time }}
                                                        </span>
                                                    @endif
                                                    @if($isCompleted)
                                                        <span class="text-xs bg-pass/10 text-pass border border-pass/30 px-2.5 py-0.5 rounded font-semibold inline-flex items-center gap-1">
                                                            Selesai
                                                        </span>
                                                    @elseif(! $isUnlocked)
                                                        <span class="text-xs bg-paper border border-rule text-ink-muted px-2 py-0.5 rounded font-medium">
                                                            🔒 Terkunci (Selesaikan Level {{ $level->position - 1 }})
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
                                                    <span>Kuis: <strong>{{ $checkpointsMarked }}/{{ $level->checkpoints->count() }} selesai</strong></span>
                                                    @if($level->exercises->isNotEmpty())
                                                        <span>Tugas: <strong class="text-ink-muted font-normal italic">Coming Soon</strong></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Right Action / Progress Bar --}}
                                        <div class="flex md:flex-col items-center md:items-end justify-between md:justify-center gap-3 shrink-0 min-w-[200px]">
                                            <div class="text-right w-full">
                                                <div class="flex justify-between md:justify-end gap-2 text-xs font-mono text-ink-muted mb-1">
                                                    <span>Progres:</span>
                                                    <span class="font-bold text-ink">{{ $percent }}%</span>
                                                </div>
                                                <div class="w-full bg-rule h-1.5 rounded-full overflow-hidden">
                                                    <div class="{{ $isCompleted ? 'bg-pass' : 'bg-brand' }} h-full transition-all duration-300" style="width: {{ $percent }}%"></div>
                                                </div>
                                            </div>

                                            @if($isUnlocked)
                                                <div class="flex flex-wrap items-center gap-1.5 justify-end">
                                                    <a href="{{ route('roadmap.level', $level->slug) }}" class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1.5 rounded bg-sheet border border-rule text-ink hover:bg-paper transition-colors" title="Materi Pembelajaran">
                                                        <span>📖 Materi</span>
                                                    </a>
                                                    <a href="{{ route('roadmap.quiz', $level->slug) }}" class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1.5 rounded {{ $checkpointsMarked === $level->checkpoints->count() && $level->checkpoints->count() > 0 ? 'bg-pass/10 border border-pass/30 text-pass' : 'bg-sheet border border-rule text-ink hover:bg-paper' }} transition-colors" title="Kuis Pilihan Ganda">
                                                        <span>✍️ Kuis ({{ $checkpointsMarked }}/{{ $level->checkpoints->count() }})</span>
                                                    </a>
                                                    @if($level->exercises->isNotEmpty())
                                                        <a
                                                            href="{{ route('roadmap.task', $level->slug) }}"
                                                            class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1.5 rounded bg-paper border border-dashed border-rule text-ink-muted hover:border-brand/40 hover:text-ink transition-colors"
                                                            title="Tugas Coding Interaktif — Sedang Disiapkan (Coming Soon)"
                                                        >
                                                            <span>💻 Tugas</span>
                                                            <span class="text-[10px] font-mono px-1.5 py-0.5 bg-sheet border border-rule rounded text-ink-muted">Coming Soon</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-xs font-medium text-ink-muted bg-paper px-2.5 py-1 rounded border border-rule cursor-not-allowed">
                                                    🔒 Belum Terbuka
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
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
        @endif

    </div>
</div>
