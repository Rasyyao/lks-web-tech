{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Interactive Level Learning Page (/belajar/{level}) --}}
{{-- Includes: Markdown sections, Checkpoints, In-browser Code Runner, Visualizations, Heartbeat pulse --}}
<div class="py-6 sm:py-8" x-data="levelHeartbeat('{{ $page->slug }}')">
    <div class="mx-auto max-w-7xl xl:max-w-[1400px] px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Breadcrumb & Back --}}
        <div class="flex items-center justify-between text-xs text-ink-muted">
            <nav class="flex items-center gap-1.5" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}" class="hover:text-ink">Beranda</a>
                <span>/</span>
                <a href="{{ route('roadmap.index') }}" class="hover:text-ink">Roadmap Client</a>
                <span>/</span>
                <span class="text-ink font-semibold">{{ $page->title }}</span>
            </nav>

            <a href="{{ route('roadmap.index') }}" class="inline-flex items-center gap-1 text-xs text-brand-deep hover:underline">
                ← Kembali ke Daftar Level
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
                        @if($page->estimated_time)
                            <span class="text-xs bg-paper border border-rule text-ink-muted px-2 py-0.5 rounded font-mono">
                                ⏱ {{ $page->estimated_time }}
                            </span>
                        @endif
                        @if($progress->is_completed)
                            <span class="text-xs bg-pass/10 text-pass border border-pass/30 px-2.5 py-0.5 rounded font-semibold inline-flex items-center gap-1">
                                ✓ Level Tuntas
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight">
                        {{ $page->title }}
                    </h1>

                    @if($page->goal)
                        <div class="text-sm text-ink-muted">
                            <strong class="text-ink">Tujuan:</strong> {{ $page->goal }}
                        </div>
                    @endif
                </div>

                {{-- Completion Bar --}}
                <div class="bg-paper border border-rule rounded-panel p-4 min-w-[240px] text-right">
                    <div class="flex justify-between items-baseline mb-1">
                        <span class="text-xs text-ink-muted font-medium uppercase">Tingkat Pemahaman</span>
                        <span class="text-sm font-bold font-mono {{ $progress->is_completed ? 'text-pass' : 'text-brand-deep' }}">{{ $progress->percent_complete }}%</span>
                    </div>
                    <div class="w-full bg-rule h-2 rounded-full overflow-hidden mb-2">
                        <div class="{{ $progress->is_completed ? 'bg-pass' : 'bg-brand' }} h-full transition-all duration-300" style="width: {{ $progress->percent_complete }}%"></div>
                    </div>
                    <div class="text-xs text-ink-muted text-left">
                        {{ $progress->checkpoints_marked }}/{{ $progress->checkpoints_total }} cek dipahami • {{ $progress->exercises_passed }}/{{ $progress->exercises_total }} latihan
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Grid: Content (Left) + Table of Contents / Progress Tracker (Right) --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            {{-- Main Content Column --}}
            <div class="lg:col-span-3 space-y-6">

                {{-- Sections --}}
                @foreach($page->sections as $section)
                    @php
                        $isRead = in_array($section->slug, $readSectionSlugs, true);
                    @endphp
                    <section id="sec-{{ $section->slug }}" class="bg-sheet border border-rule rounded-panel p-6 shadow-sm space-y-4 scroll-mt-20">
                        <div class="flex items-center justify-between border-b border-rule pb-3">
                            <h2 class="text-lg sm:text-xl font-bold text-ink">
                                {{ $section->title }}
                            </h2>

                            @if($isRead)
                                <span class="inline-flex items-center gap-1 text-xs text-pass font-medium">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Sudah Dibaca
                                </span>
                            @else
                                <button
                                    type="button"
                                    wire:click="markSectionRead('{{ $section->slug }}')"
                                    class="text-xs text-brand-deep hover:underline"
                                >
                                    Tandai Selesai Dibaca ✓
                                </button>
                            @endif
                        </div>

                        {{-- HTML Markdown Body --}}
                        <div class="prose max-w-none text-ink text-sm sm:text-base leading-relaxed break-words space-y-3">
                            {!! $section->body_html !!}
                        </div>
                    </section>
                @endforeach

                {{-- Interactive Concept Visualizations --}}
                @if($page->slug === 'level-5')
                    {{-- Level 5 SVG Geometry Playground --}}
                    <div class="bg-sheet border border-brand/30 rounded-panel p-6 shadow-sm space-y-4" x-data="svgPlayground()">
                        <div class="flex items-center justify-between border-b border-rule pb-2">
                            <h3 class="text-base sm:text-lg font-bold text-ink flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-brand"></span>
                                Visualisasi Interaktif: Geometri Garis & Vektor Normal SVG
                            </h3>
                            <span class="text-xs text-ink-muted">Tarik titik P1 dan P2</span>
                        </div>
                        <p class="text-xs text-ink-muted">
                            Uji rumus jarak Euclidean, titik tengah, dan pembuatan 3 garis paralel moda transportasi dengan pergeseran vektor normal tegak lurus.
                        </p>

                        <div class="bg-paper border border-rule rounded p-2 flex flex-col items-center">
                            <svg class="w-full h-64 bg-white border border-rule rounded cursor-crosshair" @mousedown="startDrag($event)" @mousemove="onDrag($event)" @mouseup="stopDrag()">
                                {{-- Parallel lines --}}
                                <template x-for="(line, idx) in parallelLines" :key="idx">
                                    <line :x1="line.x1" :y1="line.y1" :x2="line.x2" :y2="line.y2" :stroke="line.color" stroke-width="3" stroke-linecap="round"></line>
                                </template>

                                {{-- Midpoint label --}}
                                <text :x="midX" :y="midY - 8" text-anchor="middle" font-size="11" font-weight="bold" fill="#23272D" stroke="white" stroke-width="3" paint-order="stroke" x-text="distanceText"></text>

                                {{-- Handles --}}
                                <circle :cx="p1.x" :cy="p1.y" r="8" fill="#E31E24" stroke="white" stroke-width="2" class="cursor-pointer"></circle>
                                <text :x="p1.x" :y="p1.y + 20" text-anchor="middle" font-size="11" fill="#23272D" font-weight="bold">P1</text>

                                <circle :cx="p2.x" :cy="p2.y" r="8" fill="#17704A" stroke="white" stroke-width="2" class="cursor-pointer"></circle>
                                <text :x="p2.x" :y="p2.y + 20" text-anchor="middle" font-size="11" fill="#23272D" font-weight="bold">P2</text>
                            </svg>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs font-mono bg-paper p-3 rounded border border-rule">
                            <div>P1: <strong class="text-ink" x-text="`(${Math.round(p1.x)}, ${Math.round(p1.y)})`"></strong></div>
                            <div>P2: <strong class="text-ink" x-text="`(${Math.round(p2.x)}, ${Math.round(p2.y)})`"></strong></div>
                            <div>Jarak: <strong class="text-brand-deep" x-text="distanceValue"></strong></div>
                            <div>Titik Tengah: <strong class="text-pass" x-text="`(${Math.round(midX)}, ${Math.round(midY)})`"></strong></div>
                        </div>
                    </div>
                @endif

                @if($page->slug === 'level-6')
                    {{-- Level 6 Coordinate Converter Playground --}}
                    <div class="bg-sheet border border-brand/30 rounded-panel p-6 shadow-sm space-y-4" x-data="coordinatePlayground()">
                        <div class="flex items-center justify-between border-b border-rule pb-2">
                            <h3 class="text-base sm:text-lg font-bold text-ink flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-brand"></span>
                                Visualisasi Interaktif: Transform Layar vs Koordinat Peta
                            </h3>
                            <span class="text-xs text-ink-muted">Klik pada area kotak</span>
                        </div>
                        <p class="text-xs text-ink-muted">
                            Simulasi rumus: <code>mx = (sx - tx) / s</code>. Klik di mana saja dalam jendela untuk melihat konversi koordinat layar ke koordinat peta.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-paper p-3 rounded border border-rule text-xs font-mono">
                            <label class="flex flex-col gap-1">
                                <span>Pan TX (px): <strong x-text="tx"></strong></span>
                                <input type="range" min="-300" max="300" x-model.number="tx" class="w-full">
                            </label>
                            <label class="flex flex-col gap-1">
                                <span>Pan TY (px): <strong x-text="ty"></strong></span>
                                <input type="range" min="-300" max="300" x-model.number="ty" class="w-full">
                            </label>
                            <label class="flex flex-col gap-1">
                                <span>Skala Zoom (s): <strong x-text="s.toFixed(2)"></strong></span>
                                <input type="range" min="0.5" max="3" step="0.1" x-model.number="s" class="w-full">
                            </label>
                        </div>

                        <div
                            class="relative w-full h-64 bg-paper border border-rule rounded overflow-hidden cursor-crosshair select-none"
                            @click="handleClick($event)"
                        >
                            {{-- Transformed Map Layer --}}
                            <div
                                class="absolute inset-0 origin-top-left border border-dashed border-brand/40 bg-white"
                                :style="`transform: translate(${tx}px, ${ty}px) scale(${s}); transform-origin: 0 0; width: 800px; height: 500px;`"
                            >
                                <div class="p-2 text-xs font-mono text-ink-muted">Area Peta (800 x 500 px)</div>
                                <template x-for="(dot, i) in dots" :key="i">
                                    <div
                                        class="absolute w-3 h-3 bg-brand rounded-full -translate-x-1/2 -translate-y-1/2 border border-white"
                                        :style="`left: ${dot.mx}px; top: ${dot.my}px;`"
                                    ></div>
                                </template>
                            </div>
                        </div>

                        <div class="text-xs font-mono bg-paper p-3 rounded border border-rule flex justify-between">
                            <div>Klik Terakhir Layar (sx, sy): <strong class="text-ink" x-text="lastClick ? `(${lastClick.sx}, ${lastClick.sy})` : '-'"></strong></div>
                            <div>Koordinat Peta (mx, my): <strong class="text-brand-deep" x-text="lastClick ? `(${lastClick.mx}, ${lastClick.my})` : '-'"></strong></div>
                        </div>
                    </div>
                @endif

                @if($page->slug === 'level-7')
                    {{-- Level 7 BFS vs DFS Traversal Stepper --}}
                    <div class="bg-sheet border border-brand/30 rounded-panel p-6 shadow-sm space-y-4" x-data="graphStepper()">
                        <div class="flex items-center justify-between border-b border-rule pb-2">
                            <h3 class="text-base sm:text-lg font-bold text-ink flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-brand"></span>
                                Visualisasi Interaktif: Stepper BFS vs DFS Backtracking
                            </h3>
                            <div class="flex gap-2">
                                <button type="button" @click="setMode('BFS')" :class="mode === 'BFS' ? 'bg-brand text-white' : 'bg-paper text-ink'" class="px-2.5 py-1 text-xs font-bold rounded border border-rule transition-colors">
                                    BFS (Lapis per Lapis)
                                </button>
                                <button type="button" @click="setMode('DFS')" :class="mode === 'DFS' ? 'bg-brand text-white' : 'bg-paper text-ink'" class="px-2.5 py-1 text-xs font-bold rounded border border-rule transition-colors">
                                    DFS (Semua Jalur + Backtrack)
                                </button>
                            </div>
                        </div>

                        <p class="text-xs text-ink-muted">
                            Telusuri bagaimana algoritma mencari rute dari <strong>A</strong> ke <strong>D</strong> pada graph 4 simpul.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Graph Canvas --}}
                            <div class="bg-paper border border-rule rounded p-4 flex flex-col items-center justify-center">
                                <svg class="w-64 h-56">
                                    {{-- Edges: A-B, A-C, B-C, B-D, C-D --}}
                                    <line x1="50" y1="50" x2="200" y2="50" stroke="#D5D8DD" stroke-width="3"></line>
                                    <line x1="50" y1="50" x2="50" y2="180" stroke="#D5D8DD" stroke-width="3"></line>
                                    <line x1="200" y1="50" x2="50" y2="180" stroke="#D5D8DD" stroke-width="3"></line>
                                    <line x1="200" y1="50" x2="200" y2="180" stroke="#D5D8DD" stroke-width="3"></line>
                                    <line x1="50" y1="180" x2="200" y2="180" stroke="#D5D8DD" stroke-width="3"></line>

                                    {{-- Nodes --}}
                                    <g>
                                        <circle cx="50" cy="50" r="18" :fill="activeNodes.includes('A') ? '#E31E24' : '#FFFFFF'" stroke="#23272D" stroke-width="2"></circle>
                                        <text x="50" y="55" text-anchor="middle" font-weight="bold" :fill="activeNodes.includes('A') ? '#FFF' : '#23272D'">A</text>

                                        <circle cx="200" cy="50" r="18" :fill="activeNodes.includes('B') ? '#E31E24' : '#FFFFFF'" stroke="#23272D" stroke-width="2"></circle>
                                        <text x="200" y="55" text-anchor="middle" font-weight="bold" :fill="activeNodes.includes('B') ? '#FFF' : '#23272D'">B</text>

                                        <circle cx="50" cy="180" r="18" :fill="activeNodes.includes('C') ? '#E31E24' : '#FFFFFF'" stroke="#23272D" stroke-width="2"></circle>
                                        <text x="50" y="185" text-anchor="middle" font-weight="bold" :fill="activeNodes.includes('C') ? '#FFF' : '#23272D'">C</text>

                                        <circle cx="200" cy="180" r="18" :fill="activeNodes.includes('D') ? '#17704A' : '#FFFFFF'" stroke="#23272D" stroke-width="2"></circle>
                                        <text x="200" y="185" text-anchor="middle" font-weight="bold" :fill="activeNodes.includes('D') ? '#FFF' : '#23272D'">D</text>
                                    </g>
                                </svg>
                            </div>

                            {{-- Steps and Controls --}}
                            <div class="space-y-3">
                                <div class="bg-paper p-3 rounded border border-rule space-y-1 text-xs">
                                    <div class="font-bold text-ink" x-text="stepTitle"></div>
                                    <p class="text-ink-muted" x-text="stepDesc"></p>
                                </div>

                                <div class="bg-sheet p-3 rounded border border-rule text-xs font-mono space-y-1">
                                    <div>Struktur Data: <strong class="text-brand-deep" x-text="dataStructureLabel"></strong></div>
                                    <div class="text-ink" x-text="dataStructureValue"></div>
                                    <div class="pt-1">Jalur Ditemukan: <strong class="text-pass" x-text="pathsFoundText"></strong></div>
                                </div>

                                <div class="flex items-center gap-2 pt-1">
                                    <button type="button" @click="prevStep()" :disabled="stepIndex === 0" class="px-3 py-1.5 bg-sheet border border-rule text-ink text-xs font-medium rounded hover:bg-paper disabled:opacity-40">
                                        ← Mundur
                                    </button>
                                    <button type="button" @click="nextStep()" :disabled="stepIndex >= maxSteps - 1" class="px-3 py-1.5 bg-brand text-white text-xs font-medium rounded hover:bg-brand-deep disabled:opacity-40">
                                        Langkah Selanjutnya →
                                    </button>
                                    <button type="button" @click="resetSteps()" class="px-2 py-1.5 text-xs text-ink-muted hover:underline ml-auto">
                                        Ulang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Interactive Coding Exercises (LeetCode Style in Browser) --}}
                @if($page->exercises->isNotEmpty())
                    <div class="space-y-4 pt-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg sm:text-xl font-bold text-ink">
                                Latihan Coding Interaktif
                            </h2>
                            <span class="text-xs text-ink-muted">Dijalankan dan diuji langsung di browser</span>
                        </div>

                        @foreach($page->exercises as $exercise)
                            @php
                                $isPassed = in_array($exercise->slug, $passedExerciseSlugs, true);
                            @endphp
                            <div
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
                                        <h3 class="text-base sm:text-lg font-bold text-ink">
                                            {{ $exercise->title }}
                                        </h3>
                                    </div>
                                    <div>
                                        <template x-if="passed">
                                            <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-pass/10 text-pass border border-pass/30 inline-flex items-center gap-1">
                                                ✓ Lulus Tes
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
                                <div class="text-sm text-ink leading-relaxed whitespace-pre-line bg-paper/60 p-3 rounded border border-rule/60">
                                    {{ $exercise->instructions }}
                                </div>

                                {{-- Code Editor Textarea --}}
                                <div class="space-y-1">
                                    <div class="flex justify-between items-center text-xs text-ink-muted font-mono">
                                        <span>Editor JavaScript:</span>
                                        <span>Tekan tombol jalankan untuk menguji</span>
                                    </div>
                                    <textarea
                                        x-model="code"
                                        rows="8"
                                        class="w-full font-mono text-xs sm:text-sm bg-ink text-sheet p-3 rounded border border-rule focus:outline-none focus:ring-1 focus:ring-brand leading-relaxed"
                                        spellcheck="false"
                                        @keydown.tab.prevent="insertTab($event)"
                                    ></textarea>
                                </div>

                                {{-- Action Bar --}}
                                <div class="flex items-center justify-between gap-3 pt-1">
                                    <button
                                        type="button"
                                        @click="runTests()"
                                        :disabled="isRunning"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white text-xs sm:text-sm font-semibold rounded hover:bg-brand-deep disabled:opacity-50 transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                                    >
                                        <span x-show="!isRunning">▶ Jalankan Kode & Uji Tes</span>
                                        <span x-show="isRunning">Menguji...</span>
                                    </button>

                                    <button
                                        type="button"
                                        @click="resetCode()"
                                        class="text-xs text-ink-muted hover:underline"
                                    >
                                        Reset ke Kode Awal
                                    </button>
                                </div>

                                {{-- Results Box --}}
                                <template x-if="hasRun">
                                    <div class="border rounded p-4 text-xs font-mono space-y-3" :class="passed ? 'bg-pass/5 border-pass/30' : 'bg-tint/40 border-brand-deep/20'">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-sm" :class="passed ? 'text-pass' : 'text-brand-deep'" x-text="passed ? '✓ SEMUA TES LULUS!' : '✗ SEBAGIAN TES GAGAL'"></span>
                                            <span class="text-ink-muted" x-text="`${passedCount}/${totalCount} tes lolos (${durationMs}ms)`"></span>
                                        </div>

                                        <div class="space-y-2 max-h-48 overflow-y-auto">
                                            <template x-for="(test, i) in testResults" :key="i">
                                                <div class="p-2 rounded border bg-sheet" :class="test.passed ? 'border-pass/30' : 'border-brand-deep/30'">
                                                    <div class="flex items-center justify-between font-bold">
                                                        <span :class="test.passed ? 'text-pass' : 'text-brand-deep'" x-text="`Tes #${i + 1}: ${test.passed ? 'Lulus' : 'Gagal'}`"></span>
                                                    </div>
                                                    <div class="text-ink-muted mt-1">Input: <span class="text-ink" x-text="JSON.stringify(test.input)"></span></div>
                                                    <div class="text-ink-muted">Ekspektasi: <span class="text-pass" x-text="JSON.stringify(test.expected)"></span></div>
                                                    <template x-if="!test.passed">
                                                        <div class="text-brand-deep">Hasil Kamu: <span x-text="JSON.stringify(test.actual)"></span></div>
                                                    </template>
                                                    <template x-if="test.error">
                                                        <div class="text-brand-deep font-semibold mt-1" x-text="test.error"></div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Checkpoints (Cek Pemahaman) --}}
                @if($page->checkpoints->isNotEmpty())
                    <section id="checkpoints" class="bg-sheet border border-rule rounded-panel p-6 shadow-sm space-y-4">
                        <div class="border-b border-rule pb-3">
                            <h2 class="text-lg sm:text-xl font-bold text-ink">
                                Cek Pemahaman Konsep
                            </h2>
                            <p class="text-xs sm:text-sm text-ink-muted">
                                Centang pertanyaan di bawah ini bila kamu sudah dapat menjawabnya dan memahaminya <strong>tanpa melihat catatan</strong>.
                            </p>
                        </div>

                        <div class="space-y-3">
                            @foreach($page->checkpoints as $cp)
                                @php
                                    $isUnderstood = in_array($cp->slug, $markedCheckpointSlugs, true);
                                @endphp
                                <label class="flex items-start gap-3 p-3 rounded border {{ $isUnderstood ? 'bg-pass/5 border-pass/30' : 'bg-paper border-rule' }} hover:border-brand/40 cursor-pointer transition-colors">
                                    <input
                                        type="checkbox"
                                        class="mt-1 w-4 h-4 text-brand rounded border-rule focus:ring-brand cursor-pointer"
                                        {{ $isUnderstood ? 'checked' : '' }}
                                        wire:click="toggleCheckpoint('{{ $cp->slug }}')"
                                    >
                                    <div class="text-sm leading-relaxed {{ $isUnderstood ? 'text-ink font-medium' : 'text-ink-muted' }}">
                                        {{ $cp->prompt }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Bottom Navigation (Prev / Next) --}}
                <div class="flex items-center justify-between pt-4 border-t border-rule">
                    @if($prevLevel)
                        <a href="{{ route('roadmap.level', $prevLevel->slug) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sheet border border-rule text-ink text-sm font-medium rounded hover:bg-paper transition-colors">
                            ← {{ $prevLevel->title }}
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if($nextLevel)
                        <a href="{{ route('roadmap.level', $nextLevel->slug) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand text-white text-sm font-medium rounded hover:bg-brand-deep transition-colors">
                            {{ $nextLevel->title }} →
                        </a>
                    @endif
                </div>

            </div>

            {{-- Sidebar Column: Table of Contents & Quick Jumper --}}
            <div class="space-y-4">
                <div class="bg-sheet border border-rule rounded-panel p-4 shadow-sm sticky top-6 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-ink-muted border-b border-rule pb-2">
                        Daftar Isi Level Ini
                    </h3>

                    <nav class="space-y-1 text-xs" aria-label="Daftar Isi Bagian">
                        @foreach($page->sections as $sec)
                            @php
                                $secRead = in_array($sec->slug, $readSectionSlugs, true);
                            @endphp
                            <a href="#sec-{{ $sec->slug }}" class="flex items-center justify-between py-1.5 px-2 rounded hover:bg-paper transition-colors text-ink">
                                <span class="truncate">{{ $sec->title }}</span>
                                <span class="font-mono text-[10px] {{ $secRead ? 'text-pass font-bold' : 'text-ink-muted' }}">
                                    {{ $secRead ? '✓' : '—' }}
                                </span>
                            </a>
                        @endforeach

                        @if($page->checkpoints->isNotEmpty())
                            <a href="#checkpoints" class="flex items-center justify-between py-1.5 px-2 rounded hover:bg-paper transition-colors text-ink font-medium border-t border-rule mt-2 pt-2">
                                <span>Cek Pemahaman</span>
                                <span class="font-mono text-brand-deep font-bold">
                                    {{ count($markedCheckpointSlugs) }}/{{ $page->checkpoints->count() }}
                                </span>
                            </a>
                        @endif
                    </nav>

                    {{-- Pulse / Active Status Badge --}}
                    <div class="pt-3 border-t border-rule text-[11px] text-ink-muted flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full" :class="isHeartbeatActive ? 'bg-pass animate-pulse' : 'bg-rule'"></span>
                        <span x-text="isHeartbeatActive ? 'Sesi belajar aktif tercatat' : 'Sesi belajar dijeda'"></span>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

