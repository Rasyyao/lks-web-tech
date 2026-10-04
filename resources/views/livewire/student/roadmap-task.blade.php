{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Dedicated Coding Tasks & Code Editor Page for Roadmap Level (/belajar/{slug}/tugas) --}}
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
                <span class="text-ink font-semibold">Tugas Coding</span>
            </nav>

            <a href="{{ route('roadmap.quiz', $page->slug) }}" class="inline-flex items-center gap-1 text-xs text-brand-deep hover:underline">
                ← Kembali ke Kuis
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
                            Tugas Pemrograman Langsung (In-Browser I/O Runner)
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight">
                        Tugas Coding: {{ $page->title }}
                    </h1>

                    <p class="text-sm text-ink-muted leading-relaxed max-w-2xl">
                        Tulis dan jalankan kode JavaScript secara mandiri langsung di browser. Compiler I/O runner akan menangkap output return value atau console log dan mencocokkannya secara fleksibel dengan test cases otomatis.
                    </p>
                </div>

                {{-- Quick Stats Box --}}
                <div class="bg-paper border border-rule rounded-panel p-4 min-w-[240px] text-right">
                    <div class="flex justify-between items-baseline mb-1">
                        <span class="text-xs text-ink-muted font-medium uppercase">Tugas Selesai</span>
                        <span class="text-sm font-bold font-mono {{ count($passedExerciseSlugs) === $page->exercises->count() ? 'text-pass' : 'text-brand-deep' }}">
                            {{ count($passedExerciseSlugs) }} / {{ $page->exercises->count() }} Lulus
                        </span>
                    </div>
                    <div class="w-full bg-rule h-2 rounded-full overflow-hidden mb-2">
                        <div
                            class="{{ count($passedExerciseSlugs) === $page->exercises->count() ? 'bg-pass' : 'bg-brand' }} h-full transition-all duration-300"
                            style="width: {{ $page->exercises->count() > 0 ? round((count($passedExerciseSlugs) / $page->exercises->count()) * 100) : 100 }}%"
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
                class="py-3 px-4 font-semibold text-xs sm:text-sm border-b-2 border-transparent text-ink-muted hover:text-ink transition-colors inline-flex items-center gap-2"
            >
                <span>❓</span>
                <span>2. Kuis Pemahaman ({{ count($markedCheckpointSlugs) }}/{{ $page->checkpoints->count() }})</span>
            </a>
            <a
                href="{{ route('roadmap.task', $page->slug) }}"
                class="py-3 px-4 font-bold text-xs sm:text-sm border-b-2 border-brand text-brand-deep transition-colors inline-flex items-center gap-2"
            >
                <span>💻</span>
                <span>3. Tugas Coding ({{ count($passedExerciseSlugs) }}/{{ $page->exercises->count() }})</span>
            </a>
        </div>

        {{-- Stepped Coding Task Container (One Task at a Time) --}}
        <div
            x-data="{
                activeTaskIndex: 0,
                totalTasks: {{ $page->exercises->count() }},
                goToTask(idx) {
                    if (idx >= 0 && idx < this.totalTasks) {
                        this.activeTaskIndex = idx;
                    }
                },
                nextTask() {
                    if (this.activeTaskIndex < this.totalTasks - 1) {
                        this.activeTaskIndex++;
                    }
                },
                prevTask() {
                    if (this.activeTaskIndex > 0) {
                        this.activeTaskIndex--;
                    }
                }
            }"
            class="space-y-6"
        >
            {{-- Task Selector Navigation (Non-Scrollable Stepper) --}}
            @if($page->exercises->count() > 1)
                <div class="bg-sheet border border-rule rounded-panel p-4 flex flex-wrap items-center justify-between gap-3 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono font-medium text-ink-muted uppercase">Pilih Tugas Coding:</span>
                        <div class="flex flex-wrap items-center gap-1.5">
                            @foreach($page->exercises as $exIdx => $exItem)
                                @php
                                    $isExPassed = in_array($exItem->slug, $passedExerciseSlugs, true);
                                @endphp
                                <button
                                    type="button"
                                    @click="goToTask({{ $exIdx }})"
                                    class="px-3.5 py-1.5 rounded font-mono text-xs font-bold transition-all flex items-center gap-1.5 border"
                                    :class="{
                                        'ring-2 ring-brand border-brand bg-brand text-white shadow-sm': activeTaskIndex === {{ $exIdx }},
                                        'bg-pass/10 text-pass border-pass/40 hover:bg-pass/20': activeTaskIndex !== {{ $exIdx }} && {{ $isExPassed ? 'true' : 'false' }},
                                        'bg-paper text-ink border-rule hover:bg-sheet': activeTaskIndex !== {{ $exIdx }} && !{{ $isExPassed ? 'true' : 'false' }}
                                    }"
                                >
                                    <span>Tugas {{ $exIdx + 1 }}: {{ Str::limit($exItem->title, 26) }}</span>
                                    @if($isExPassed)
                                        <span class="font-bold">✓</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="text-xs font-mono text-ink-muted">
                        Tugas <span class="font-bold text-ink" x-text="activeTaskIndex + 1"></span> dari <span class="font-bold text-ink">{{ $page->exercises->count() }}</span>
                    </div>
                </div>
            @endif

            {{-- Task Cards (Displayed 1 at a Time) --}}
            @forelse($page->exercises as $exerciseIndex => $exercise)
                @php
                    $isPassed = in_array($exercise->slug, $passedExerciseSlugs, true);
                @endphp
                <div
                    x-show="activeTaskIndex === {{ $exerciseIndex }}"
                    x-cloak
                    class="bg-sheet border {{ $isPassed ? 'border-pass/40' : 'border-rule' }} rounded-panel p-6 shadow-sm space-y-4"
                    x-data="codeExerciseRunner({
                        id: {{ $exercise->id }},
                        slug: '{{ $exercise->slug }}',
                        starterCode: @js($exercise->starter_code),
                        testCases: @js($exercise->test_cases),
                        isPassed: @js($isPassed),
                        csrfToken: '{{ csrf_token() }}'
                    })"
                >
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-rule pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-ink text-white font-mono text-xs flex items-center justify-center font-bold">
                                {{ $exercise->position }}
                            </span>
                            <h2 class="text-base sm:text-lg font-bold text-ink">
                                {{ $exercise->title }}
                            </h2>
                        </div>
                        <div>
                            <template x-if="passed">
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-pass/10 text-pass border border-pass/30 inline-flex items-center gap-1">
                                    ✓ Lulus Verifikasi
                                </span>
                            </template>
                            <template x-if="!passed">
                                <span class="px-2.5 py-0.5 rounded text-xs font-medium bg-paper text-ink-muted border border-rule">
                                    Belum Lulus
                                </span>
                            </template>
                        </div>
                    </div>

                    {{-- Instructions --}}
                    <div class="text-sm text-ink leading-relaxed whitespace-pre-line bg-paper/60 p-4 rounded border border-rule/60">
                        {{ $exercise->instructions }}
                    </div>

                    {{-- Code Editor Textarea --}}
                    <div class="space-y-1">
                        <div class="flex justify-between items-center text-xs text-ink-muted font-mono">
                            <span>Editor JavaScript (In-Browser Runner):</span>
                            <span>Tekan Tab untuk indentasi 4 spasi</span>
                        </div>
                        <textarea
                            x-model="code"
                            rows="10"
                            class="w-full font-mono text-xs sm:text-sm bg-ink text-sheet p-3.5 rounded border border-rule focus:outline-none focus:ring-1 focus:ring-brand leading-relaxed"
                            spellcheck="false"
                            @keydown.tab.prevent="insertTab($event)"
                        ></textarea>
                    </div>

                    {{-- Action Bar & Stepper Controls --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 border-t border-rule/60">
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                @click="runTests()"
                                :disabled="isRunning"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand text-white text-xs sm:text-sm font-semibold rounded hover:bg-brand-deep disabled:opacity-50 transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep shadow-xs"
                                style="color: #ffffff !important; background-color: #c92a2a !important;"
                            >
                                <span x-show="!isRunning">▶ Jalankan & Uji Kode</span>
                                <span x-show="isRunning" style="display: none;">Menguji I/O...</span>
                            </button>

                            <button
                                type="button"
                                @click="resetCode()"
                                class="px-3 py-2 text-xs text-ink-muted hover:text-ink hover:underline font-mono"
                            >
                                Reset Kode Awal
                            </button>
                        </div>

                        {{-- Task Stepper Navigation Controls --}}
                        <div class="flex items-center gap-2 self-end sm:self-auto">
                            <button
                                type="button"
                                @click="prevTask()"
                                :disabled="activeTaskIndex === 0"
                                class="px-4 py-2 bg-paper border border-rule text-ink text-xs sm:text-sm font-medium rounded hover:bg-sheet disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                            >
                                ← Tugas Sebelumnya
                            </button>

                            <template x-if="activeTaskIndex < totalTasks - 1">
                                <button
                                    type="button"
                                    @click="nextTask()"
                                    class="px-4 py-2 bg-paper border border-rule text-ink text-xs sm:text-sm font-medium rounded hover:bg-sheet transition-colors"
                                >
                                    Tugas Selanjutnya →
                                </button>
                            </template>

                            <template x-if="activeTaskIndex === totalTasks - 1">
                                @if($nextLevel && $nextLevel->isUnlockedFor(auth()->user()))
                                    <a
                                        href="{{ route('roadmap.level', $nextLevel->slug) }}"
                                        class="px-4 py-2 bg-brand text-white text-xs sm:text-sm font-semibold rounded hover:bg-brand-deep transition-colors inline-flex items-center gap-1.5"
                                        style="color: #ffffff !important; background-color: #c92a2a !important;"
                                    >
                                        Lanjut ke {{ $nextLevel->title }} →
                                    </a>
                                @else
                                    <a
                                        href="{{ route('roadmap.index') }}"
                                        class="px-4 py-2 bg-paper border border-rule text-ink text-xs sm:text-sm font-semibold rounded hover:bg-sheet transition-colors inline-flex items-center gap-1.5"
                                    >
                                        Kembali ke Roadmap →
                                    </a>
                                @endif
                            </template>
                        </div>
                    </div>

                    {{-- Test Results Panel (I/O Display) --}}
                    <template x-if="hasRun">
                        <div class="mt-4 p-4 rounded-panel border text-xs font-mono space-y-3" :class="passed ? 'bg-pass/10 border-pass/40' : 'bg-brand/10 border-brand/40'">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-sm" :class="passed ? 'text-pass' : 'text-brand'">
                                    <span x-text="passed ? '✓ Seluruh Test Cases Lulus!' : '✕ Beberapa Test Case Belum Sesuai'"></span>
                                    (<span x-text="passedCount"></span>/<span x-text="totalCount"></span> Lulus)
                                </span>
                                <span class="text-ink-muted" x-text="`${Math.round(durationMs)} ms`"></span>
                            </div>

                            <div class="space-y-2.5 pt-2 border-t border-rule/40">
                                <template x-for="(res, idx) in testResults" :key="idx">
                                    <div class="p-3 rounded bg-paper/90 border border-rule/50 space-y-1.5 shadow-xs">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-ink flex items-center gap-2">
                                                <span>Kasus #<span x-text="idx + 1"></span></span>
                                                <span class="text-[10px] px-1.5 py-0.5 rounded font-mono" :class="res.passed ? 'bg-pass/20 text-pass font-bold' : 'bg-brand/20 text-brand font-bold'" x-text="res.passed ? 'LULUS' : 'GAGAL'"></span>
                                            </span>
                                            <span class="text-[10px] text-ink-muted" x-text="res.matchedVia ? `Metode: ${res.matchedVia}` : ''"></span>
                                        </div>
                                        <div class="text-[11px] text-ink-muted">
                                            <strong class="text-ink">Input:</strong> <code class="text-ink bg-sheet px-1.5 py-0.5 rounded border border-rule/60" x-text="formatValue(res.input)"></code>
                                        </div>
                                        <div class="text-[11px] text-ink-muted">
                                            <strong class="text-ink">Ekspektasi Output:</strong> <code class="text-pass font-bold bg-sheet px-1.5 py-0.5 rounded border border-pass/30" x-text="formatValue(res.expected)"></code>
                                        </div>
                                        <div class="text-[11px] text-ink-muted">
                                            <strong class="text-ink">Hasil Output Kodemu:</strong> <code :class="res.passed ? 'text-pass font-semibold bg-sheet' : 'text-brand font-semibold bg-sheet'" class="px-1.5 py-0.5 rounded border border-rule/60" x-text="res.error ? `Error: ${res.error}` : formatValue(res.actual)"></code>
                                        </div>
                                        <template x-if="res.logs && res.logs.length > 0">
                                            <div class="text-[11px] text-ink-muted pt-1 border-t border-rule/30">
                                                <strong class="text-ink">Output Konsol (stdout):</strong>
                                                <pre class="mt-1 p-2 rounded bg-ink text-sheet text-[11px] overflow-x-auto leading-relaxed" x-text="res.logs.join('\n')"></pre>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            @empty
                <div class="bg-sheet border border-rule rounded-panel p-8 text-center space-y-3">
                    <p class="text-ink-muted text-sm">Belum ada tugas coding untuk level ini.</p>
                </div>
            @endforelse
        </div>

        {{-- Bottom Navigation --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-rule">
            <a href="{{ route('roadmap.quiz', $page->slug) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-sheet border border-rule text-ink text-sm font-medium rounded hover:bg-paper transition-colors w-full sm:w-auto justify-center">
                ← Kembali ke Kuis Pemahaman
            </a>

            @if($nextLevel)
                @if($nextLevel->isUnlockedFor(auth()->user()))
                    <a href="{{ route('roadmap.level', $nextLevel->slug) }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-brand text-white text-sm font-semibold rounded hover:bg-brand-deep transition-colors w-full sm:w-auto justify-center" style="color: #ffffff !important; background-color: #c92a2a !important;">
                        Lanjut ke {{ $nextLevel->title }} →
                    </a>
                @else
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-paper border border-rule text-ink-muted text-xs sm:text-sm font-medium rounded cursor-not-allowed text-center">
                        🔒 Selesaikan seluruh kuis dan tugas untuk membuka {{ $nextLevel->title }}
                    </span>
                @endif
            @else
                <a href="{{ route('roadmap.index') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-pass text-white text-sm font-semibold rounded hover:bg-pass/90 transition-colors w-full sm:w-auto justify-center" style="color: #ffffff !important;">
                    ✓ Selamat! Seluruh Roadmap Selesai
                </a>
            @endif
        </div>

    </div>
</div>

<script>
// Flexible In-Browser I/O Runner Engine
function compareOutput(actual, expected, logs = [], onMatched = null) {
    // 1. Direct strict match
    if (actual === expected) {
        if (onMatched) onMatched('Exact Return');
        return true;
    }

    // 2. Flexible value matching (numeric, boolean, trimmed string, deep object)
    if (flexibleMatch(actual, expected)) {
        if (onMatched) onMatched('Flexible Value Match');
        return true;
    }

    // 3. Stdout / Console.log capture matching (when student used console.log instead of return)
    if (logs && logs.length > 0) {
        const fullStdout = logs.join('\n').trim();
        const lastLine = logs[logs.length - 1].trim();

        if (flexibleMatch(fullStdout, expected)) {
            if (onMatched) onMatched('Console Stdout');
            return true;
        }

        if (flexibleMatch(lastLine, expected)) {
            if (onMatched) onMatched('Console Stdout');
            return true;
        }

        try {
            const parsedLast = JSON.parse(lastLine);
            if (flexibleMatch(parsedLast, expected)) {
                if (onMatched) onMatched('Console Stdout JSON');
                return true;
            }
        } catch (e) {}

        try {
            const parsedAll = JSON.parse(fullStdout);
            if (flexibleMatch(parsedAll, expected)) {
                if (onMatched) onMatched('Console Stdout JSON');
                return true;
            }
        } catch (e) {}
    }

    return false;
}

function flexibleMatch(a, b) {
    if (a === b) return true;
    if (a == null && b == null) return true;
    if (a == null || b == null) return false;

    // Boolean match (true == "true", false == "false")
    if (typeof a === 'boolean' || typeof b === 'boolean') {
        return String(a).trim().toLowerCase() === String(b).trim().toLowerCase();
    }

    // Numeric match (e.g. 90000 == "90000", float precision tolerances)
    const numA = Number(a);
    const numB = Number(b);
    if (!isNaN(numA) && !isNaN(numB) && (typeof a === 'number' || typeof b === 'number')) {
        return Math.abs(numA - numB) < 1e-6;
    }

    // Trimmed string comparison
    if (typeof a === 'string' && typeof b === 'string') {
        if (a.trim() === b.trim()) return true;
    }

    // Deep array/object comparison
    if (typeof a === 'object' && typeof b === 'object') {
        return deepEqual(a, b);
    }

    // Fallback trimmed string equivalence
    return String(a).trim() === String(b).trim();
}

function deepEqual(a, b) {
    if (a === b) return true;
    if (a == null || b == null) return false;
    if (typeof a !== 'object' || typeof b !== 'object') {
        return flexibleMatch(a, b);
    }

    if (Array.isArray(a) !== Array.isArray(b)) return false;

    if (Array.isArray(a)) {
        if (a.length !== b.length) return false;
        for (let i = 0; i < a.length; i++) {
            if (!deepEqual(a[i], b[i])) return false;
        }
        return true;
    }

    const keysA = Object.keys(a).sort();
    const keysB = Object.keys(b).sort();
    if (keysA.length !== keysB.length) return false;
    for (let i = 0; i < keysA.length; i++) {
        if (keysA[i] !== keysB[i]) return false;
        if (!deepEqual(a[keysA[i]], b[keysB[i]])) return false;
    }
    return true;
}

function codeExerciseRunner(config) {
    return {
        id: config.id,
        slug: config.slug,
        starterCode: config.starterCode,
        code: config.starterCode,
        testCases: config.testCases || [],
        passed: config.isPassed,
        hasRun: false,
        isRunning: false,
        durationMs: 0,
        passedCount: 0,
        totalCount: config.testCases ? config.testCases.length : 0,
        testResults: [],

        formatValue(val) {
            if (val === undefined) return 'undefined';
            if (val === null) return 'null';
            if (typeof val === 'object') {
                try {
                    return JSON.stringify(val);
                } catch (e) {
                    return String(val);
                }
            }
            return String(val);
        },

        insertTab(e) {
            const start = e.target.selectionStart;
            const end = e.target.selectionEnd;
            this.code = this.code.substring(0, start) + '    ' + this.code.substring(end);
            this.$nextTick(() => {
                e.target.selectionStart = e.target.selectionEnd = start + 4;
            });
        },

        resetCode() {
            this.code = this.starterCode;
            this.hasRun = false;
        },

        runTests() {
            this.isRunning = true;
            this.hasRun = true;
            this.testResults = [];
            this.passedCount = 0;

            const t0 = performance.now();

            try {
                // Determine target function name
                const starterFnMatch = this.starterCode ? this.starterCode.match(/function\s+([a-zA-Z0-9_$]+)/) : null;
                let targetFnName = starterFnMatch ? starterFnMatch[1] : null;

                if (!targetFnName || !this.code.includes(targetFnName)) {
                    const userFnMatch = this.code.match(/(?:function\s+|const\s+|let\s+|var\s+)([a-zA-Z0-9_$]+)\s*(?:=|\()/);
                    if (userFnMatch) {
                        targetFnName = userFnMatch[1];
                    }
                }

                for (const tc of this.testCases) {
                    const logs = [];
                    let actual = undefined;
                    let isTcPass = false;
                    let tcError = null;
                    let matchedVia = null;

                    // Sandboxed console to capture standard output
                    const fakeConsole = {
                        log: (...args) => logs.push(args.map(a => typeof a === 'object' ? JSON.stringify(a) : String(a)).join(' ')),
                        info: (...args) => logs.push(args.map(a => typeof a === 'object' ? JSON.stringify(a) : String(a)).join(' ')),
                        warn: (...args) => logs.push(args.map(a => typeof a === 'object' ? JSON.stringify(a) : String(a)).join(' ')),
                        error: (...args) => logs.push(args.map(a => typeof a === 'object' ? JSON.stringify(a) : String(a)).join(' '))
                    };

                    try {
                        const inputArgs = Array.isArray(tc.input) ? tc.input : [tc.input];

                        // Build and execute code
                        const fnBody = `
                            ${this.code}

                            if (typeof ${targetFnName} === 'function') {
                                return ${targetFnName}(...__inputArgs);
                            }
                            return undefined;
                        `;
                        const runner = new Function('console', '__inputArgs', fnBody);

                        actual = runner(fakeConsole, inputArgs);
                        isTcPass = compareOutput(actual, tc.expected, logs, (via) => { matchedVia = via; });
                    } catch (err) {
                        tcError = err.message;
                    }

                    if (isTcPass) {
                        this.passedCount++;
                    }

                    let displayActual = actual;
                    if (displayActual === undefined && logs.length > 0) {
                        displayActual = logs.join('\n');
                    }

                    this.testResults.push({
                        input: tc.input,
                        expected: tc.expected,
                        actual: displayActual,
                        logs: logs,
                        passed: isTcPass,
                        matchedVia: matchedVia,
                        error: tcError
                    });
                }

                const allPassed = (this.passedCount === this.totalCount && this.totalCount > 0);
                this.passed = allPassed;
                this.durationMs = performance.now() - t0;

                // Send result to backend
                fetch(`/belajar/exercise/${this.id}/submit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken
                    },
                    body: JSON.stringify({
                        code: this.code,
                        passed: allPassed,
                        duration_ms: Math.round(this.durationMs),
                        results: this.testResults
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.passed) {
                        if (typeof Livewire !== 'undefined') {
                            Livewire.dispatch('exercise-passed', { exerciseId: this.id });
                        }
                    }
                })
                .catch(() => {});

            } catch (err) {
                this.passed = false;
                this.durationMs = performance.now() - t0;
                this.testResults.push({
                    input: 'Eksekusi Kode',
                    expected: 'Kode JavaScript valid',
                    actual: null,
                    logs: [],
                    passed: false,
                    matchedVia: null,
                    error: err.message
                });
            } finally {
                this.isRunning = false;
            }
        }
    };
}
</script>
