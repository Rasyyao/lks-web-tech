{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Dedicated Module Roadmap Page (/materi/roadmap/{track}) --}}
{{-- Primary job of the screen: Full chronological roadmap of topics for the chosen module track (Client or Server). --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, paper, tint --}}
{{-- Type roles: Schibsted Grotesk for topic names and descriptions; JetBrains Mono for step counters and metrics --}}
{{-- Card padding: Generous p-6 sm:p-7 with clean borders, structured footer bar, and responsive layout --}}

<div class="space-y-8">
    {{-- Navigation Breadcrumb & Fast Switcher --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-rule pb-4">
        <a href="{{ route('materials.index') }}" class="text-xs text-ink-muted hover:text-brand-deep inline-flex items-center gap-1.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Pilihan Modul
        </a>

        <div class="flex items-center gap-2">
            <span class="text-xs text-ink-muted hidden sm:inline">Pindah Jalur:</span>
            @if($track === 'client')
                <a
                    href="{{ route('materials.roadmap', 'server') }}"
                    class="text-xs text-ink-muted hover:text-ink font-medium bg-sheet hover:bg-paper border border-rule px-3 py-1.5 rounded-cell transition-colors inline-flex items-center gap-1.5"
                >
                    Lihat Modul Server-Side →
                </a>
            @else
                <a
                    href="{{ route('materials.roadmap', 'client') }}"
                    class="text-xs text-ink-muted hover:text-ink font-medium bg-sheet hover:bg-paper border border-rule px-3 py-1.5 rounded-cell transition-colors inline-flex items-center gap-1.5"
                >
                    ← Lihat Modul Client-Side
                </a>
            @endif
        </div>
    </div>

    {{-- Module Track Header Hero Card --}}
    <div class="bg-sheet border border-rule rounded-panel p-6 sm:p-8 space-y-4">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div class="space-y-3 max-w-3xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center text-xs font-mono font-semibold px-2.5 py-0.5 rounded-cell {{ $track === 'server' ? 'bg-brand/10 text-brand-deep border border-brand/20' : 'bg-tint text-brand-deep border border-brand-deep/20' }}">
                        {{ $track === 'server' ? 'Modul Server-Side • Backend & REST API' : 'Modul Client-Side • Frontend & UI' }}
                    </span>
                    <span class="text-xs text-ink-muted font-mono">• Silabus Resmi LKS Web Technologies</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight">
                    Roadmap Pembelajaran: {{ $trackTitle }}
                </h1>

                <p class="text-sm text-ink-muted leading-relaxed">
                    @if($track === 'client')
                        Fokus pada pembangunan antarmuka web interaktif berstandar kompetisi tanpa bantuan framework frontend berat. Kurikulum mencakup fondasi HTML5 semantik, tata letak modern CSS Flexbox & CSS Grid, logika modern JavaScript ES6+, hingga manipulasi mendalam elemen DOM dan penanganan event.
                    @else
                        Fokus pada arsitektur backend dan pembuatan REST API yang tangguh, aman, dan efisien. Kurikulum mencakup pemrograman berorientasi objek PHP 8 OOP, perancangan database relasional MySQL terstandar, framework Laravel 11 dengan Eloquent ORM, hingga integrasi frontend modern via Axios.
                    @endif
                </p>
            </div>

            {{-- Summary Metrics Box --}}
            <div class="shrink-0 bg-paper border border-rule rounded-panel p-4 sm:p-5 flex flex-row md:flex-col justify-between md:justify-center gap-4 text-center min-w-[200px]">
                <div>
                    <span class="block text-2xl font-bold font-mono text-brand-deep leading-none">
                        {{ $topics->count() }}
                    </span>
                    <span class="text-[11px] text-ink-muted uppercase font-mono tracking-wider">Tahap Roadmap</span>
                </div>
                <div class="w-px md:w-full h-auto md:h-px bg-rule"></div>
                <div>
                    <span class="block text-2xl font-bold font-mono text-ink leading-none">
                        {{ $totalMaterials }}
                    </span>
                    <span class="text-[11px] text-ink-muted uppercase font-mono tracking-wider">Modul Materi</span>
                </div>
                <div class="w-px md:w-full h-auto md:h-px bg-rule"></div>
                <div>
                    <span class="block text-2xl font-bold font-mono text-ink leading-none">
                        {{ $totalQuestions }}
                    </span>
                    <span class="text-[11px] text-ink-muted uppercase font-mono tracking-wider">Soal Latihan</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Roadmap Steps List --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between border-b border-rule pb-3">
            <div>
                <h2 class="text-lg font-bold text-ink">
                    Tahapan Belajar & Silabus Terstruktur
                </h2>
                <p class="text-xs text-ink-muted">
                    Selesaikan materi dan latih soal secara bertahap dari tahap pertama hingga tahap akhir.
                </p>
            </div>
            <span class="text-xs font-mono text-ink-muted">
                {{ $topics->count() }} Tahapan
            </span>
        </div>

        @if($topics->isEmpty())
            <div class="bg-sheet border border-rule rounded-panel p-8 text-center text-sm text-ink-muted">
                Belum ada topik roadmap yang didaftarkan untuk modul ini.
            </div>
        @else
            <div class="space-y-5">
                @foreach($topics as $index => $topic)
                    <div class="bg-sheet border border-rule rounded-panel p-6 sm:p-7 hover:border-ink-muted transition-all space-y-4 group">
                        {{-- Top Header: Step Indicator & Topic Title --}}
                        <div class="flex flex-col sm:flex-row sm:items-start gap-4 sm:gap-5">
                            {{-- Step Box --}}
                            <div class="w-14 h-14 rounded-panel bg-paper border border-rule flex flex-col items-center justify-center shrink-0">
                                <span class="text-[9px] font-mono uppercase tracking-wider text-ink-muted leading-tight">Tahap</span>
                                <span class="font-mono text-lg font-bold text-brand-deep tabular-nums leading-none">
                                    {{ sprintf('%02d', $index + 1) }}
                                </span>
                            </div>

                            {{-- Topic Info --}}
                            <div class="space-y-2 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center text-[10px] font-mono px-2 py-0.5 rounded-cell font-semibold {{ ($topic->track?->value ?? $topic->track) === 'server' ? 'bg-brand/10 text-brand-deep' : 'bg-tint text-brand-deep' }}">
                                        {{ ($topic->track?->value ?? $topic->track) === 'server' ? 'Server-Side' : 'Client-Side' }}
                                    </span>
                                    <h3 class="text-lg sm:text-xl font-bold text-ink group-hover:text-brand-deep transition-colors">
                                        <a href="{{ route('materials.show', $topic->slug) }}" class="focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                                            {{ $topic->name }}
                                        </a>
                                    </h3>
                                </div>

                                @if($topic->description)
                                    <p class="text-sm text-ink-muted leading-relaxed">
                                        {{ $topic->description }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Sub-materials Badge Snippets --}}
                        @if($topic->materials->isNotEmpty())
                            <div class="pl-0 sm:pl-[4.75rem] flex flex-wrap items-center gap-2 pt-1">
                                @foreach($topic->materials as $mat)
                                    <span class="inline-flex items-center gap-1.5 text-xs text-ink bg-paper border border-rule px-2.5 py-1 rounded-cell">
                                        <svg class="w-3.5 h-3.5 text-brand-deep" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        {{ $mat->title }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Card Footer: Metadata on left, Actions on right --}}
                        <div class="border-t border-rule pt-4 mt-5 pl-0 sm:pl-[4.75rem] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="text-xs text-ink-muted font-mono flex items-center gap-3">
                                <span>
                                    <strong class="text-ink">{{ $topic->materials->count() }}</strong> materi tertulis
                                </span>
                                <span>•</span>
                                <span>
                                    <strong class="text-ink">{{ $topic->questions_count }}</strong> soal latihan
                                </span>
                            </div>

                            <div class="flex items-center gap-2.5 self-start sm:self-auto">
                                <x-button variant="secondary" size="sm" :href="route('materials.show', $topic->slug)">
                                    Baca Materi
                                </x-button>
                                <x-button size="sm" :href="route('practice.start', ['topic' => $topic->id])">
                                    Latih Soal
                                </x-button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
