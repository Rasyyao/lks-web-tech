{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Practice Setup (/latihan) --}}
{{-- Primary job of the screen: Configure practice parameters (topic, difficulty, count, timer) and view recent past attempts. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, pass, paper, tint --}}
{{-- Type roles: Schibsted Grotesk for form labels and titles; tabular-nums for counts, minutes, and scores --}}
{{-- Layout idea: Two-column layout on desktop: Left is test configuration, right is recent attempts list --}}
{{-- What I changed after the "would any app have this?" check: Removed gamified streak flames and shiny badge unlocks; focused strictly on purposeful student test setup. --}}

<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-ink">
            {{ __('practice.start_title') }}
        </h1>
        <p class="text-xs text-ink-muted">
            Bank Soal & Simulasi Ujian LKS Web Technologies
        </p>
    </div>

    @error('general')
        <div class="bg-tint border-l-4 border-brand p-4 rounded-panel text-sm text-brand-deep" role="alert">
            {{ $message }}
        </div>
    @enderror

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Configuration Form --}}
        <div class="lg:col-span-2">
            <x-panel class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-ink">
                        Parameter Latihan
                    </h2>
                    <p class="text-xs text-ink-muted">
                        Pilih topik dan tingkat kesulitan untuk menyusun lembar soal acak.
                    </p>
                </div>

                <form wire:submit="start" class="space-y-6 border-t border-rule pt-4">
                    {{-- 1. Topic --}}
                    <div class="space-y-1">
                        <label for="topic" class="block text-sm font-medium text-ink">
                            {{ __('practice.topic') }}
                        </label>
                        <select
                            id="topic"
                            wire:model="topic"
                            class="w-full rounded-cell border border-rule px-3 py-2 text-sm bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                        >
                            <option value="all">Semua Topik Campuran</option>
                            @foreach($topics as $t)
                                <option value="{{ $t->id }}">
                                    {{ $t->name }} ({{ $t->questions_count }} soal)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Difficulty --}}
                    <div class="space-y-1">
                        <label for="difficulty" class="block text-sm font-medium text-ink">
                            {{ __('practice.difficulty') }}
                        </label>
                        <select
                            id="difficulty"
                            wire:model="difficulty"
                            class="w-full rounded-cell border border-rule px-3 py-2 text-sm bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                        >
                            <option value="all">Semua Tingkat Kesulitan (1 - 5)</option>
                            <option value="1">Tingkat 1 — Dasar</option>
                            <option value="2">Tingkat 2 — Menengah Rendah</option>
                            <option value="3">Tingkat 3 — Menengah</option>
                            <option value="4">Tingkat 4 — Lanjutan</option>
                            <option value="5">Tingkat 5 — Simulasi LKS Nasional</option>
                        </select>
                    </div>

                    {{-- 3. Question Count --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-ink">
                            {{ __('practice.question_count') }}
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach([5, 10, 20] as $cnt)
                                <label class="border rounded-cell p-3 flex items-center justify-center cursor-pointer transition-colors {{ $questionCount === $cnt ? 'border-brand bg-tint font-bold text-brand-deep' : 'border-rule bg-sheet text-ink hover:border-ink-muted' }}">
                                    <input
                                        type="radio"
                                        wire:model="questionCount"
                                        value="{{ $cnt }}"
                                        class="sr-only"
                                    >
                                    <span class="text-sm tabular-nums">{{ $cnt }} Soal</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- 4. Timer Toggle --}}
                    <div class="space-y-3 pt-2 border-t border-rule">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="useTimer" class="text-sm font-medium text-ink block cursor-pointer">
                                    {{ __('practice.timer') }}
                                </label>
                                <span class="text-xs text-ink-muted">
                                    Latihan berakhir otomatis saat waktu habis.
                                </span>
                            </div>
                            <input
                                type="checkbox"
                                id="useTimer"
                                wire:model.live="useTimer"
                                class="rounded-cell border-rule text-brand focus:ring-brand-deep h-4 w-4"
                            >
                        </div>

                        @if($useTimer)
                            <div class="pt-2">
                                <label for="timerMinutes" class="block text-xs font-medium text-ink mb-1">
                                    Durasi Waktu:
                                </label>
                                <select
                                    id="timerMinutes"
                                    wire:model="timerMinutes"
                                    class="w-full sm:w-48 rounded-cell border border-rule px-3 py-2 text-sm bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                                >
                                    <option value="5">5 Menit</option>
                                    <option value="10">10 Menit</option>
                                    <option value="15">15 Menit</option>
                                    <option value="30">30 Menit</option>
                                    <option value="45">45 Menit</option>
                                    <option value="60">60 Menit</option>
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-rule flex items-center justify-end">
                        <x-button type="submit" size="lg">
                            {{ __('practice.start_button') }}
                        </x-button>
                    </div>
                </form>
            </x-panel>
        </div>

        {{-- Recent Attempts History --}}
        <div>
            <x-panel class="space-y-4">
                <div>
                    <h2 class="text-base font-bold text-ink">
                        {{ __('practice.recent_attempts') }}
                    </h2>
                    <p class="text-xs text-ink-muted">
                        Riwayat pengerjaan latihan sebelumnya
                    </p>
                </div>

                @if($recentAttempts->isEmpty())
                    <p class="text-xs text-ink-muted py-4 text-center border-t border-rule">
                        {{ __('practice.no_attempts') }}
                    </p>
                @else
                    <ul class="divide-y divide-rule border-t border-rule" role="list">
                        @foreach($recentAttempts as $attempt)
                            <li class="py-3 space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <time class="text-ink-muted tabular-nums">
                                        {{ $attempt->submitted_at?->translatedFormat('d M, H:i') }}
                                    </time>
                                    <span class="font-bold text-ink tabular-nums">
                                        {{ $attempt->points_earned }} poin
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-ink-muted">
                                        {{ $attempt->question_count }} soal ({{ round($attempt->score_pct) }}%)
                                    </span>
                                    <a
                                        href="{{ route('practice.result', $attempt->id) }}"
                                        class="text-xs text-brand-deep hover:underline font-medium focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                                    >
                                        Buka hasil
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-panel>
        </div>
    </div>
</div>