{{-- Client Scripts for Heartbeat, Code Runner & Visualizations --}}
<script>
function levelHeartbeat(levelSlug) {
    return {
        isHeartbeatActive: true,
        tabId: null,
        intervalId: null,

        init() {
            // Get or create unique tab ID
            this.tabId = sessionStorage.getItem('lks_tab_id');
            if (!this.tabId) {
                this.tabId = 'tab_' + Math.random().toString(36).substring(2, 12);
                sessionStorage.setItem('lks_tab_id', this.tabId);
            }

            this.pulse();
            this.intervalId = setInterval(() => {
                if (document.visibilityState === 'visible') {
                    this.pulse();
                } else {
                    this.isHeartbeatActive = false;
                }
            }, 15000);

            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    this.pulse();
                } else {
                    this.isHeartbeatActive = false;
                }
            });
        },

        pulse() {
            fetch('/aktivitas/heartbeat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ tab_id: this.tabId })
            })
            .then(res => res.json())
            .then(data => {
                this.isHeartbeatActive = data.recorded;
            })
            .catch(() => {
                this.isHeartbeatActive = false;
            });
        }
    };
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

        insertTab(e) {
            const start = e.target.selectionStart;
            const end = e.target.selectionEnd;
            this.code = this.code.substring(0, start) + '    ' + this.code.substring(end);
            this.$nextTick(() => {
                e.target.selectionStart = e.target.selectionEnd = start + 4;
            });
        },

        resetCode() {
            if (confirm('Kembalikan ke kode awal?')) {
                this.code = this.starterCode;
                this.hasRun = false;
            }
        },

        runTests() {
            this.isRunning = true;
            this.hasRun = true;
            this.testResults = [];
            this.passedCount = 0;

            const t0 = performance.now();

            try {
                // Compile the student's code inside a Function scope
                // Extracts the defined function and runs test cases
                const userFuncFactory = new Function(`${this.code};
                    return {
                        formatDurasi: typeof formatDurasi !== 'undefined' ? formatDurasi : null,
                        formatRupiah: typeof formatRupiah !== 'undefined' ? formatRupiah : null,
                        hitungJarak: typeof hitungJarak !== 'undefined' ? hitungJarak : null,
                        hitungTitikTengah: typeof hitungTitikTengah !== 'undefined' ? hitungTitikTengah : null,
                        layarKePeta: typeof layarKePeta !== 'undefined' ? layarKePeta : null,
                        zoomTx: typeof zoomTx !== 'undefined' ? zoomTx : null,
                        bfs: typeof bfs !== 'undefined' ? bfs : null,
                        dfsSemuaJalur: typeof dfsSemuaJalur !== 'undefined' ? dfsSemuaJalur : null,
                    };
                `);

                const userExports = userFuncFactory();
                // Find first valid exported function
                const activeFn = Object.values(userExports).find(fn => typeof fn === 'function');

                if (!activeFn) {
                    throw new Error('Fungsi yang diminta belum didefinisikan dengan benar dalam kode kamu.');
                }

                for (const tc of this.testCases) {
                    let actual;
                    let isTcPass = false;
                    let tcError = null;

                    try {
                        actual = activeFn(...tc.input);
                        isTcPass = JSON.stringify(actual) === JSON.stringify(tc.expected);
                    } catch (err) {
                        tcError = err.message;
                    }

                    if (isTcPass) {
                        this.passedCount++;
                    }

                    this.testResults.push({
                        input: tc.input,
                        expected: tc.expected,
                        actual: actual,
                        passed: isTcPass,
                        error: tcError
                    });
                }

                this.passed = (this.passedCount === this.totalCount && this.totalCount > 0);
            } catch (compileErr) {
                this.passed = false;
                this.testResults = [{
                    input: 'Kompilasi',
                    expected: 'Berhasil dijalankan',
                    actual: 'Error Sintaks',
                    passed: false,
                    error: compileErr.message
                }];
            }

            this.durationMs = Math.round(performance.now() - t0);
            this.isRunning = false;

            // Submit attempt to server
            fetch(`/belajar/latihan/${this.id}/percobaan`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken
                },
                body: JSON.stringify({
                    code: this.code,
                    passed: this.passed,
                    results: this.testResults,
                    duration_ms: this.durationMs
                })
            }).catch(() => {});
        }
    };
}

