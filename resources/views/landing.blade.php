<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Platform Seleksi & Inkubasi Calon Delegasi LKS Web Technologies SMK Telkom Purwokerto">
    <title>Seleksi LKS Web Technologies — {{ __('general.school_name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-paper text-ink font-sans antialiased flex flex-col selection:bg-brand selection:text-white">
    {{-- Header / Top Navbar --}}
    <header class="bg-sheet border-b border-rule sticky top-0 z-40 shadow-xs border-t-[3px] border-t-brand">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            {{-- Brand Logo --}}
            <a href="/" class="flex items-center gap-3 focus-visible:outline-2 focus-visible:outline-brand-deep">
                @if(file_exists(public_path('brand/logo-smk-telkom-purwokerto.png')))
                    <img
                        src="{{ asset('brand/logo-smk-telkom-purwokerto.png') }}"
                        alt="Logo SMK Telkom Purwokerto"
                        class="h-9 w-auto shrink-0"
                    >
                @endif
                <div class="min-w-0">
                    <span class="text-sm sm:text-base font-bold text-ink block leading-tight">
                        LKS Web Technologies
                    </span>
                    <span class="text-[11px] text-ink-muted block font-medium">
                        {{ __('general.school_name') }}
                    </span>
                </div>
            </a>

            {{-- Navigation Links (Desktop) --}}
            <nav class="hidden md:flex items-center gap-6 text-xs font-semibold text-ink-muted" aria-label="Navigasi Landing Page">
                <a href="#fitur" class="hover:text-brand-deep transition-colors">Fitur Platform</a>
                <a href="#modul" class="hover:text-brand-deep transition-colors">Modul Praktik</a>
                <a href="#roadmap" class="hover:text-brand-deep transition-colors">Roadmap Belajar</a>
                <a href="#alur" class="hover:text-brand-deep transition-colors">Alur Seleksi</a>
            </nav>

            {{-- Portal Login CTA Button --}}
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('login') }}"
                    class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold rounded-cell transition-all duration-150 shadow-xs hover:opacity-95 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                    style="color: #ffffff !important; background-color: #c92a2a !important;"
                >
                    <span>Masuk ke Portal Siswa</span>
                    <span class="ml-1.5">&rarr;</span>
                </a>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 space-y-16 sm:space-y-24 py-10 sm:py-16">
        {{-- ==================== HERO SECTION ==================== --}}
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-6">
                {{-- Badge Announcement --}}
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-tint text-brand-deep border border-brand/20 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-brand animate-pulse"></span>
                    <span>Seleksi LKS 2026 &bull; Standar Tingkat Provinsi &amp; Nasional</span>
                </div>

                {{-- Hero Heading --}}
                <h1 class="text-3xl sm:text-5xl font-extrabold text-ink tracking-tight leading-tight">
                    Platform Seleksi &amp; Inkubasi <br class="hidden sm:inline">
                    <span class="text-brand-deep">LKS Web Technologies</span>
                </h1>

                {{-- Subtitle --}}
                <p class="text-sm sm:text-base text-ink-muted leading-relaxed max-w-2xl mx-auto">
                    Pusat pelatihan terpadu calon delegasi <strong>SMK Telkom Purwokerto</strong>. Dibekali modul kompetisi nyata, kurikulum materi terstruktur dari nol hingga mahir, bank soal resmi, dan evaluasi otomatis berstandar WorldSkills.
                </p>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                    <a
                        href="{{ route('login') }}"
                        class="w-full sm:w-auto px-6 py-3 text-sm font-bold rounded-cell transition-all shadow-sm hover:opacity-95 text-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                        style="color: #ffffff !important; background-color: #c92a2a !important;"
                    >
                        Mulai Seleksi Sekarang &rarr;
                    </a>
                    <a
                        href="#modul"
                        class="w-full sm:w-auto px-6 py-3 text-sm font-semibold rounded-cell bg-sheet text-ink border border-rule hover:bg-paper transition-colors text-center focus-visible:outline-2 focus-visible:outline-brand-deep"
                    >
                        Lihat Modul &amp; Silabus
                    </a>
                </div>

                {{-- Quick Statistics Grid (Matching Site KPI Cards) --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pt-8 text-left">
                    <div class="p-4 bg-sheet rounded-panel border border-rule shadow-xs">
                        <span class="text-[11px] font-semibold text-ink-muted uppercase tracking-wider block">Modul Praktik</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span class="text-2xl font-extrabold text-ink tabular-nums">{{ $totalModulesCount }}</span>
                            <span class="text-xs text-ink-muted">Modul LKS</span>
                        </div>
                        <span class="text-[10px] text-pass font-medium block mt-1">Client &amp; Server Side</span>
                    </div>

                    <div class="p-4 bg-sheet rounded-panel border border-rule shadow-xs">
                        <span class="text-[11px] font-semibold text-ink-muted uppercase tracking-wider block">Roadmap Materi</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span class="text-2xl font-extrabold text-ink tabular-nums">{{ $totalLevelsCount }}</span>
                            <span class="text-xs text-ink-muted">Level</span>
                        </div>
                        <span class="text-[10px] text-brand-deep font-medium block mt-1">Dari Level 0 s/d 8</span>
                    </div>

                    <div class="p-4 bg-sheet rounded-panel border border-rule shadow-xs">
                        <span class="text-[11px] font-semibold text-ink-muted uppercase tracking-wider block">Bank Soal Arsip</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span class="text-2xl font-extrabold text-ink tabular-nums">{{ $totalPapersCount }}</span>
                            <span class="text-xs text-ink-muted">Berkas Soal</span>
                        </div>
                        <span class="text-[10px] text-ink-muted font-medium block mt-1">Kab, Prov, &amp; Nasional</span>
                    </div>

                    <div class="p-4 bg-sheet rounded-panel border border-rule shadow-xs">
                        <span class="text-[11px] font-semibold text-ink-muted uppercase tracking-wider block">Evaluasi Otomatis</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span class="text-2xl font-extrabold text-ink tabular-nums">100%</span>
                            <span class="text-xs text-ink-muted">Otomasi</span>
                        </div>
                        <span class="text-[10px] text-gold-text font-medium block mt-1">Headless &amp; API Tests</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- ==================== CORE FEATURES SECTION ==================== --}}
        <section id="fitur" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="space-y-8">
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-deep">Keunggulan Sistem</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-ink">
                        Dirancang Khusus untuk Standar Kompetisi LKS
                    </h2>
                    <p class="text-xs sm:text-sm text-ink-muted">
                        Ekosistem latihan yang menyatukan seluruh kebutuhan persiapan siswa dan monitoring mentor.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    {{-- Feature 1 --}}
                    <div class="bg-sheet border border-rule rounded-panel p-5 space-y-3 shadow-xs hover:border-brand/40 transition-colors">
                        <div class="w-10 h-10 rounded-cell bg-purple-50 text-purple-700 flex items-center justify-center border border-purple-200">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-ink">Modul Praktik Riil</h3>
                        <p class="text-xs text-ink-muted leading-relaxed">
                            Mengerjakan proyek berstandar nasional mulai dari REST API Laravel Sanctum, SVG Map manipulasi DOM, hingga algoritma DFS pathfinding.
                        </p>
                    </div>

                    {{-- Feature 2 --}}
                    <div class="bg-sheet border border-rule rounded-panel p-5 space-y-3 shadow-xs hover:border-brand/40 transition-colors">
                        <div class="w-10 h-10 rounded-cell bg-brand/10 text-brand-deep flex items-center justify-center border border-brand/20">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-ink">Roadmap Belajar Terkunci</h3>
                        <p class="text-xs text-ink-muted leading-relaxed">
                            Progres belajar bertahap dengan kuis checkpoint interaktif di setiap level untuk memastikan konsep dikuasai sebelum membuka tingkat lanjut.
                        </p>
                    </div>

                    {{-- Feature 3 --}}
                    <div class="bg-sheet border border-rule rounded-panel p-5 space-y-3 shadow-xs hover:border-brand/40 transition-colors">
                        <div class="w-10 h-10 rounded-cell bg-sky-50 text-sky-700 flex items-center justify-center border border-sky-200">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-ink">Bank Soal &amp; Starter Kit</h3>
                        <p class="text-xs text-ink-muted leading-relaxed">
                            Unduh berkas soal resmi LKS, paket aset media, database dump SQL, dan starter kit template untuk latihan mandiri peserta.
                        </p>
                    </div>

                    {{-- Feature 4 --}}
                    <div class="bg-sheet border border-rule rounded-panel p-5 space-y-3 shadow-xs hover:border-brand/40 transition-colors">
                        <div class="w-10 h-10 rounded-cell bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-200">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-ink">Papan Peringkat Live</h3>
                        <p class="text-xs text-ink-muted leading-relaxed">
                            Peringkat kompetitif transparan yang menghitung skor soal, nilai proyek modul dari mentor, dan keaktifan durasi belajar siswa.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ==================== FEATURED MODULES SECTION ==================== --}}
        <section id="modul" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-rule pb-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-deep">Spesifikasi Lomba</span>
                        <h2 class="text-2xl font-bold text-ink mt-0.5">
                            Modul Praktik Seleksi 2026
                        </h2>
                    </div>
                    <a href="{{ route('login') }}" class="text-xs font-bold text-brand-deep hover:underline">
                        Masuk untuk Mengerjakan &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach($modules as $mod)
                        @php
                            $isServer = $mod->track === \App\Enums\ModuleTrack::Server;
                        @endphp
                        <div class="bg-sheet border border-rule rounded-panel p-5 flex flex-col justify-between space-y-4 shadow-sm hover:border-brand/40 transition-colors">
                            <div class="space-y-3">
                                {{-- Badges --}}
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if($isServer)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-cell text-[11px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">
                                            Server-side
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-cell text-[11px] font-bold uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200">
                                            Client-side
                                        </span>
                                    @endif

                                    <span class="text-[11px] font-mono font-medium text-ink-muted bg-paper px-2 py-0.5 rounded-cell border border-rule">
                                        Level {{ $mod->level }}
                                    </span>

                                    <span class="text-[11px] text-ink-muted tabular-nums">
                                        {{ $mod->duration_minutes }} Menit
                                    </span>
                                </div>

                                <h3 class="text-base font-bold text-ink leading-snug line-clamp-2">
                                    {{ $mod->title }}
                                </h3>

                                <p class="text-xs text-ink-muted line-clamp-3 leading-relaxed">
                                    {{ $mod->summary }}
                                </p>

                                {{-- Timestamps metadata --}}
                                <div class="p-2.5 bg-paper rounded-cell border border-rule text-xs space-y-1">
                                    <div class="flex justify-between text-ink-muted">
                                        <span>Diupload:</span>
                                        <span class="font-medium text-ink tabular-nums">{{ $mod->opens_at?->translatedFormat('l, d M Y') ?? 'Terbuka' }}</span>
                                    </div>
                                    <div class="flex justify-between text-ink-muted border-t border-rule/50 pt-1">
                                        <span>Batas Tenggat:</span>
                                        <span class="font-bold text-brand-deep tabular-nums">{{ $mod->closes_at?->translatedFormat('l, d M Y, H:i') ?? '—' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-rule">
                                <a
                                    href="{{ route('login') }}"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 text-xs font-semibold rounded-cell bg-sheet text-ink border border-rule hover:bg-paper transition-colors"
                                >
                                    Buka Detail Modul &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ==================== ROADMAP CURRICULUM PREVIEW ==================== --}}
        <section id="roadmap" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-sheet border border-rule rounded-panel p-6 sm:p-8 space-y-8 shadow-sm">
                <div class="max-w-2xl space-y-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-deep">Kurikulum WorldSkills</span>
                    <h2 class="text-2xl font-bold text-ink">
                        Roadmap Materi Pelatihan Terstruktur
                    </h2>
                    <p class="text-xs sm:text-sm text-ink-muted">
                        Materi disesuaikan dengan kisi-kisi resmi LKS Tingkat Provinsi Jawa Tengah dan Nasional 2026.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Phase 1 --}}
                    <div class="p-4 bg-paper rounded-cell border border-rule space-y-2.5">
                        <span class="text-[11px] font-mono font-bold text-brand-deep uppercase">Fase 1 &bull; Level 0 - 2</span>
                        <h3 class="text-sm font-bold text-ink">Fondasi &amp; Desain Web Semantik</h3>
                        <p class="text-xs text-ink-muted leading-relaxed">
                            Struktur dokumen HTML5 semantik, CSS Modern Flexbox &amp; CSS Grid, aksesibilitas ARIA, form validation native, dan tata letak responsif.
                        </p>
                    </div>

                    {{-- Phase 2 --}}
                    <div class="p-4 bg-paper rounded-cell border border-rule space-y-2.5">
                        <span class="text-[11px] font-mono font-bold text-sky-700 uppercase">Fase 2 &bull; Level 3 - 5</span>
                        <h3 class="text-sm font-bold text-ink">Client-Side &amp; Algoritma Interaktif</h3>
                        <p class="text-xs text-ink-muted leading-relaxed">
                            Vanilla JS DOM API murni tanpa framework, SVG rendering, mouse event pipeline (pan/zoom), algoritma DFS pathfinding, dan persistensi LocalStorage.
                        </p>
                    </div>

                    {{-- Phase 3 --}}
                    <div class="p-4 bg-paper rounded-cell border border-rule space-y-2.5">
                        <span class="text-[11px] font-mono font-bold text-purple-700 uppercase">Fase 3 &bull; Level 6 - 8</span>
                        <h3 class="text-sm font-bold text-ink">Backend RESTful API &amp; Otomasi Pengujian</h3>
                        <p class="text-xs text-ink-muted leading-relaxed">
                            Arsitektur Laravel Sanctum token auth, transaksi database atomik, integrasi frontend Axios/Chart.js, dan pengujian headless Playwright.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ==================== SELECTION PROCESS WORKFLOW ==================== --}}
        <section id="alur" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="space-y-8">
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-deep">Alur Seleksi</span>
                    <h2 class="text-2xl font-bold text-ink">
                        4 Langkah Menuju Tim Delegasi Sekolah
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-sheet border border-rule rounded-panel p-5 space-y-2 shadow-xs">
                        <span class="w-7 h-7 rounded-full bg-brand/10 text-brand-deep font-bold text-xs flex items-center justify-center border border-brand/20">1</span>
                        <h3 class="text-sm font-bold text-ink">Masuk Akun NIS</h3>
                        <p class="text-xs text-ink-muted">Gunakan kredensial resmi NIS siswa untuk mengakses dashboard seleksi.</p>
                    </div>

                    <div class="bg-sheet border border-rule rounded-panel p-5 space-y-2 shadow-xs">
                        <span class="w-7 h-7 rounded-full bg-brand/10 text-brand-deep font-bold text-xs flex items-center justify-center border border-brand/20">2</span>
                        <h3 class="text-sm font-bold text-ink">Pelajari Roadmap</h3>
                        <p class="text-xs text-ink-muted">Selesaikan materi bacaan dan kuis checkpoint untuk membuka modul lanjutan.</p>
                    </div>

                    <div class="bg-sheet border border-rule rounded-panel p-5 space-y-2 shadow-xs">
                        <span class="w-7 h-7 rounded-full bg-brand/10 text-brand-deep font-bold text-xs flex items-center justify-center border border-brand/20">3</span>
                        <h3 class="text-sm font-bold text-ink">Kirim Tugas Modul (.ZIP)</h3>
                        <p class="text-xs text-ink-muted">Unggah arsip kode proyek sebelum batas tenggat berakhir untuk diuji oleh sistem.</p>
                    </div>

                    <div class="bg-sheet border border-rule rounded-panel p-5 space-y-2 shadow-xs">
                        <span class="w-7 h-7 rounded-full bg-brand/10 text-brand-deep font-bold text-xs flex items-center justify-center border border-brand/20">4</span>
                        <h3 class="text-sm font-bold text-ink">Pantau Peringkat</h3>
                        <p class="text-xs text-ink-muted">Akumulasi nilai dinilai transparan hingga penetapan delegasi resmi sekolah.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ==================== CTA BANNER ==================== --}}
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-sheet border border-rule border-l-4 border-l-brand rounded-panel p-8 sm:p-12 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-2 max-w-2xl">
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-deep">Siap Berkompetisi?</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-ink">
                        Wujudkan Prestasimu di LKS Web Technologies 2026
                    </h2>
                    <p class="text-xs sm:text-sm text-ink-muted leading-relaxed">
                        Masuk sekarang menggunakan akun NIS siswa untuk mulai menyelesaikan modul dan mencatatkan skor terbaikmu di papan peringkat.
                    </p>
                </div>

                <div class="shrink-0 w-full md:w-auto">
                    <a
                        href="{{ route('login') }}"
                        class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 text-sm font-bold rounded-cell transition-all shadow-sm hover:opacity-95 text-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                        style="color: #ffffff !important; background-color: #c92a2a !important;"
                    >
                        Buka Portal Siswa &rarr;
                    </a>
                </div>
            </div>
        </section>
    </main>

    {{-- Footer --}}
    <footer class="border-t border-rule bg-sheet mt-auto">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 text-xs text-ink-muted flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-bold text-ink">{{ __('general.school_name') }}</span>
                <span>&bull;</span>
                <span>LKS Web Technologies 2026</span>
            </div>
            <div>
                <span>Standar Kurikulum WorldSkills &amp; Kemendikbudristek</span>
            </div>
        </div>
    </footer>
</body>
</html>
