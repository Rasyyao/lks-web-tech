{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Dedicated Single-Page Coming Soon for Tugas Coding (/belajar/{slug}/tugas) --}}
{{-- Aesthetic: Exam syllabus & mastery board for SMK Telkom Purwokerto (white sheets, charcoal ink, brand accents) --}}

<div class="py-8 sm:py-12">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

        {{-- Single Unified Coming Soon Container --}}
        <div class="bg-sheet border border-rule rounded-panel shadow-sm overflow-hidden relative">

            {{-- Top Accent Bar --}}
            <div class="h-1.5 w-full bg-brand"></div>

            {{-- Card Body --}}
            <div class="p-6 sm:p-10 md:p-12 space-y-8">

                {{-- Breadcrumb & Back Action --}}
                <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-ink-muted border-b border-rule pb-4">
                    <nav class="flex items-center gap-1.5" aria-label="Breadcrumb">
                        <a href="{{ route('roadmap.index') }}" class="hover:text-ink transition-colors">Roadmap Belajar</a>
                        <span>/</span>
                        <a href="{{ route('roadmap.level', $page->slug) }}" class="hover:text-ink transition-colors">Level {{ $page->position }}</a>
                        <span>/</span>
                        <span class="text-ink font-semibold">Tugas Coding</span>
                    </nav>

                    <a href="{{ route('roadmap.index') }}" class="inline-flex items-center gap-1 text-xs text-brand-deep hover:underline font-medium">
                        ← Kembali ke Roadmap Belajar
                    </a>
                </div>

                {{-- Hero Section --}}
                <div class="text-center max-w-2xl mx-auto space-y-4 pt-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-paper border border-rule text-brand-deep shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-brand animate-pulse"></span>
                        FITUR SEDANG DIPERSIAPKAN · COMING SOON
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-extrabold text-ink tracking-tight">
                        Tugas Coding: Level {{ $page->position }}
                    </h1>

                    <p class="text-base sm:text-lg font-medium text-brand-deep">
                        {{ $page->title }}
                    </p>

                    <p class="text-sm text-ink-muted leading-relaxed max-w-xl mx-auto">
                        Fitur interaktif <strong>Editor JavaScript</strong> dan lingkungan live browser sandbox untuk <strong>Tugas Coding</strong> saat ini sedang di-hold untuk penyempurnaan compiler dan evaluasi otomatis.
                    </p>
                </div>

                {{-- Level Status Banner --}}
                <div class="bg-paper border border-rule rounded-panel p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="space-y-1 text-center sm:text-left">
                        <div class="text-xs font-mono font-bold uppercase tracking-wider text-ink-muted">
                            Progres Belajar Anda di Level Ini
                        </div>
                        <div class="text-sm font-semibold text-ink">
                            Kuis Terjawab: <span class="{{ count($markedCheckpointSlugs) === $page->checkpoints->count() && $page->checkpoints->count() > 0 ? 'text-pass font-bold' : 'text-brand-deep' }} font-mono">{{ count($markedCheckpointSlugs) }} / {{ $page->checkpoints->count() }} Selesai</span>
                            <span class="text-ink-muted mx-1">•</span>
                            Progres Modul: <span class="font-mono {{ $progress->is_completed ? 'text-pass font-bold' : 'text-ink' }}">{{ $progress->percent_complete }}% {{ $progress->is_completed ? '(Selesai)' : '' }}</span>
                        </div>
                    </div>

                    <div class="w-full sm:w-48 bg-rule h-2 rounded-full overflow-hidden shrink-0">
                        <div
                            class="{{ $progress->is_completed ? 'bg-pass' : 'bg-brand' }} h-full transition-all duration-300"
                            style="width: {{ $progress->percent_complete }}%"
                        ></div>
                    </div>
                </div>

                {{-- Features Preview Grid --}}
                <div class="space-y-3">
                    <div class="text-xs font-mono font-bold uppercase tracking-wider text-ink-muted text-center sm:text-left">
                        Fitur Yang Sedang Disiapkan:
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <div class="bg-paper border border-rule rounded-panel p-4 space-y-1.5 hover:border-brand/40 transition-colors">
                            <div class="text-sm font-bold text-ink flex items-center gap-1.5">
                                <span>⚡</span>
                                <span>Editor JavaScript</span>
                            </div>
                            <p class="text-xs text-ink-muted leading-relaxed">
                                Monaco Code Editor dengan syntax highlighting modern, auto-complete, dan penataan kode otomatis.
                            </p>
                        </div>

                        <div class="bg-paper border border-rule rounded-panel p-4 space-y-1.5 hover:border-brand/40 transition-colors">
                            <div class="text-sm font-bold text-ink flex items-center gap-1.5">
                                <span>🖥️</span>
                                <span>Live Visual Sandbox</span>
                            </div>
                            <p class="text-xs text-ink-muted leading-relaxed">
                                Pratinjau langsung visualisasi HTML, CSS Flexbox & Grid secara real-time langsung di browser.
                            </p>
                        </div>

                        <div class="bg-paper border border-rule rounded-panel p-4 space-y-1.5 hover:border-brand/40 transition-colors">
                            <div class="text-sm font-bold text-ink flex items-center gap-1.5">
                                <span>🧪</span>
                                <span>Compiler Auto-Grading</span>
                            </div>
                            <p class="text-xs text-ink-muted leading-relaxed">
                                Evaluasi otomatis input-output dan verifikasi DOM tanpa perlu instalasi tools tambahan.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Navigation Actions --}}
                <div class="pt-4 border-t border-rule flex flex-col sm:flex-row items-center justify-between gap-3">
                    <a
                        href="{{ route('roadmap.level', $page->slug) }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-paper border border-rule text-ink text-xs sm:text-sm font-medium rounded hover:bg-sheet transition-colors"
                    >
                        📖 Pelajari Materi: {{ $page->title }}
                    </a>

                    <div class="flex flex-col sm:flex-row items-center gap-2.5 w-full sm:w-auto">
                        <a
                            href="{{ route('roadmap.quiz', $page->slug) }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-brand text-white text-xs sm:text-sm font-semibold rounded hover:bg-brand-deep transition-colors shadow-xs text-center"
                            style="color: #ffffff !important; background-color: #c92a2a !important;"
                        >
                            ✍️ Buka Kuis Pemahaman Level Ini →
                        </a>

                        @if($nextLevel)
                            <a
                                href="{{ route('roadmap.level', $nextLevel->slug) }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-paper border border-rule text-brand-deep hover:text-ink text-xs sm:text-sm font-semibold rounded hover:bg-sheet transition-colors"
                            >
                                Lanjut Level {{ $nextLevel->position }} →
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
