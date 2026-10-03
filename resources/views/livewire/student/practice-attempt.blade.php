{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Practice Attempt (/latihan/{attempt}) --}}
{{-- Primary job of the screen: Single question focus mode with immediate auto-save, question number palette, and timer. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, paper, tint, pass --}}
{{-- Type roles: Schibsted Grotesk for questions and options; JetBrains Mono for code blocks; tabular-nums for timer and counts --}}
{{-- Layout idea: Header with progress + countdown timer, question card with options/input, bottom navigation bar + number jump grid --}}
{{-- What I changed after the "would any app have this?" check: No distracting cartoon mascots or confetti sound placeholders; clean focused test-taking environment. --}}

<div
    class="max-w-3xl mx-auto space-y-6"
    @if($remainingSeconds !== null)
        wire:poll.5s="checkTimer"
        x-data="{
            time: {{ $remainingSeconds }},
            timer: null,
            formatTime(seconds) {
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            }
        }"
        x-init="
            timer = setInterval(() => {
                if (time > 0) {
                    time--;
                } else {
                    clearInterval(timer);
                    $wire.checkTimer();
                }
            }, 1000);
        "
        x-on:beforeunload.window="clearInterval(timer)"
    @endif
>
    {{-- Header: Progress and Timer --}}
    <header class="bg-sheet border border-rule rounded-panel p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-sm font-bold text-ink tabular-nums">
                {{ __('practice.question_number', ['number' => $currentIndex + 1]) }}
            </span>
            <span class="text-xs text-ink-muted">
                dari {{ $items->count() }} soal
            </span>
        </div>

        <div class="flex items-center gap-4">
            @if($remainingSeconds !== null)
                <div class="flex items-center gap-1.5 text-brand-deep font-mono font-bold text-sm bg-tint px-2.5 py-1 rounded-cell">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-text="formatTime(time)" class="tabular-nums"></span>
                </div>
            @endif

            <button
                type="button"
                wire:click="openReview"
                class="text-xs font-medium text-ink-muted hover:text-brand-deep underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
            >
                Daftar Soal
            </button>
        </div>
    </header>

    {{-- Question navigation grid palette --}}
    <div class="bg-sheet border border-rule rounded-panel p-3">
        <div class="flex flex-wrap gap-1.5 justify-center sm:justify-start">
            @foreach($items as $idx => $item)
                @php
                    $isAnswered = ! empty($item->answer);
                    $isCurrent = ($idx === $currentIndex && ! $isReviewMode);
                @endphp
                <button
                    type="button"
                    wire:click="goToQuestion({{ $idx }})"
                    class="w-8 h-8 rounded-cell text-xs font-medium tabular-nums transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep
                        {{ $isCurrent ? 'bg-brand text-sheet font-bold' : ($isAnswered ? 'bg-paper text-ink border border-ink-muted/50' : 'bg-sheet text-ink-muted border border-rule hover:border-ink-muted') }}
                    "
                    title="{{ $isAnswered ? 'Sudah dijawab' : 'Belum dijawab' }}"
                >
                    {{ $idx + 1 }}
                </button>
            @endforeach
        </div>
    </div>

    @if($isReviewMode)
        {{-- Review Screen before finishing --}}
        <x-panel class="space-y-6">
            <div>
                <h2 class="text-xl font-bold text-ink">
                    {{ __('practice.review_before_finish') }}
                </h2>
                <p class="text-xs text-ink-muted">
                    Pastikan seluruh soal telah dijawab sebelum mengakhiri latihan.
                </p>
            </div>

            <div class="divide-y divide-rule border-y border-rule" role="list">
                @foreach($items as $idx => $item)
                    @php
                        $isAnswered = ! empty($item->answer);
                    @endphp
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-mono font-medium text-ink tabular-nums w-12">
                                Soal {{ $idx + 1 }}
                            </span>
                            @if($isAnswered)
                                <span class="inline-flex items-center text-xs text-pass font-medium">
                                    ● {{ __('practice.answered') }}
                                </span>
                            @else
                                <span class="inline-flex items-center text-xs text-brand-deep font-medium">
                                    ○ {{ __('practice.unanswered') }}
                                </span>
                            @endif
                        </div>

                        <x-button variant="secondary" size="sm" wire:click="goToQuestion({{ $idx }})">
                            Buka Soal
                        </x-button>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between pt-2">
                <x-button variant="secondary" wire:click="goToQuestion({{ $items->count() - 1 }})">
                    Kembali ke Soal Terakhir
                </x-button>

                <x-button wire:click="finishAttempt" wire:confirm="Apakah kamu yakin ingin menyelesaikan latihan ini?">
                    {{ __('practice.finish_button') }}
                </x-button>
            </div>
        </x-panel>
    @else
        {{-- Single Question Screen --}}
        @if($currentItem)
            @php
                $type = \App\Enums\QuestionType::from($currentItem->snapshot['type']);
                $snapshot = $currentItem->snapshot;
            @endphp
            <x-panel class="space-y-6">
                {{-- Question metadata --}}
                <div class="flex items-center justify-between text-xs text-ink-muted pb-3 border-b border-rule">
                    <span class="tabular-nums">Tingkat Kesulitan: {{ $snapshot['difficulty'] ?? 1 }}</span>
                    <span class="tabular-nums font-medium text-ink">+{{ $snapshot['points'] ?? 10 }} poin</span>
                </div>

                {{-- Question body in Markdown --}}
                <div class="prose max-w-none text-base text-ink">
                    {!! Str::markdown($snapshot['body_md'] ?? '') !!}
                </div>

                {{-- Answering input --}}
                <div class="pt-4 border-t border-rule space-y-3">
                    @if($type === \App\Enums\QuestionType::MultipleChoice || $type === \App\Enums\QuestionType::TrueFalse)
                        <div class="space-y-2" role="radiogroup" aria-label="Pilihan jawaban">
                            @foreach($snapshot['options'] ?? [] as $opt)
                                @php
                                    $isSelected = ($selectedOptionId == $opt['id']);
                                @endphp
                                <label
                                    wire:click="selectOption({{ $opt['id'] }})"
                                    class="flex items-start gap-3 p-3.5 rounded-panel border cursor-pointer transition-colors
                                        {{ $isSelected ? 'border-brand bg-tint font-medium text-ink' : 'border-rule bg-sheet text-ink hover:bg-paper/50' }}
                                    "
                                >
                                    <input
                                        type="radio"
                                        name="option_choice"
                                        value="{{ $opt['id'] }}"
                                        checked="{{ $isSelected }}"
                                        class="mt-0.5 border-rule text-brand focus:ring-brand-deep"
                                    >
                                    <span class="text-sm flex-1 leading-snug">
                                        {{ $opt['label'] }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @elseif($type === \App\Enums\QuestionType::ShortAnswer)
                        <div class="space-y-2">
                            <label for="shortAnswer" class="block text-sm font-medium text-ink">
                                {{ __('practice.your_answer') }}:
                            </label>
                            <input
                                type="text"
                                id="shortAnswer"
                                wire:model.blur="shortAnswerText"
                                placeholder="Tulis jawabanmu di sini..."
                                class="w-full rounded-cell border border-rule px-3.5 py-2.5 text-sm bg-sheet text-ink font-mono focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                            >
                            <p class="text-xs text-ink-muted">
                                Jawaban tidak membedakan huruf besar/kecil. Jawaban disimpan otomatis setelah kamu selesai mengetik.
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Navigation actions --}}
                <div class="flex items-center justify-between pt-6 border-t border-rule">
                    <x-button
                        variant="secondary"
                        wire:click="prevQuestion"
                        :disabled="$currentIndex === 0"
                        class="{{ $currentIndex === 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                    >
                        {{ __('practice.prev_question') }}
                    </x-button>

                    <div class="flex items-center gap-2">
                        @if($currentIndex === $items->count() - 1)
                            <x-button wire:click="openReview">
                                {{ __('practice.review_before_finish') }}
                            </x-button>
                        @else
                            <x-button wire:click="nextQuestion">
                                {{ __('practice.next_question') }}
                            </x-button>
                        @endif
                    </div>
                </div>
            </x-panel>
        @endif
    @endif
</div>
