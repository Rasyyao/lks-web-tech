{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Lembar Jawaban (/latihan/{attempt}/hasil) --}}
{{-- Primary job of the screen: The one memorable thing: a corrected school exam sheet showing score count-up, per-question marks in margin, and clear explanations. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand-deep (wrong marks only), pass (correct marks), paper, gold --}}
{{-- Type roles: Schibsted Grotesk for sheet typography; tabular-nums for scores and counts; JetBrains Mono for code blocks --}}
{{-- Layout idea: Classical printed exam sheet layout with perforated divider border, margin marks (✓ Benar, ✗ Salah), wrong answers expanded first --}}
{{-- What I changed after the "would any app have this?" check: Banned red primary action buttons (red is reserved strictly for 'Salah' marks on this screen per DESIGN_RULES section 3 and 5); action button is neutral outline 'secondary'. Avoided celebration confetti, party horns, or SaaS score gauges. --}}

<div class="max-w-4xl mx-auto space-y-6">
    {{-- Back to practice list --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('practice.start') }}" class="text-xs text-ink-muted hover:text-ink inline-flex items-center gap-1 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
            ← {{ __('practice.start_title') }}
        </a>
        <a href="{{ route('leaderboard') }}" class="text-xs text-ink-muted hover:text-ink underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
            {{ __('general.leaderboard') }} →
        </a>
    </div>

    {{-- The Exam Sheet Card --}}
    <div
        class="bg-sheet border border-rule rounded-panel overflow-hidden shadow-none"
        x-data="{
            targetScore: {{ $attempt->points_earned }},
            displayScore: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? {{ $attempt->points_earned }} : 0,
            init() {
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    return;
                }
                const duration = 750;
                const startTime = performance.now();
                const animate = (currentTime) => {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    this.displayScore = Math.floor(progress * this.targetScore);
                    if (progress < 1) {
                        requestAnimationFrame(animate);
                    } else {
                        this.displayScore = this.targetScore;
                    }
                };
                requestAnimationFrame(animate);
            }
        }"
    >
        {{-- Exam Header Summary --}}
        <header class="p-6 sm:p-8 bg-sheet border-b border-rule space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-ink-muted block">
                        {{ __('practice.result_title') }}
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-bold text-ink mt-0.5">
                        Hasil Latihan Mandiri
                    </h1>
                    <time class="text-xs text-ink-muted tabular-nums block mt-1">
                        {{ $attempt->submitted_at?->translatedFormat('l, d F Y — H:i') }}
                    </time>
                </div>

                <div class="text-left sm:text-right space-y-1">
                    <span class="text-xs text-ink-muted block">Waktu Pengerjaan</span>
                    <span class="text-sm font-medium text-ink tabular-nums">
                        {{ __('practice.completed_in', ['time' => $durationMinutes . ' menit']) }}
                    </span>
                </div>
            </div>

            <div class="pt-4 border-t border-rule grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl sm:text-4xl font-bold text-ink tabular-nums" x-text="displayScore">
                        {{ $attempt->points_earned }}
                    </span>
                    <span class="text-sm text-ink-muted">
                        poin diperoleh ({{ round($attempt->score_pct) }}%)
                    </span>
                </div>

                <div class="flex items-center sm:justify-end gap-3 text-sm">
                    <span class="inline-flex items-center gap-1 text-pass font-medium">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                        <span class="tabular-nums">{{ $correctCount }} benar</span>
                    </span>
                    <span class="text-rule">•</span>
                    <span class="inline-flex items-center gap-1 text-brand-deep font-medium">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                        </svg>
                        <span class="tabular-nums">{{ $wrongCount }} salah</span>
                    </span>
                </div>
            </div>
        </header>

        {{-- Question Items List --}}
        {{-- DESIGN_RULES: Wrong answers come first and are expanded; correct ones are collapsed --}}
        <div class="divide-y divide-rule" role="list">
            @foreach($sortedItems as $index => $item)
                @php
                    $isCorrect = $item->is_correct;
                    $type = \App\Enums\QuestionType::from($item->snapshot['type']);
                    $snapshot = $item->snapshot;
                    $studentAnswer = $item->answer;
                @endphp
                <article
                    class="p-6 transition-colors {{ $isCorrect ? 'bg-sheet' : 'bg-tint/30' }}"
                    x-data="{ expanded: {{ $isCorrect ? 'false' : 'true' }} }"
                >
                    <div class="flex items-start justify-between gap-4">
                        {{-- Margin mark: SVG + word per DESIGN_RULES section 5 --}}
                        <div class="flex items-center gap-3">
                            <x-mark :correct="$isCorrect" />
                            <span class="text-xs font-mono text-ink-muted tabular-nums">
                                Soal #{{ $item->position + 1 }}
                            </span>
                        </div>

                        <div class="flex items-center gap-4">
                            <span class="text-xs font-bold tabular-nums {{ $isCorrect ? 'text-pass' : 'text-ink-muted' }}">
                                {{ $isCorrect ? '+' . $item->points . ' poin' : '+0 poin' }}
                            </span>

                            @if($isCorrect)
                                <button
                                    type="button"
                                    x-on:click="expanded = !expanded"
                                    class="text-xs text-ink-muted hover:text-ink underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                                >
                                    <span x-text="expanded ? 'Sembunyikan' : 'Lihat Soal'">Lihat Soal</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Question Content --}}
                    <div x-show="expanded" class="mt-4 pl-0 sm:pl-7 space-y-4">
                        <div class="prose max-w-none text-sm text-ink">
                            {!! Str::markdown($snapshot['body_md'] ?? '') !!}
                        </div>

                        {{-- Student answer and correct answer breakdown --}}
                        <div class="bg-sheet border border-rule rounded-cell p-4 space-y-2 text-xs">
                            {{-- Student's given answer --}}
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="text-ink-muted font-medium w-28 shrink-0">
                                    {{ __('practice.your_answer') }}:
                                </span>
                                <div class="font-medium">
                                    @if(empty($studentAnswer))
                                        <span class="text-brand-deep italic">Tidak dijawab</span>
                                    @elseif($type === \App\Enums\QuestionType::MultipleChoice || $type === \App\Enums\QuestionType::TrueFalse)
                                        @php
                                            $optChosen = collect($snapshot['options'] ?? [])->firstWhere('id', $studentAnswer['option_id'] ?? null);
                                        @endphp
                                        <span class="{{ $isCorrect ? 'text-pass' : 'text-brand-deep' }}">
                                            {{ $optChosen['label'] ?? 'Pilihan tidak valid' }}
                                        </span>
                                    @elseif($type === \App\Enums\QuestionType::ShortAnswer)
                                        <span class="font-mono {{ $isCorrect ? 'text-pass' : 'text-brand-deep' }}">
                                            {{ $studentAnswer['text'] ?? '' }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Correct answer if student was wrong --}}
                            @if(! $isCorrect)
                                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 pt-2 border-t border-rule">
                                    <span class="text-ink-muted font-medium w-28 shrink-0">
                                        {{ __('practice.correct_answer') }}:
                                    </span>
                                    <div class="font-medium text-pass">
                                        @if($type === \App\Enums\QuestionType::MultipleChoice || $type === \App\Enums\QuestionType::TrueFalse)
                                            @php
                                                $correctOpt = collect($snapshot['options'] ?? [])->firstWhere('is_correct', true);
                                            @endphp
                                            <span>{{ $correctOpt['label'] ?? '—' }}</span>
                                        @elseif($type === \App\Enums\QuestionType::ShortAnswer)
                                            @php
                                                $answersList = collect($snapshot['accepted_answers'] ?? [])->pluck('text')->join(' / ');
                                            @endphp
                                            <span class="font-mono">{{ $answersList }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Explanation if provided --}}
                        @if(! empty($snapshot['explanation_md']))
                            <div class="bg-paper border border-rule rounded-cell p-3.5 space-y-1">
                                <span class="text-xs font-semibold text-ink-muted block uppercase tracking-wider">
                                    {{ __('practice.explanation') }}:
                                </span>
                                <div class="text-xs text-ink prose max-w-none">
                                    {!! Str::markdown($snapshot['explanation_md']) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Footer actions per DESIGN_RULES: Neutral outline button in ink, NOT brand red! --}}
        <footer class="p-6 bg-paper border-t border-rule flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-xs text-ink-muted">
                Poin telah otomatis dihitung dan dimasukkan ke papan peringkat.
            </span>

            <div class="flex items-center gap-3">
                <x-button variant="secondary" :href="route('practice.start')">
                    {{ __('practice.retry') }}
                </x-button>
                <x-button variant="secondary" :href="route('leaderboard')">
                    Lihat Peringkat
                </x-button>
            </div>
        </footer>
    </div>
</div>