function svgPlayground() {
    return {
        p1: { x: 80, y: 150 },
        p2: { x: 300, y: 100 },
        dragging: null,

        startDrag(e) {
            const rect = e.target.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            if (Math.hypot(x - this.p1.x, y - this.p1.y) < 20) this.dragging = 'p1';
            else if (Math.hypot(x - this.p2.x, y - this.p2.y) < 20) this.dragging = 'p2';
        },

        onDrag(e) {
            if (!this.dragging) return;
            const rect = e.currentTarget.getBoundingClientRect();
            const x = Math.max(20, Math.min(rect.width - 20, e.clientX - rect.left));
            const y = Math.max(20, Math.min(rect.height - 20, e.clientY - rect.top));

            this[this.dragging].x = x;
            this[this.dragging].y = y;
        },

        stopDrag() {
            this.dragging = null;
        },

        get distanceValue() {
            return (Math.hypot(this.p2.x - this.p1.x, this.p2.y - this.p1.y)).toFixed(1);
        },

        get distanceText() {
            return `Jarak: ${this.distanceValue} px`;
        },

        get midX() {
            return (this.p1.x + this.p2.x) / 2;
        },

        get midY() {
            return (this.p1.y + this.p2.y) / 2;
        },

        get parallelLines() {
            const dx = this.p2.x - this.p1.x;
            const dy = this.p2.y - this.p1.y;
            const len = Math.hypot(dx, dy) || 1;
            const nx = -dy / len;
            const ny = dx / len;

            const offsets = [-8, 0, 8];
            const colors = ['#E31E24', '#17704A', '#23272D'];

            return offsets.map((off, i) => ({
                x1: this.p1.x + nx * off,
                y1: this.p1.y + ny * off,
                x2: this.p2.x + nx * off,
                y2: this.p2.y + ny * off,
                color: colors[i]
            }));
        }
    };
}

