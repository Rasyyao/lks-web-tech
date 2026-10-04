{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Dedicated Quiz Page for Roadmap Level (/belajar/{slug}/kuis) --}}
{{-- Aesthetic: Exam syllabus & mastery board for SMK Telkom Purwokerto (white sheets, charcoal ink, brand accents) --}}
<div class="py-6 sm:py-8">
    <div class="mx-auto max-w-7xl xl:max-w-[1400px] px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Breadcrumb & Back Link --}}
        <div class="flex items-center justify-between text-xs text-ink-muted">
            <nav class="flex items-center gap-1.5" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}" class="hover:text-ink">Beranda</a>
                <span>/</span>
                <a href="{{ route('roadmap.index') }}" class="hover:text-ink">Roadmap Client</a>
                <span>/</span>
                <a href="{{ route('roadmap.level', $page->slug) }}" class="hover:text-ink">{{ $page->title }}</a>
                <span>/</span>
                <span class="text-ink font-semibold">Kuis Pemahaman</span>
            </nav>

            <a href="{{ route('roadmap.level', $page->slug) }}" class="inline-flex items-center gap-1 text-xs text-brand-deep hover:underline">
                ← Kembali ke Materi Belajar
            </a>
        </div>

        {{-- Level Header Card --}}
        <div class="bg-sheet border border-rule rounded-panel p-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-xs font-mono font-bold bg-brand/10 text-brand-deep border border-brand/20">
                            LEVEL {{ $page->position }}
                        </span>

                        <span class="text-xs bg-paper border border-rule text-ink-muted px-2 py-0.5 rounded font-medium">
                            Kuis Uji Pemahaman
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight">
                        Kuis: {{ $page->title }}
                    </h1>

                    <p class="text-sm text-ink-muted leading-relaxed max-w-2xl">
                        Jawab seluruh soal pilihan ganda di bawah ini berdasarkan materi yang telah kamu pelajari. Seluruh kuis harus terjawab benar sebelum dapat menuntaskan modul ini.
                    </p>
                </div>

                {{-- Quick Stats Box --}}
                <div class="bg-paper border border-rule rounded-panel p-4 min-w-[240px] text-right">
                    <div class="flex justify-between items-baseline mb-1">
                        <span class="text-xs text-ink-muted font-medium uppercase">Kuis Terjawab</span>
                        <span class="text-sm font-bold font-mono {{ count($markedCheckpointSlugs) === $page->checkpoints->count() ? 'text-pass' : 'text-brand-deep' }}">
                            {{ count($markedCheckpointSlugs) }} / {{ $page->checkpoints->count() }}
                        </span>
                    </div>
                    <div class="w-full bg-rule h-2 rounded-full overflow-hidden mb-2">
                        <div
                            class="{{ count($markedCheckpointSlugs) === $page->checkpoints->count() ? 'bg-pass' : 'bg-brand' }} h-full transition-all duration-300"
                            style="width: {{ $page->checkpoints->count() > 0 ? round((count($markedCheckpointSlugs) / $page->checkpoints->count()) * 100) : 100 }}%"
                        ></div>
                    </div>
                    <div class="text-xs text-ink-muted text-left">
                        Total progres modul: <strong>{{ $progress->percent_complete }}%</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Level Navigation Stepper / Tabs --}}
        <div class="flex items-center gap-2 border-b border-rule">
            <a
                href="{{ route('roadmap.level', $page->slug) }}"
                class="py-3 px-4 font-semibold text-xs sm:text-sm border-b-2 border-transparent text-ink-muted hover:text-ink transition-colors inline-flex items-center gap-2"
            >
                <span>📖</span>
                <span>1. Materi Pembelajaran</span>
            </a>
            <a
                href="{{ route('roadmap.quiz', $page->slug) }}"
                class="py-3 px-4 font-bold text-xs sm:text-sm border-b-2 border-brand text-brand-deep transition-colors inline-flex items-center gap-2"
            >
                <span>❓</span>
                <span>2. Kuis Pemahaman ({{ count($markedCheckpointSlugs) }}/{{ $page->checkpoints->count() }})</span>
            </a>
            <a
                href="{{ route('roadmap.task', $page->slug) }}"
                class="py-3 px-4 font-semibold text-xs sm:text-sm border-b-2 border-transparent text-ink-muted hover:text-ink transition-colors inline-flex items-center gap-2"
            >
                <span>💻</span>
                <span>3. Tugas Coding ({{ $passedExerciseCount }}/{{ $page->exercises->count() }})</span>
            </a>
        </div>

        {{-- Stepped Quiz Container (One Question at a Time) --}}
        <div
            x-data="{
                currentIndex: @entangle('currentIndex'),
                total: {{ $page->checkpoints->count() }},
                goTo(index) {
                    if (index >= 0 && index < this.total) {
                        this.currentIndex = index;
                    }
                },
                next() {
                    if (this.currentIndex < this.total - 1) {
                        this.currentIndex++;
                    }
                },
                prev() {
                    if (this.currentIndex > 0) {
                        this.currentIndex--;
                    }
                }
            }"
            class="space-y-6"
        >
            {{-- Question Indicator Pills Navigation --}}
            @if($page->checkpoints->count() > 1)
                <div class="bg-sheet border border-rule rounded-panel p-4 flex flex-wrap items-center justify-between gap-3 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono font-medium text-ink-muted uppercase">Daftar Soal:</span>
                        <div class="flex flex-wrap items-center gap-1.5">
                            @foreach($page->checkpoints as $cpIdx => $cpItem)
                                @php
                                    $isItemUnderstood = in_array($cpItem->slug, $markedCheckpointSlugs, true);
                                @endphp
                                <button
                                    type="button"
                                    @click="goTo({{ $cpIdx }})"
                                    class="w-8 h-8 rounded font-mono text-xs font-bold transition-all flex items-center justify-center border"
                                    :class="{
                                        'ring-2 ring-brand border-brand bg-brand text-white shadow-sm': currentIndex === {{ $cpIdx }},
                                        'bg-pass/10 text-pass border-pass/40 hover:bg-pass/20': currentIndex !== {{ $cpIdx }} && {{ $isItemUnderstood ? 'true' : 'false' }},
                                        'bg-paper text-ink border-rule hover:bg-sheet': currentIndex !== {{ $cpIdx }} && !{{ $isItemUnderstood ? 'true' : 'false' }}
                                    }"
                                >
                                    @if($isItemUnderstood)
                                        ✓
                                    @else
                                        {{ $cpIdx + 1 }}
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="text-xs font-mono text-ink-muted">
                        Soal <span class="font-bold text-ink" x-text="currentIndex + 1"></span> dari <span class="font-bold text-ink">{{ $page->checkpoints->count() }}</span>
                    </div>
                </div>
            @endif

            {{-- Question Cards --}}
            @forelse($page->checkpoints as $cpIndex => $cp)
                @php
                    $isUnderstood = in_array($cp->slug, $markedCheckpointSlugs, true);
                    $savedMark = $checkpointMarks[$cp->slug] ?? null;
                    $selectedAnswer = $savedMark?->selected_answer;
                    $feedback = session('quiz_feedback_' . $cp->slug);
                    $options = is_array($cp->options) ? $cp->options : [];
                @endphp
                <div
                    x-show="currentIndex === {{ $cpIndex }}"
                    x-cloak
                    class="bg-sheet border {{ $isUnderstood ? 'border-pass/40 bg-pass/5' : 'border-rule' }} rounded-panel p-6 space-y-6 transition-all shadow-sm"
                    x-data="{
                        selectedOption: '{{ $selectedAnswer ?? '' }}',
                        isSubmitting: false,
                        submitAnswer() {
                            if (!this.selectedOption) return;
                            this.isSubmitting = true;
                            $wire.answerQuiz('{{ $cp->slug }}', this.selectedOption)
                                .then(() => { this.isSubmitting = false; });
                        }
                    }"
                >
                    <div class="flex items-start justify-between gap-3 border-b border-rule/60 pb-3">
                        <div class="flex items-start gap-3">
                            <span class="w-8 h-8 rounded-full {{ $isUnderstood ? 'bg-pass text-white' : 'bg-ink text-white' }} font-mono text-sm flex items-center justify-center font-bold flex-shrink-0 mt-0.5">
                                {{ $loop->iteration }}
                            </span>
                            <div>
                                <span class="text-xs uppercase font-mono tracking-wider text-ink-muted block mb-1">
                                    Pertanyaan {{ $loop->iteration }} dari {{ $page->checkpoints->count() }}
                                </span>
                                <h2 class="text-base sm:text-lg font-semibold text-ink leading-relaxed">
                                    {{ $cp->prompt }}
                                </h2>
                            </div>
                        </div>
                        <div>
                            @if($isUnderstood)
                                <span class="px-2.5 py-1 rounded text-xs font-bold bg-pass/10 text-pass border border-pass/30 inline-flex items-center gap-1 flex-shrink-0">
                                    ✓ Terjawab Benar
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded text-xs font-medium bg-paper text-ink-muted border border-rule inline-flex items-center gap-1 flex-shrink-0">
                                    Belum Terjawab
                                </span>
                            @endif
                        </div>
                    </div>

                    @if(!empty($options))
                        <div class="grid grid-cols-1 gap-2.5 pt-1">
                            @foreach($options as $index => $opt)
                                @php
                                    $optKey = is_array($opt) ? ($opt['id'] ?? (string)$index) : (string)$index;
                                    $optText = is_array($opt) ? ($opt['text'] ?? '') : (string)$opt;
                                    $optKeyUpper = strtoupper($optKey);
                                    $isCorrectChoice = (strtolower($optKey) === strtolower((string)$cp->correct_answer));
                                @endphp
                                <label
                                    @click="selectedOption = '{{ $optKey }}'"
                                    class="flex items-start gap-3 p-3.5 rounded border text-sm cursor-pointer transition-colors relative"
                                    :class="{
                                        'border-brand bg-brand/5 ring-1 ring-brand': selectedOption.toLowerCase() === '{{ strtolower($optKey) }}' && !{{ $isUnderstood ? 'true' : 'false' }},
                                        'border-rule bg-paper hover:bg-sheet': selectedOption.toLowerCase() !== '{{ strtolower($optKey) }}' && !{{ $isUnderstood ? 'true' : 'false' }},
                                        'border-pass/60 bg-pass/10 text-ink font-medium': {{ ($isUnderstood && $isCorrectChoice) ? 'true' : 'false' }},
                                        'border-rule bg-paper/60 text-ink-muted opacity-60': {{ ($isUnderstood && !$isCorrectChoice) ? 'true' : 'false' }}
                                    }"
                                >
                                    <input
                                        type="radio"
                                        name="quiz_{{ $cp->slug }}"
                                        value="{{ $optKey }}"
                                        x-model="selectedOption"
                                        {{ $isUnderstood ? 'disabled' : '' }}
                                        class="mt-1 text-brand focus:ring-brand"
                                    >
                                    <div class="flex-1 leading-relaxed">
                                        <span class="font-bold mr-1.5">{{ $optKeyUpper }}.</span>
                                        <span>{{ $optText }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    {{-- Feedback Alert --}}
                    @if($feedback)
                        <div class="p-4 rounded border text-sm {{ $feedback['is_correct'] ? 'bg-pass/10 border-pass/40 text-ink' : 'bg-brand/10 border-brand/40 text-ink' }}">
                            <div class="font-bold {{ $feedback['is_correct'] ? 'text-pass' : 'text-brand' }}">
                                {{ $feedback['is_correct'] ? '✓ Jawaban Benar!' : '✕ Jawaban Belum Tepat' }}
                            </div>
                            <div class="mt-1 text-ink-muted leading-relaxed">
                                {{ $feedback['message'] }}
                            </div>
                        </div>
                    @elseif($isUnderstood && $cp->explanation)
                        <div class="p-4 rounded border bg-pass/10 border-pass/30 text-sm text-ink">
                            <div class="font-bold text-pass">
                                ✓ Penjelasan:
                            </div>
                            <div class="mt-1 text-ink-muted leading-relaxed">
                                {{ $cp->explanation }}
                            </div>
                        </div>
                    @endif

                    {{-- Question Actions & Stepper Controls --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-rule/60">
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                @click="submitAnswer()"
                                :disabled="!selectedOption || isSubmitting"
                                class="px-5 py-2.5 bg-brand text-white text-sm font-semibold rounded hover:bg-brand-deep disabled:opacity-50 transition-colors inline-flex items-center justify-center gap-2"
                                style="color: #ffffff !important; background-color: #c92a2a !important;"
                            >
                                <span x-show="!isSubmitting">{{ $isUnderstood ? 'Jawab Ulang' : 'Kirim Jawaban' }}</span>
                                <span x-show="isSubmitting" style="display: none;">Memeriksa...</span>
                            </button>
                        </div>

                        {{-- Next / Prev Buttons --}}
                        <div class="flex items-center gap-2 self-end sm:self-auto">
                            <button
                                type="button"
                                @click="prev()"
                                :disabled="currentIndex === 0"
                                class="px-4 py-2 bg-paper border border-rule text-ink text-xs sm:text-sm font-medium rounded hover:bg-sheet disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                            >
                                ← Soal Sebelumnya
                            </button>

                            <template x-if="currentIndex < total - 1">
                                <button
                                    type="button"
                                    @click="next()"
                                    class="px-4 py-2 bg-paper border border-rule text-ink text-xs sm:text-sm font-medium rounded hover:bg-sheet transition-colors"
                                >
                                    Soal Selanjutnya →
                                </button>
                            </template>

                            <template x-if="currentIndex === total - 1">
                                <a
                                    href="{{ route('roadmap.task', $page->slug) }}"
                                    class="px-4 py-2 bg-brand text-white text-xs sm:text-sm font-semibold rounded hover:bg-brand-deep transition-colors inline-flex items-center gap-1.5"
                                    style="color: #ffffff !important; background-color: #c92a2a !important;"
                                >
                                    Lanjut ke Tugas Coding →
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-sheet border border-rule rounded-panel p-8 text-center space-y-3">
                    <p class="text-ink-muted text-sm">Belum ada kuis untuk level ini.</p>
                </div>
            @endforelse
        </div>

        {{-- Bottom Navigation --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-rule">
            <a href="{{ route('roadmap.level', $page->slug) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-sheet border border-rule text-ink text-sm font-medium rounded hover:bg-paper transition-colors w-full sm:w-auto justify-center">
                ← Kembali ke Materi Pembelajaran
            </a>

            <a href="{{ route('roadmap.task', $page->slug) }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-brand text-white text-sm font-semibold rounded hover:bg-brand-deep transition-colors w-full sm:w-auto justify-center">
                Lanjut ke Tugas Coding ({{ $passedExerciseCount }}/{{ $page->exercises->count() }}) →
            </a>
        </div>

    </div>
</div>
