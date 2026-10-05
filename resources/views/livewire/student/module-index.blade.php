{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Modules List (/modul) --}}
{{-- Primary job of the screen: Allow students to explore published LKS modules, filter by track/level, see category tags, upload date and deadline timestamps, and track submission states. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, pass, paper --}}

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-rule pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight">
                {{ __('modules.title') }}
            </h1>
            <p class="text-xs sm:text-sm text-ink-muted mt-0.5">
                {{ __('modules.list_title') }} &bull; LKS Web Technologies
            </p>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <label for="track-filter" class="text-xs font-semibold text-ink-muted">Bidang:</label>
                <select
                    id="track-filter"
                    wire:model.live="track"
                    class="rounded-cell border border-rule px-3 py-1.5 text-xs bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-brand-deep"
                >
                    <option value="all">Semua Bidang</option>
                    @foreach($tracks as $t)
                        <option value="{{ $t->value }}">{{ $t->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label for="level-filter" class="text-xs font-semibold text-ink-muted">Tingkat:</label>
                <select
                    id="level-filter"
                    wire:model.live="level"
                    class="rounded-cell border border-rule px-3 py-1.5 text-xs bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-brand-deep"
                >
                    <option value="all">Semua Level</option>
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}">Level {{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    {{-- Module cards --}}
    @if($modules->isEmpty())
        <x-panel class="text-center py-12 text-sm text-ink-muted">
            {{ __('modules.empty') }}
        </x-panel>
    @else
        <div class="space-y-4">
            @foreach($modules as $module)
                @php
                    $bestSubmission = $module->submissions->sortByDesc('manual_score')->first();
                    $isSubmitted = $module->submissions->isNotEmpty();
                    $isOpen = $module->isOpen();
                    $isServer = $module->track === \App\Enums\ModuleTrack::Server;
                @endphp
                <div class="rounded-panel p-5 transition-all duration-200 {{ $isSubmitted ? 'bg-sheet border-2 border-pass/40 shadow-sm ring-1 ring-pass/10' : 'bg-sheet border border-rule shadow-sm hover:border-rule/90' }}">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                        {{-- Left Column: Category, Level, Title, Summary, Timestamps --}}
                        <div class="space-y-3 max-w-3xl">
                            {{-- Top Metadata Badges --}}
                            <div class="flex flex-wrap items-center gap-2">
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

                                <span class="text-[11px] font-mono font-medium text-ink-muted bg-paper px-2 py-0.5 rounded-cell border border-rule">
                                    Tingkat {{ $module->level }}
                                </span>

                                <span class="text-[11px] text-ink-muted tabular-nums">
                                    Durasi {{ $module->duration_minutes }} Menit
                                </span>

                                @if(! $isOpen)
                                    <span class="px-2 py-0.5 rounded-cell text-[11px] font-bold bg-tint text-brand-deep border border-brand-deep/20">
                                        Ditutup
                                    </span>
                                @endif

                                {{-- Submission Status Badge --}}
                                @if($isSubmitted)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-cell bg-pass/10 text-pass text-[11px] font-bold border border-pass/30 ml-auto sm:ml-0">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Sudah Dikumpulkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-cell bg-paper text-ink-muted text-[11px] font-medium border border-rule ml-auto sm:ml-0">
                                        Belum Dikumpulkan
                                    </span>
                                @endif
                            </div>

                            {{-- Title & Summary --}}
                            <div>
                                <h2 class="text-lg font-bold text-ink hover:text-brand-deep transition-colors">
                                    <a href="{{ route('modules.show', $module->slug) }}" class="focus-visible:outline-2 focus-visible:outline-brand-deep">
                                        {{ $module->title }}
                                    </a>
                                </h2>
                                <p class="text-xs sm:text-sm text-ink-muted line-clamp-2 mt-1 leading-relaxed">
                                    {{ $module->summary }}
                                </p>
                            </div>

                            {{-- Timestamps: Upload day/date and deadline day/date/time --}}
                            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 pt-1 text-xs">
                                <div class="flex items-center gap-1.5 text-ink-muted">
                                    <svg class="w-3.5 h-3.5 text-ink-muted shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <span>Diupload:</span>
                                    <time class="font-medium text-ink tabular-nums">
                                        {{ $module->opens_at?->translatedFormat('l, d M Y') ?? '—' }}
                                    </time>
                                </div>

                                <div class="flex items-center gap-1.5 text-ink-muted">
                                    <svg class="w-3.5 h-3.5 text-brand-deep shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Tenggat:</span>
                                    <time class="font-bold text-brand-deep tabular-nums">
                                        {{ $module->closes_at?->translatedFormat('l, d M Y, H:i') ?? '—' }}
                                    </time>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Status/Score & Action Button --}}
                        <div class="flex sm:flex-col items-center lg:items-end justify-between lg:justify-center border-t lg:border-t-0 border-rule pt-4 lg:pt-0 shrink-0 gap-3">
                            <div class="text-left lg:text-right">
                                @if($bestSubmission && $bestSubmission->manual_score !== null)
                                    <span class="text-[10px] text-ink-muted uppercase font-bold tracking-wider block">Nilai Terbaik</span>
                                    <span class="text-lg font-extrabold text-pass tabular-nums">
                                        {{ $bestSubmission->manual_score }} / 100
                                    </span>
                                @elseif($bestSubmission)
                                    <span class="text-[10px] text-ink-muted uppercase font-bold tracking-wider block">Status Tugas</span>
                                    <span class="text-xs font-semibold text-ink block">
                                        Diterima
                                    </span>
                                    <span class="text-[11px] text-ink-muted">Menunggu review</span>
                                @else
                                    <span class="text-[10px] text-ink-muted uppercase font-bold tracking-wider block">Status Pengumpulan</span>
                                    <span class="text-xs font-medium text-ink-muted block">
                                        Tersedia untuk dikerjakan
                                    </span>
                                @endif
                            </div>

                            <div>
                                @if($isSubmitted)
                                    <x-button variant="secondary" size="sm" :href="route('modules.show', $module->slug)">
                                        Lihat Modul & Nilai
                                    </x-button>
                                @else
                                    <a
                                        href="{{ route('modules.show', $module->slug) }}"
                                        class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold rounded-cell transition-colors"
                                        style="color: #ffffff !important; background-color: #c92a2a !important;"
                                    >
                                        Kerjakan Modul &rarr;
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