function coordinatePlayground() {
    return {
        tx: 20,
        ty: 20,
        s: 1.0,
        dots: [],
        lastClick: null,

        handleClick(e) {
            const rect = e.currentTarget.getBoundingClientRect();
            const sx = Math.round(e.clientX - rect.left);
            const sy = Math.round(e.clientY - rect.top);

            const mx = Math.round((sx - this.tx) / this.s);
            const my = Math.round((sy - this.ty) / this.s);

            this.lastClick = { sx, sy, mx, my };
            this.dots.push({ mx, my });
            if (this.dots.length > 5) this.dots.shift();
        }
    };
}

function graphStepper() {
    return {
        mode: 'BFS',
        stepIndex: 0,

        setMode(newMode) {
            this.mode = newMode;
            this.resetSteps();
        },

        get maxSteps() {
            return this.mode === 'BFS' ? 5 : 8;
        },

        resetSteps() {
            this.stepIndex = 0;
        },

        nextStep() {
            if (this.stepIndex < this.maxSteps - 1) this.stepIndex++;
        },

        prevStep() {
            if (this.stepIndex > 0) this.stepIndex--;
        },

        get activeNodes() {
            if (this.mode === 'BFS') {
                const map = [
                    ['A'],
                    ['A', 'B'],
                    ['A', 'B', 'C'],
                    ['A', 'B', 'C', 'D'],
                    ['A', 'B', 'D']
                ];
                return map[this.stepIndex] || ['A'];
            } else {
                const map = [
                    ['A'],
                    ['A', 'B'],
                    ['A', 'B', 'C'],
                    ['A', 'B', 'C', 'D'],
                    ['A', 'B', 'D'],
                    ['A', 'C'],
                    ['A', 'C', 'B', 'D'],
                    ['A', 'C', 'D']
                ];
                return map[this.stepIndex] || ['A'];
            }
        },

        get stepTitle() {
            if (this.mode === 'BFS') {
                const titles = [
                    'Langkah 1: Inisialisasi BFS',
                    'Langkah 2: Periksa Tetangga A',
                    'Langkah 3: Ambil B dari Antrean',
                    'Langkah 4: Capai Tujuan D',
                    'Selesai: Rekonstruksi Jalur Terpendek'
                ];
                return titles[this.stepIndex];
            } else {
                const titles = [
                    'Langkah 1: Mulai dari Simpul A',
                    'Langkah 2: Cabang ke B',
                    'Langkah 3: Cabang ke C',
                    'Langkah 4: Capai D (Jalur 1)',
                    'Langkah 5: Backtrack & Coba B -> D (Jalur 2)',
                    'Langkah 6: Backtrack ke A, Coba Cabang C',
                    'Langkah 7: Jelajah C -> B -> D (Jalur 3)',
                    'Langkah 8: Selesai C -> D (Jalur 4)'
                ];
                return titles[this.stepIndex];
            }
        },

        get stepDesc() {
            if (this.mode === 'BFS') {
                const desc = [
                    'Simpul A dimasukkan ke antrean. Catatan parent: parent[A] = null.',
                    'A diambil dari antrean. Tetangga B dan C dimasukkan ke belakang antrean.',
                    'B diambil dari antrean. Tetangga B adalah C dan D. D adalah tujuan!',
                    'Simpul tujuan D ditemukan. BFS berhenti di lapis terdekat.',
                    'Menelusuri parent dari D -> B -> A menghasilkan jalur terpendek: [A, B, D].'
                ];
                return desc[this.stepIndex];
            } else {
                const desc = [
                    'Path = [A], Visited = {A}. Cari tetangga pertama A yaitu B.',
                    'Path = [A, B], Visited = {A, B}. Tetangga B adalah C.',
                    'Path = [A, B, C], Visited = {A, B, C}. Tetangga C adalah D.',
                    'Capai tujuan D! Jalur 1 ditemukan: [A, B, C, D]. Lakukan Backtrack.',
                    'Keluarkan C dari visited. Coba tetangga D langsung dari B: Jalur 2 [A, B, D].',
                    'Backtrack sampai A. Keluarkan B dari visited. Sekarang coba tetangga A lainnya yaitu C.',
                    'Path = [A, C, B, D]. Jalur 3 ditemukan: [A, C, B, D]. Backtrack.',
                    'Path = [A, C, D]. Jalur 4 ditemukan: [A, C, D]. Seluruh 4 kombinasi jalur lengkap!'
                ];
                return desc[this.stepIndex];
            }
        },

        get dataStructureLabel() {
            return this.mode === 'BFS' ? 'Queue (FIFO Antrean)' : 'Call Stack & Backtrack Set';
        },

        get dataStructureValue() {
            if (this.mode === 'BFS') {
                const q = ['[A]', '[B, C]', '[C, D]', '[D]', 'Empty'];
                return `Antrean: ${q[this.stepIndex]}`;
            } else {
                const s = [
                    'path: [A]',
                    'path: [A, B]',
                    'path: [A, B, C]',
                    'path: [A, B, C, D] -> Rekam Hasil',
                    'backtrack -> path: [A, B, D]',
                    'backtrack -> path: [A, C]',
                    'path: [A, C, B, D] -> Rekam Hasil',
                    'path: [A, C, D] -> Rekam Hasil'
                ];
                return s[this.stepIndex];
            }
        },

        get pathsFoundText() {
            if (this.mode === 'BFS') {
                return this.stepIndex >= 4 ? '1 jalur terpendek (A, B, D)' : '0 jalur';
            } else {
                if (this.stepIndex < 3) return '0 jalur';
                if (this.stepIndex === 3) return '1 jalur: [A, B, C, D]';
                if (this.stepIndex <= 5) return '2 jalur: [A, B, C, D], [A, B, D]';
                if (this.stepIndex === 6) return '3 jalur: [A, B, C, D], [A, B, D], [A, C, B, D]';
                return '4 jalur lengkap!';
            }
        }
    };
}
</script>
