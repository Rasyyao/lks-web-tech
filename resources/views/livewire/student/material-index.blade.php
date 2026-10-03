{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Materi & Roadmap LKS Web Technologies (/materi) --}}
{{-- Primary job of the screen: Provide categorized learning paths across two primary competition tracks (Client-side & Server-side) with deep curriculum roadmap, materials, and practice sets. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, paper, tint, pass --}}
{{-- Type roles: Schibsted Grotesk for topic names and descriptions; JetBrains Mono for step counters and metrics --}}
{{-- Layout idea: Interactive track selector cards at the top (Client vs Server), followed by an interconnected step-by-step roadmap view --}}

<div class="space-y-8">
    {{-- Header --}}
    <div class="space-y-1">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded-cell text-xs font-medium font-mono bg-tint text-brand-deep border border-brand-deep/20 uppercase tracking-wider">
                Silabus Kompetisi
            </span>
            <span class="text-xs text-ink-muted">• LKS SMK Telkom Purwokerto</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight">
            Roadmap Pembelajaran & Silabus LKS
        </h1>
        <p class="text-sm text-ink-muted max-w-3xl">
            Kurikulum resmi latihan LKS Web Technologies dikelompokkan ke dalam dua bidang modul utama. Pilih modul untuk melihat tahapan belajar, materi teknis, dan latihan soal.
        </p>
    </div>

    {{-- Module Track Selector (Client vs Server) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Card Modul Client-Side --}}
        <button
            type="button"
            wire:click="setTrack('client')"
            class="text-left transition-all rounded-panel p-5 sm:p-6 border {{ $activeTrack === 'client' ? 'bg-sheet border-brand shadow-sm ring-2 ring-brand/20' : 'bg-sheet/60 hover:bg-sheet border-rule hover:border-ink-muted' }} focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
        >
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-panel flex items-center justify-center {{ $activeTrack === 'client' ? 'bg-brand text-sheet' : 'bg-paper text-ink-muted border border-rule' }}">
                        {{-- Browser / Client Icon --}}
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-ink">
                                Modul Client-Side
                            </h2>
                            @if($activeTrack === 'client')
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-brand-deep bg-tint px-2 py-0.5 rounded-full border border-brand-deep/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-deep"></span>
                                    Aktif
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-ink-muted">
                            Frontend, HTML5, CSS Layout & JavaScript DOM
                        </p>
                    </div>
                </div>
            </div>

            <p class="mt-3 text-xs text-ink leading-relaxed">
                Fokus pada pembangunan antarmuka web interaktif tanpa framework berat: HTML5 semantik, CSS Flexbox/Grid responsif, manipulasi DOM vanilla, event handling, dan Web Storage.
            </p>

            <div class="mt-4 pt-3 border-t border-rule flex flex-wrap items-center justify-between gap-2 text-xs text-ink-muted">
                <span class="font-mono text-ink font-medium">
                    {{ $clientStats['topics_count'] }} Tahap Roadmap
                </span>
                <span>
                    {{ $clientStats['materials_count'] }} Modul • {{ $clientStats['questions_count'] }} Soal Latihan
                </span>
            </div>
        </button>

        {{-- Card Modul Server-Side --}}
        <button
            type="button"
            wire:click="setTrack('server')"
            class="text-left transition-all rounded-panel p-5 sm:p-6 border {{ $activeTrack === 'server' ? 'bg-sheet border-brand shadow-sm ring-2 ring-brand/20' : 'bg-sheet/60 hover:bg-sheet border-rule hover:border-ink-muted' }} focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
        >
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-panel flex items-center justify-center {{ $activeTrack === 'server' ? 'bg-brand text-sheet' : 'bg-paper text-ink-muted border border-rule' }}">
                        {{-- Server / Database Icon --}}
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 0 6h13.5a3 3 0 1 0 0-6m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3m-19.5 0a4.5 4.5 0 0 1 .9-2.7L5.75 5.1a4.5 4.5 0 0 1 3.6-1.85h5.3a4.5 4.5 0 0 1 3.6 1.85l1.6 3.15a4.5 4.5 0 0 1 .9 2.7" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-ink">
                                Modul Server-Side
                            </h2>
                            @if($activeTrack === 'server')
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-brand-deep bg-tint px-2 py-0.5 rounded-full border border-brand-deep/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-deep"></span>
                                    Aktif
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-ink-muted">
                            PHP OOP, REST API, Laravel & Integrasi Vue/React Axios
                        </p>
                    </div>
                </div>
            </div>

            <p class="mt-3 text-xs text-ink leading-relaxed">
                Fokus pada arsitektur backend REST API yang kokoh: PHP 8 OOP, perancangan API terstandar, framework Laravel, query database MySQL Eloquent, dan integrasi frontend (Vue/React) via Axios.
            </p>

            <div class="mt-4 pt-3 border-t border-rule flex flex-wrap items-center justify-between gap-2 text-xs text-ink-muted">
                <span class="font-mono text-ink font-medium">
                    {{ $serverStats['topics_count'] }} Tahap Roadmap
                </span>
                <span>
                    {{ $serverStats['materials_count'] }} Modul • {{ $serverStats['questions_count'] }} Soal Latihan
                </span>
            </div>
        </button>
    </div>

    {{-- Roadmap Content Section --}}
    <div class="space-y-4">
        {{-- Section Heading & Track Switcher Tabs --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-rule pb-3">
            <div>
                <h2 class="text-lg font-bold text-ink">
                    Roadmap: {{ $activeTrack === 'server' ? 'Modul Server-Side' : 'Modul Client-Side' }}
                </h2>
                <p class="text-xs text-ink-muted">
                    Urutan tahapan materi dan latihan terstruktur untuk menguasai kompetensi {{ $activeTrack === 'server' ? 'backend dan API' : 'antarmuka frontend' }}.
                </p>
            </div>

            <div class="inline-flex p-1 bg-paper border border-rule rounded-cell self-start sm:self-auto">
                <button
                    type="button"
                    wire:click="setTrack('client')"
                    class="px-3 py-1 text-xs font-medium rounded-cell transition-colors {{ $activeTrack === 'client' ? 'bg-sheet text-ink shadow-sm' : 'text-ink-muted hover:text-ink' }}"
                >
                    Client-Side ({{ $clientStats['topics_count'] }})
                </button>
                <button
                    type="button"
                    wire:click="setTrack('server')"
                    class="px-3 py-1 text-xs font-medium rounded-cell transition-colors {{ $activeTrack === 'server' ? 'bg-sheet text-ink shadow-sm' : 'text-ink-muted hover:text-ink' }}"
                >
                    Server-Side ({{ $serverStats['topics_count'] }})
                </button>
            </div>
        </div>

        @if($activeTopics->isEmpty())
            <x-panel class="text-center py-10 text-sm text-ink-muted">
                Belum ada tahapan roadmap untuk modul ini.
            </x-panel>
        @else
            {{-- Roadmap Steps List with Timeline --}}
            <div class="space-y-4 relative">
                @foreach($activeTopics as $index => $topic)
                    <x-panel class="relative overflow-hidden transition-all hover:border-ink-muted group">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                            <div class="flex items-start gap-4">
                                {{-- Step Number Pill --}}
                                <div class="shrink-0">
                                    <div class="w-12 h-12 rounded-panel border border-rule bg-paper flex flex-col items-center justify-center text-center">
                                        <span class="text-[9px] font-mono uppercase tracking-wider text-ink-muted">Tahap</span>
                                        <span class="font-mono text-base font-bold text-brand-deep tabular-nums leading-none">
                                            {{ sprintf('%02d', $index + 1) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Topic Details --}}
                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center text-[10px] font-mono px-2 py-0.5 rounded-cell font-medium {{ ($topic->track?->value ?? $topic->track) === 'server' ? 'bg-brand/10 text-brand-deep' : 'bg-tint text-brand-deep' }}">
                                            {{ ($topic->track?->value ?? $topic->track) === 'server' ? 'Server-Side' : 'Client-Side' }}
                                        </span>
                                        <h3 class="text-lg font-bold text-ink group-hover:text-brand-deep transition-colors">
                                            <a href="{{ route('materials.show', $topic->slug) }}" class="focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                                                {{ $topic->name }}
                                            </a>
                                        </h3>
                                    </div>

                                    @if($topic->description)
                                        <p class="text-xs text-ink-muted max-w-2xl leading-relaxed">
                                            {{ $topic->description }}
                                        </p>
                                    @endif

                                    {{-- Sub-materials snippet --}}
                                    @if($topic->materials->isNotEmpty())
                                        <div class="pt-1 flex flex-wrap items-center gap-1.5">
                                            @foreach($topic->materials as $mat)
                                                <span class="inline-flex items-center gap-1 text-[11px] text-ink-muted bg-paper border border-rule px-2 py-0.5 rounded-cell">
                                                    <svg class="w-3 h-3 text-brand-deep" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    {{ $mat->title }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="text-xs text-ink-muted pt-1 flex items-center gap-3">
                                        <span>
                                            <strong class="font-mono text-ink">{{ $topic->materials->count() }}</strong> modul materi tertulis
                                        </span>
                                        <span>•</span>
                                        <span>
                                            <strong class="font-mono text-ink">{{ $topic->questions_count }}</strong> soal latihan terdaftar
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 shrink-0 md:self-center pl-16 md:pl-0">
                                <x-button variant="secondary" size="sm" :href="route('materials.show', $topic->slug)">
                                    Baca Materi
                                </x-button>
                                <x-button size="sm" :href="route('practice.start', ['topic' => $topic->id])">
                                    Latih Soal
                                </x-button>
                            </div>
                        </div>
                    </x-panel>
                @endforeach
            </div>
        @endif
    </div>
</div>
