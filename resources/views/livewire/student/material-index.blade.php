{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Silabus & Pilihan Modul LKS Web Technologies (/materi) --}}
{{-- Primary job of the screen: Clear curriculum gateway between Client-side and Server-side modules, directing student to dedicated roadmap pages. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, paper, tint --}}
{{-- Type roles: Schibsted Grotesk for headings and summaries; JetBrains Mono for counters and module badges --}}
{{-- Card padding: Generous p-6 sm:p-8 with consistent spacing, card hover states, and clear CTA links --}}

<div class="space-y-8">
    {{-- Header --}}
    <div class="space-y-2">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded-cell text-xs font-medium font-mono bg-tint text-brand-deep border border-brand-deep/20 uppercase tracking-wider">
                Silabus Kompetisi
            </span>
            <span class="text-xs text-ink-muted">• LKS SMK Telkom Purwokerto</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight">
            Modul & Roadmap LKS Web Technologies
        </h1>
        <p class="text-sm text-ink-muted max-w-3xl leading-relaxed">
            Kurikulum resmi latihan LKS Web Technologies dikelompokkan ke dalam dua bidang modul utama. Pilih salah satu modul di bawah ini untuk melihat tahapan belajar, materi teknis, dan latihan soal.
        </p>
    </div>

    {{-- Module Cards Grid (Spacious Padding & Clean Hierarchy) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
        {{-- Card 1: Modul Client-Side --}}
        <a
            href="{{ route('materials.roadmap', 'client') }}"
            class="group flex flex-col justify-between bg-sheet border border-rule hover:border-ink-muted hover:shadow-sm rounded-panel p-6 sm:p-8 transition-all relative overflow-hidden focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
        >
            <div class="space-y-5">
                {{-- Header with Icon & Badge --}}
                <div class="flex items-start justify-between gap-4">
                    <div class="w-12 h-12 rounded-panel bg-tint text-brand-deep border border-brand-deep/20 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        {{-- Browser / Client Icon --}}
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                        </svg>
                    </div>

                    <span class="inline-flex items-center text-[11px] font-mono font-medium px-2.5 py-1 rounded-cell bg-paper text-ink-muted border border-rule uppercase tracking-wider">
                        Modul 01 • Client
                    </span>
                </div>

                {{-- Title & Subtitle --}}
                <div class="space-y-1.5">
                    <h2 class="text-xl sm:text-2xl font-bold text-ink group-hover:text-brand-deep transition-colors">
                        Modul Client-Side
                    </h2>
                    <p class="text-xs font-mono text-brand-deep font-medium">
                        Frontend, HTML5, CSS Layout & JavaScript DOM
                    </p>
                </div>

                {{-- Description --}}
                <p class="text-sm text-ink-muted leading-relaxed">
                    Fokus pada pembangunan antarmuka web interaktif tanpa framework berat: HTML5 semantik, CSS Flexbox & CSS Grid responsif, manipulasi DOM vanilla, event handling, dan Web Storage.
                </p>

                {{-- Key Topics Preview --}}
                @if(!empty($clientStats['topics_preview']))
                    <div class="space-y-2 pt-2">
                        <span class="text-[11px] font-mono uppercase tracking-wider text-ink-muted block">
                            Cakupan Tahapan Materi:
                        </span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($clientStats['topics_preview'] as $topicName)
                                <span class="inline-flex items-center gap-1 text-xs text-ink bg-paper border border-rule px-2.5 py-1 rounded-cell">
                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-deep"></span>
                                    {{ $topicName }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Footer Divider & Action --}}
            <div class="mt-8 pt-5 border-t border-rule flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="text-xs text-ink-muted font-mono flex items-center gap-2">
                    <strong class="text-ink">{{ $clientStats['topics_count'] }}</strong> Tahap
                    <span>•</span>
                    <strong class="text-ink">{{ $clientStats['materials_count'] }}</strong> Modul
                    <span>•</span>
                    <strong class="text-ink">{{ $clientStats['questions_count'] }}</strong> Soal
                </div>

                <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-deep group-hover:translate-x-1 transition-transform">
                    <span>Buka Roadmap Client-Side</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </div>
            </div>
        </a>

        {{-- Card 2: Modul Server-Side --}}
        <a
            href="{{ route('materials.roadmap', 'server') }}"
            class="group flex flex-col justify-between bg-sheet border border-rule hover:border-ink-muted hover:shadow-sm rounded-panel p-6 sm:p-8 transition-all relative overflow-hidden focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
        >
            <div class="space-y-5">
                {{-- Header with Icon & Badge --}}
                <div class="flex items-start justify-between gap-4">
                    <div class="w-12 h-12 rounded-panel bg-brand/10 text-brand-deep border border-brand/20 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        {{-- Server / Database Icon --}}
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 0 6h13.5a3 3 0 1 0 0-6m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3m-19.5 0a4.5 4.5 0 0 1 .9-2.7L5.75 5.1a4.5 4.5 0 0 1 3.6-1.85h5.3a4.5 4.5 0 0 1 3.6 1.85l1.6 3.15a4.5 4.5 0 0 1 .9 2.7" />
                        </svg>
                    </div>

                    <span class="inline-flex items-center text-[11px] font-mono font-medium px-2.5 py-1 rounded-cell bg-paper text-ink-muted border border-rule uppercase tracking-wider">
                        Modul 02 • Server
                    </span>
                </div>

                {{-- Title & Subtitle --}}
                <div class="space-y-1.5">
                    <h2 class="text-xl sm:text-2xl font-bold text-ink group-hover:text-brand-deep transition-colors">
                        Modul Server-Side
                    </h2>
                    <p class="text-xs font-mono text-brand-deep font-medium">
                        PHP OOP, REST API, Laravel & Integrasi Vue/React Axios
                    </p>
                </div>

                {{-- Description --}}
                <p class="text-sm text-ink-muted leading-relaxed">
                    Fokus pada arsitektur backend REST API yang kokoh: PHP 8 OOP, perancangan API terstandar, framework Laravel, query database MySQL Eloquent, dan integrasi frontend (Vue/React) via Axios.
                </p>

                {{-- Key Topics Preview --}}
                @if(!empty($serverStats['topics_preview']))
                    <div class="space-y-2 pt-2">
                        <span class="text-[11px] font-mono uppercase tracking-wider text-ink-muted block">
                            Cakupan Tahapan Materi:
                        </span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($serverStats['topics_preview'] as $topicName)
                                <span class="inline-flex items-center gap-1 text-xs text-ink bg-paper border border-rule px-2.5 py-1 rounded-cell">
                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-deep"></span>
                                    {{ $topicName }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Footer Divider & Action --}}
            <div class="mt-8 pt-5 border-t border-rule flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="text-xs text-ink-muted font-mono flex items-center gap-2">
                    <strong class="text-ink">{{ $serverStats['topics_count'] }}</strong> Tahap
                    <span>•</span>
                    <strong class="text-ink">{{ $serverStats['materials_count'] }}</strong> Modul
                    <span>•</span>
                    <strong class="text-ink">{{ $serverStats['questions_count'] }}</strong> Soal
                </div>

                <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-deep group-hover:translate-x-1 transition-transform">
                    <span>Buka Roadmap Server-Side</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </div>
            </div>
        </a>
    </div>
</div>
