{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Modules List (/modul) --}}
{{-- Primary job of the screen: Allow students to explore published LKS modules, filter by track/level, and see their best scores. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, pass, paper --}}
{{-- Type roles: Schibsted Grotesk for headings and summaries; tabular-nums for levels, deadlines, and scores --}}
{{-- Layout idea: Top filters (track, level), followed by a clean list of module rows/cards with status and score --}}
{{-- What I changed after the "would any app have this?" check: Avoided colourful progress rings and course-store badges; designed as official curriculum module index. --}}

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-ink">
                {{ __('modules.title') }}
            </h1>
            <p class="text-xs text-ink-muted">
                {{ __('modules.list_title') }} — LKS Web Technologies
            </p>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <label for="track-filter" class="text-xs font-medium text-ink-muted">Bidang:</label>
                <select
                    id="track-filter"
                    wire:model.live="track"
                    class="rounded-cell border border-rule px-2.5 py-1 text-xs bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                >
                    <option value="all">Semua Bidang</option>
                    @foreach($tracks as $t)
                        <option value="{{ $t->value }}">{{ $t->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label for="level-filter" class="text-xs font-medium text-ink-muted">Tingkat:</label>
                <select
                    id="level-filter"
                    wire:model.live="level"
                    class="rounded-cell border border-rule px-2.5 py-1 text-xs bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                >
                    <option value="all">Semua Level</option>
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}">Level {{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    {{-- Module rows --}}
    @if($modules->isEmpty())
        <x-panel class="text-center py-8 text-sm text-ink-muted">
            {{ __('modules.empty') }}
        </x-panel>
    @else
        <div class="space-y-3">
            @foreach($modules as $module)
                @php
                    $bestSubmission = $module->submissions->sortByDesc('manual_score')->first();
                    $isOpen = $module->isOpen();
                @endphp
                <x-panel class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1.5 max-w-2xl">
                        <div class="flex flex-wrap items-center gap-2 text-xs text-ink-muted">
                            <span class="font-medium text-brand-deep">{{ $module->track->label() }}</span>
                            <span>•</span>
                            <span class="tabular-nums">Level {{ $module->level }}</span>
                            <span>•</span>
                            <span class="tabular-nums">{{ $module->duration_minutes }} menit</span>
                            @if(! $isOpen)
                                <span>•</span>
                                <span class="text-brand-deep font-medium">Ditutup</span>
                            @endif
                        </div>

                        <h2 class="text-base font-bold text-ink">
                            <a href="{{ route('modules.show', $module->slug) }}" class="hover:text-brand-deep focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                                {{ $module->title }}
                            </a>
                        </h2>

                        <p class="text-xs text-ink-muted line-clamp-2">
                            {{ $module->summary }}
                        </p>
                    </div>

                    <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center border-t sm:border-t-0 border-rule pt-3 sm:pt-0 shrink-0 gap-2">
                        <div class="text-left sm:text-right">
                            @if($bestSubmission && $bestSubmission->manual_score !== null)
                                <span class="text-xs text-ink-muted block">Nilai terbaik</span>
                                <span class="text-base font-bold text-pass tabular-nums">
                                    {{ $bestSubmission->manual_score }} / 100
                                </span>
                            @elseif($bestSubmission)
                                <span class="text-xs font-medium text-ink block">
                                    Diterima
                                </span>
                                <span class="text-xs text-ink-muted">Menunggu penilaian</span>
                            @else
                                <span class="text-xs text-ink-muted block">Tenggat</span>
                                <span class="text-xs font-medium text-ink tabular-nums">
                                    {{ $module->closes_at?->translatedFormat('d M Y') ?? '—' }}
                                </span>
                            @endif
                        </div>

                        <x-button variant="secondary" size="sm" :href="route('modules.show', $module->slug)">
                            Lihat Modul
                        </x-button>
                    </div>
                </x-panel>
            @endforeach
        </div>
    @endif
</div>
