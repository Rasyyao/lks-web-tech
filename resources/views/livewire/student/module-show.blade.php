{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Module Detail Widescreen (/modul/{slug}) --}}
{{-- Primary job of the screen: Provide comprehensive LKS task brief (Backend REST API & Frontend Web App), database schema & ERD, API specification criteria, downloadable starter pack assets, and quick sidebar ZIP submission. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, pass, paper, tint, gold --}}
{{-- Type roles: Schibsted Grotesk for prose & headers; tabular-nums for scores and limits; JetBrains Mono for endpoints, code, JSON payloads, and headers --}}
{{-- Layout idea: High-productivity two-column workspace on desktop (8-col documentation & API tabs on left, 4-col sticky status, assets, submit form, and test results on right) utilizing 1400px widescreen space. --}}

<div class="space-y-6" x-data="{ activeTab: 'spec' }">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="Breadcrumb">
        <a href="{{ route('modules.index') }}" class="text-xs text-ink-muted hover:text-brand-deep inline-flex items-center gap-1.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep font-medium">
            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
            </svg>
            <span>{{ __('modules.list_title') }}</span>
            <span class="text-rule">/</span>
            <span class="text-ink font-semibold truncate">{{ $module->title }}</span>
        </a>
    </nav>

    {{-- Main Two-Column Grid: 8 Cols Left (Specs & Test Matrix) vs 4 Cols Right (Submit & History) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- LEFT COLUMN: Module Content & Technical Specs (8 Cols) --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Module Overview Card --}}
            <x-panel class="space-y-4">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="font-bold text-brand-deep uppercase tracking-wider px-2 py-0.5 rounded-cell bg-tint border border-brand-deep/10">
                        {{ $module->track->label() }}
                    </span>
                    <span class="text-rule">•</span>
                    <span class="tabular-nums font-medium text-ink bg-paper px-2 py-0.5 rounded-cell border border-rule">
                        Tingkat {{ $module->level }}
                    </span>
                    <span class="text-rule">•</span>
                    <span class="tabular-nums text-ink-muted">
                        Durasi {{ $module->duration_minutes }} Menit
                    </span>
                    <span class="text-rule">•</span>
                    <span class="tabular-nums text-ink-muted">
                        Maks. {{ $module->max_attempts_per_day }} kiriman / hari
                    </span>
                </div>

                <div class="space-y-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight">
                        {{ $module->title }}
                    </h1>
                    <p class="text-sm sm:text-base text-ink-muted leading-relaxed">
                        {{ $module->summary }}
                    </p>
                </div>

                {{-- Interactive Tabs Header --}}
                <div class="border-b border-rule pt-2 flex items-center gap-1 sm:gap-1.5 overflow-x-auto text-xs sm:text-sm no-scrollbar" role="tablist">
                    <button
                        type="button"
                        x-on:click="activeTab = 'spec'"
                        :class="activeTab === 'spec' ? 'border-brand text-brand-deep font-bold border-b-2 bg-tint/40' : 'border-transparent text-ink-muted hover:text-ink hover:border-rule'"
                        class="px-3 sm:px-4 py-2 rounded-t-cell transition-colors whitespace-nowrap focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                        role="tab"
                    >
                        Spesifikasi Tugas
                    </button>

                    <button
                        type="button"
                        x-on:click="activeTab = 'database'"
                        :class="activeTab === 'database' ? 'border-brand text-brand-deep font-bold border-b-2 bg-tint/40' : 'border-transparent text-ink-muted hover:text-ink hover:border-rule'"
                        class="px-3 sm:px-4 py-2 rounded-t-cell transition-colors whitespace-nowrap focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep flex items-center gap-1.5"
                        role="tab"
                    >
                        <span>Database & ERD</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-cell bg-paper text-ink font-mono font-medium border border-rule">
                            SQL Dump
                        </span>
                    </button>

                    <button
                        type="button"
                        x-on:click="activeTab = 'tests'"
                        :class="activeTab === 'tests' ? 'border-brand text-brand-deep font-bold border-b-2 bg-tint/40' : 'border-transparent text-ink-muted hover:text-ink hover:border-rule'"
                        class="px-3 sm:px-4 py-2 rounded-t-cell transition-colors whitespace-nowrap focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep flex items-center gap-1.5"
                        role="tab"
                    >
                        <span>Kriteria Pengujian API</span>
                        @if($testSuite && isset($testSuite['test_cases']))
                            <span class="text-[10px] px-1.5 py-0.2 rounded-cell bg-paper text-ink font-mono font-medium border border-rule">
                                {{ count($testSuite['test_cases']) }} Kasus
                            </span>
                        @endif
                    </button>

                    <button
                        type="button"
                        x-on:click="activeTab = 'rules'"
                        :class="activeTab === 'rules' ? 'border-brand text-brand-deep font-bold border-b-2 bg-tint/40' : 'border-transparent text-ink-muted hover:text-ink hover:border-rule'"
                        class="px-3 sm:px-4 py-2 rounded-t-cell transition-colors whitespace-nowrap focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                        role="tab"
                    >
                        Aturan & Format ZIP
                    </button>
                </div>
            </x-panel>

            {{-- TAB 1: SPESIFIKASI ENDPOINT & BRIEF LKS --}}
            <div x-show="activeTab === 'spec'" class="space-y-6">
                <x-panel class="space-y-6">
                    <div>
                        <h2 class="text-xl font-bold text-ink">
                            Panduan Teknis: PintarMenabung
                        </h2>
                        <p class="text-xs text-ink-muted">
                            Spesifikasi lengkap RESTful API Laravel Sanctum (Fase 1) dan Integrasi Frontend Bootstrap 5 & Axios (Fase 2).
                        </p>
                    </div>

                    <div class="prose max-w-none text-sm text-ink border-t border-rule pt-4 leading-relaxed">
                        {!! Str::markdown($module->brief_md) !!}
                    </div>
                </x-panel>
            </div>

            {{-- TAB 2: DATABASE SCHEMA & ERD --}}
            <div x-show="activeTab === 'database'" class="space-y-6" style="display: none;">
                <x-panel class="space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-bold text-ink">
                                Struktur Basis Data & ERD (PintarMenabung)
                            </h2>
                            <p class="text-xs text-ink-muted">
                                File dump database SQL telah disiapkan. Anda tidak perlu membuat berkas migration secara manual.
                            </p>
                        </div>

                        @php
                            $sqlAsset = $module->assets->first(fn($a) => str_contains($a->label, '.sql'));
                        @endphp
                        @if($sqlAsset)
                            <x-button
                                variant="secondary"
                                size="sm"
                                :href="URL::temporarySignedRoute('download.asset', now()->addMinutes(60), ['asset' => $sqlAsset->id])"
                            >
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Unduh pintar_menabung.sql
                            </x-button>
                        @endif
                    </div>

                    {{-- Database Info Banner --}}
                    <div class="p-3.5 bg-paper rounded-cell border border-rule space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-brand-deep">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Cara Mengimpor Basis Data:</span>
                        </div>
                        <div class="font-mono text-xs bg-sheet p-2.5 rounded-cell border border-rule text-ink space-y-1 overflow-x-auto">
                            <p class="text-ink-muted"># 1. Buat database di MySQL atau MariaDB:</p>
                            <p class="text-brand-deep font-semibold">mysql -u root -p -e "CREATE DATABASE pintar_menabung;"</p>
                            <p class="text-ink-muted mt-2"># 2. Impor berkas dump pintar_menabung.sql:</p>
                            <p class="text-brand-deep font-semibold">mysql -u root -p pintar_menabung &lt; pintar_menabung.sql</p>
                        </div>
                    </div>

                    {{-- ER Diagram Tables Summary --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Table 1: users --}}
                        <div class="p-3 bg-sheet rounded-cell border border-rule space-y-2">
                            <div class="flex items-center justify-between border-b border-rule pb-1.5">
                                <span class="font-mono font-bold text-ink text-xs">users</span>
                                <span class="text-[10px] text-ink-muted">Data Akun Pengguna</span>
                            </div>
                            <ul class="text-xs font-mono space-y-1 text-ink-muted">
                                <li><strong class="text-brand-deep">id</strong> : BIGINT UNSIGNED (PK)</li>
                                <li><strong class="text-ink">name</strong> : VARCHAR(255)</li>
                                <li><strong class="text-ink">email</strong> : VARCHAR(255) (UNIQUE)</li>
                                <li><strong class="text-ink">password</strong> : VARCHAR(255)</li>
                                <li><strong>created_at, updated_at</strong> : TIMESTAMP</li>
                            </ul>
                        </div>

                        {{-- Table 2: currencies --}}
                        <div class="p-3 bg-sheet rounded-cell border border-rule space-y-2">
                            <div class="flex items-center justify-between border-b border-rule pb-1.5">
                                <span class="font-mono font-bold text-ink text-xs">currencies</span>
                                <span class="text-[10px] text-ink-muted">Mata Uang (USD, IDR, dll)</span>
                            </div>
                            <ul class="text-xs font-mono space-y-1 text-ink-muted">
                                <li><strong class="text-brand-deep">id</strong> : BIGINT UNSIGNED (PK)</li>
                                <li><strong class="text-ink">name</strong> : VARCHAR(255)</li>
                                <li><strong class="text-ink">symbol</strong> : VARCHAR(255) ($, Rp)</li>
                                <li><strong class="text-ink">code</strong> : VARCHAR(255) (UNIQUE, USD, IDR)</li>
                                <li><strong>created_at, updated_at</strong> : TIMESTAMP</li>
                            </ul>
                        </div>

                        {{-- Table 3: categories --}}
                        <div class="p-3 bg-sheet rounded-cell border border-rule space-y-2">
                            <div class="flex items-center justify-between border-b border-rule pb-1.5">
                                <span class="font-mono font-bold text-ink text-xs">categories</span>
                                <span class="text-[10px] text-ink-muted">Kategori Transaksi</span>
                            </div>
                            <ul class="text-xs font-mono space-y-1 text-ink-muted">
                                <li><strong class="text-brand-deep">id</strong> : BIGINT UNSIGNED (PK)</li>
                                <li><strong class="text-ink">name</strong> : VARCHAR(255)</li>
                                <li><strong class="text-ink">icon</strong> : VARCHAR(255) (🍔, 💸, 🛒)</li>
                                <li><strong class="text-ink">type</strong> : ENUM('EXPENSE', 'INCOME')</li>
                                <li><strong class="text-ink">color</strong> : VARCHAR(20) (#55EFC4)</li>
                                <li><strong>created_at, updated_at</strong> : TIMESTAMP</li>
                            </ul>
                        </div>

                        {{-- Table 4: wallets --}}
                        <div class="p-3 bg-sheet rounded-cell border border-rule space-y-2">
                            <div class="flex items-center justify-between border-b border-rule pb-1.5">
                                <span class="font-mono font-bold text-ink text-xs">wallets</span>
                                <span class="text-[10px] text-ink-muted">Dompet Pengguna</span>
                            </div>
                            <ul class="text-xs font-mono space-y-1 text-ink-muted">
                                <li><strong class="text-brand-deep">id</strong> : BIGINT UNSIGNED (PK)</li>
                                <li><strong class="text-ink">user_id</strong> : BIGINT (FK &rarr; users.id)</li>
                                <li><strong class="text-ink">currency_code</strong> : VARCHAR (FK &rarr; currencies.code)</li>
                                <li><strong class="text-ink">name</strong> : VARCHAR(255)</li>
                                <li><strong>deleted_at</strong> : TIMESTAMP (SoftDeletes)</li>
                                <li><strong>created_at, updated_at</strong> : TIMESTAMP</li>
                            </ul>
                        </div>

                        {{-- Table 5: transactions --}}
                        <div class="p-3 bg-sheet rounded-cell border border-rule space-y-2 md:col-span-2">
                            <div class="flex items-center justify-between border-b border-rule pb-1.5">
                                <span class="font-mono font-bold text-ink text-xs">transactions</span>
                                <span class="text-[10px] text-ink-muted">Pencatatan Transaksi Finansial</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-mono text-ink-muted">
                                <div>
                                    <p><strong class="text-brand-deep">id</strong> : BIGINT UNSIGNED (PK)</p>
                                    <p><strong class="text-ink">wallet_id</strong> : BIGINT (FK &rarr; wallets.id)</p>
                                    <p><strong class="text-ink">category_id</strong> : BIGINT (FK &rarr; categories.id)</p>
                                </div>
                                <div>
                                    <p><strong class="text-ink">amount</strong> : BIGINT UNSIGNED (&gt;= 1)</p>
                                    <p><strong class="text-ink">date</strong> : DATE (Format Y-m-d)</p>
                                    <p><strong class="text-ink">note</strong> : VARCHAR(255) NULL</p>
                                    <p><strong>created_at, updated_at</strong> : TIMESTAMP</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-panel>
            </div>

            {{-- TAB 3: AUTOMATED TEST SUITE MATRIX (POSTMAN TEST CASES) --}}
            <div x-show="activeTab === 'tests'" class="space-y-6" style="display: none;">
                <x-panel class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-bold text-ink">
                                Rincian Test Suite Otomatis (Postman API Tests)
                            </h2>
                            <p class="text-xs text-ink-muted">
                                Kode backend siswa akan diuji terhadap skenario Postman resmi berikut untuk menentukan skor otomatis.
                            </p>
                        </div>

                        @php
                            $postmanAsset = $module->assets->first(fn($a) => str_contains($a->label, 'postman_collection'));
                        @endphp
                        @if($postmanAsset)
                            <x-button
                                variant="secondary"
                                size="sm"
                                :href="URL::temporarySignedRoute('download.asset', now()->addMinutes(60), ['asset' => $postmanAsset->id])"
                            >
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Unduh Postman Collection
                            </x-button>
                        @endif
                    </div>

                    @if($testSuite && isset($testSuite['test_cases']))
                        {{-- Backend API Test Cases Table --}}
                        <div class="overflow-x-auto border border-rule rounded-panel">
                            <table class="w-full text-left text-xs text-ink">
                                <thead class="bg-paper border-b border-rule">
                                    <tr>
                                        <th scope="col" class="py-2.5 px-3 font-semibold text-ink-muted w-28">ID Tes</th>
                                        <th scope="col" class="py-2.5 px-3 font-semibold text-ink-muted w-20">Method</th>
                                        <th scope="col" class="py-2.5 px-3 font-semibold text-ink-muted">Endpoint Target</th>
                                        <th scope="col" class="py-2.5 px-3 font-semibold text-ink-muted">Kriteria Pengujian</th>
                                        <th scope="col" class="py-2.5 px-3 font-semibold text-ink-muted text-center w-24">Status Kode</th>
                                        <th scope="col" class="py-2.5 px-3 font-semibold text-ink-muted text-right w-16">Bobot</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-rule">
                                    @foreach($testSuite['test_cases'] as $tc)
                                        <tr class="hover:bg-paper/50">
                                            <td class="py-2.5 px-3 font-mono font-bold text-ink-muted">
                                                {{ $tc['id'] }}
                                            </td>
                                            <td class="py-2.5 px-3">
                                                @php
                                                    $method = strtoupper($tc['method']);
                                                    $badgeClass = match($method) {
                                                        'GET' => 'bg-pass/10 text-pass border-pass/20 font-bold',
                                                        'POST' => 'bg-brand/10 text-brand-deep border-brand/20 font-bold',
                                                        'PUT', 'PATCH' => 'bg-gold/10 text-gold-text border-gold/20 font-bold',
                                                        'DELETE' => 'bg-tint text-brand-deep border-brand-deep/20 font-bold',
                                                        default => 'bg-paper text-ink border-rule',
                                                    };
                                                @endphp
                                                <span class="inline-block px-1.5 py-0.5 rounded-cell font-mono text-[10px] border {{ $badgeClass }}">
                                                    {{ $method }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-3 font-mono text-ink text-[11px]">
                                                {{ $tc['endpoint'] }}
                                            </td>
                                            <td class="py-2.5 px-3">
                                                <span class="font-medium text-ink block">{{ $tc['title'] }}</span>
                                                <span class="text-[11px] text-ink-muted">{{ $tc['group'] }}</span>
                                            </td>
                                            <td class="py-2.5 px-3 text-center">
                                                <span class="font-mono font-bold text-ink px-1.5 py-0.5 bg-paper rounded-cell border border-rule">
                                                    {{ $tc['expected_status'] }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-bold text-ink tabular-nums">
                                                {{ $tc['points'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-ink-muted py-4 text-center">
                            Spesifikasi test suite otomatis belum diunggah untuk modul ini.
                        </p>
                    @endif
                </x-panel>
            </div>

            {{-- TAB 4: RULES & ATURAN PENILAIAN --}}
            <div x-show="activeTab === 'rules'" class="space-y-6" style="display: none;">
                <x-panel class="space-y-4">
                    <h2 class="text-xl font-bold text-ink">
                        Aturan Teknis & Format Pengumpulan ZIP
                    </h2>
                    <div class="prose max-w-none text-sm text-ink border-t border-rule pt-4 leading-relaxed">
                        {!! Str::markdown($module->rules_md ?? 'Tidak ada aturan khusus.') !!}
                    </div>
                </x-panel>
            </div>
        </div>

        {{-- RIGHT COLUMN: Sticky Sidebar (Status, Submit Form, Assets & Student Submissions) (4 Cols) --}}
        <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6">
            {{-- Panel 1: Status & Tenggat Modul --}}
            <x-panel class="space-y-3">
                <div class="flex items-center justify-between border-b border-rule pb-2">
                    <span class="text-xs font-semibold text-ink-muted uppercase tracking-wider">Status Modul</span>
                    @if($isOpen)
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-pass">
                            <span class="w-2 h-2 rounded-full bg-pass animate-pulse"></span>
                            Menerima Kiriman
                        </span>
                    @else
                        <span class="text-xs font-bold text-brand-deep">
                            Pengumpulan Ditutup
                        </span>
                    @endif
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-ink-muted">Waktu Buka:</span>
                        <time class="font-medium text-ink tabular-nums">
                            {{ $module->opens_at?->translatedFormat('d M Y, H:i') ?? 'Terbuka' }}
                        </time>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-ink-muted">Batas Tenggat:</span>
                        <time class="font-bold text-brand-deep tabular-nums">
                            {{ $module->closes_at?->translatedFormat('d M Y, H:i') ?? 'Tidak terbatas' }}
                        </time>
                    </div>

                    <div class="flex justify-between items-center pt-2 border-t border-rule">
                        <span class="text-ink-muted">Sisa Kuota Hari Ini:</span>
                        <span class="font-bold text-ink tabular-nums bg-paper px-2 py-0.5 rounded-cell border border-rule">
                            {{ $remainingAttempts }} / {{ $module->max_attempts_per_day }} kiriman
                        </span>
                    </div>
                </div>
            </x-panel>

            {{-- Panel 2: Quick ZIP Submission Form (Prominently Placed in Sidebar) --}}
            <x-panel class="space-y-4 border-2 {{ $isOpen && $remainingAttempts > 0 ? 'border-brand/40 bg-sheet' : 'border-rule bg-paper/50' }}">
                <div>
                    <h3 class="text-base font-bold text-ink flex items-center justify-between">
                        <span>Pengumpulan Tugas</span>
                        <span class="text-[11px] font-mono font-normal text-ink-muted">.ZIP (Maks. 20MB)</span>
                    </h3>
                    <p class="text-xs text-ink-muted mt-0.5">
                        Unggah arsip proyek kode sumber Anda untuk diuji otomatis dan dinilai oleh mentor.
                    </p>
                </div>

                @if(! $isOpen)
                    <div class="p-3 rounded-cell bg-paper text-xs text-ink-muted border border-rule text-center">
                        Modul ini sedang ditutup dan tidak menerima pengumpulan tugas.
                    </div>
                @elseif($remainingAttempts <= 0)
                    <div class="p-3 rounded-cell bg-tint text-xs text-brand-deep border border-brand-deep/20 text-center font-medium">
                        Batas kuota pengumpulan hari ini telah habis ({{ $module->max_attempts_per_day }}x). Coba lagi besok.
                    </div>
                @else
                    <form wire:submit="submitZip" class="space-y-3">
                        <div class="space-y-1">
                            <label for="zipFile" class="block text-xs font-semibold text-ink">
                                Berkas Proyek (.zip):
                            </label>
                            <input
                                type="file"
                                id="zipFile"
                                wire:model="zipFile"
                                accept=".zip"
                                class="block w-full text-xs text-ink-muted file:mr-2.5 file:py-1.5 file:px-3 file:rounded-cell file:border file:border-rule file:text-xs file:font-semibold file:bg-paper file:text-ink hover:file:bg-sheet cursor-pointer border border-rule rounded-cell bg-sheet"
                            >
                            @error('zipFile')
                                <p class="text-[11px] text-brand-deep mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Uploading loading bar --}}
                        <div wire:loading wire:target="zipFile" class="text-xs text-ink-muted flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Mengunggah berkas ZIP...</span>
                        </div>

                        <x-button type="submit" class="w-full text-center" wire:loading.attr="disabled" wire:target="submitZip">
                            <span wire:loading.remove wire:target="submitZip">Kirim Tugas (.ZIP)</span>
                            <span wire:loading wire:target="submitZip">Menyimpan...</span>
                        </x-button>
                    </form>
                @endif
            </x-panel>

            {{-- Panel 3: Paket Aset & Starter Kit (Downloadable Assets) --}}
            <x-panel class="space-y-3">
                @php
                    $visibleAssets = $module->assets->reject(fn($a) => str_contains(strtolower($a->label), 'playwright') || str_contains(strtolower($a->label), 'test-suite'));
                @endphp

                <div class="flex items-center justify-between border-b border-rule pb-2">
                    <div>
                        <h3 class="text-sm font-bold text-ink">
                            Paket Aset & Starter Kit
                        </h3>
                        <p class="text-[11px] text-ink-muted">Berkas resmi untuk peserta</p>
                    </div>
                    <span class="text-[11px] font-mono font-medium text-brand-deep px-1.5 py-0.5 rounded-cell bg-tint border border-brand-deep/10">
                        {{ $visibleAssets->count() }} Berkas
                    </span>
                </div>

                @if($visibleAssets->isEmpty())
                    <p class="text-xs text-ink-muted py-2 text-center">
                        Tidak ada berkas starter pack untuk modul ini.
                    </p>
                @else
                    <ul class="divide-y divide-rule" role="list">
                        @foreach($visibleAssets as $asset)
                            @php
                                $isFullPack = str_contains($asset->label, 'full-package');
                                $isSql = str_contains($asset->label, '.sql');
                                $isPostman = str_contains($asset->label, 'postman');
                                $isFrontend = str_contains($asset->label, 'frontend');
                            @endphp
                            <li class="py-2.5 flex items-center justify-between gap-2 {{ $isFullPack ? 'bg-tint/40 p-2 rounded-cell -mx-1' : '' }}">
                                <div class="min-w-0 pr-2">
                                    <div class="flex items-center gap-1.5">
                                        @if($isFullPack)
                                            <span class="text-xs">📦</span>
                                        @elseif($isSql)
                                            <span class="text-xs">🗄️</span>
                                        @elseif($isPostman)
                                            <span class="text-xs">🧪</span>
                                        @elseif($isFrontend)
                                            <span class="text-xs">💻</span>
                                        @else
                                            <span class="text-xs">📄</span>
                                        @endif
                                        <span class="text-xs font-mono font-semibold {{ $isFullPack ? 'text-brand-deep' : 'text-ink' }} truncate" title="{{ $asset->label }}">
                                            {{ $asset->label }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-ink-muted tabular-nums block pl-5">
                                        {{ number_format($asset->size / 1024, 1) }} KB
                                    </span>
                                </div>
                                <a
                                    href="{{ URL::temporarySignedRoute('download.asset', now()->addMinutes(60), ['asset' => $asset->id]) }}"
                                    class="shrink-0 px-2.5 py-1 text-xs font-semibold rounded-cell {{ $isFullPack ? 'bg-brand text-sheet hover:bg-brand-deep' : 'bg-paper text-ink border border-rule hover:border-ink-muted' }} focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep transition-colors"
                                >
                                    Unduh
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-panel>

            {{-- Panel 4: Riwayat Pengumpulan & Nilai Siswa --}}
            <x-panel class="space-y-3">
                <div class="flex items-center justify-between border-b border-rule pb-2">
                    <h3 class="text-sm font-bold text-ink">
                        Riwayat Kiriman Saya
                    </h3>
                    <span class="text-[11px] text-ink-muted tabular-nums">{{ $submissions->count() }} Percobaan</span>
                </div>

                @if($submissions->isEmpty())
                    <p class="text-xs text-ink-muted py-3 text-center">
                        Belum ada tugas yang dikirimkan.
                    </p>
                @else
                    <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                        @foreach($submissions as $sub)
                            <div class="p-3 bg-paper rounded-cell border border-rule space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-ink">
                                        Percobaan #{{ $sub->attempt_no }}
                                    </span>
                                    <time class="text-[11px] text-ink-muted tabular-nums">
                                        {{ $sub->created_at->translatedFormat('d M, H:i') }}
                                    </time>
                                </div>

                                {{-- Score & Status Badges --}}
                                <div class="flex items-center justify-between pt-1 border-t border-rule/50">
                                    <div>
                                        @if($sub->status === \App\Enums\SubmissionStatus::Received)
                                            <span class="px-2 py-0.5 rounded-cell font-medium bg-tint text-brand-deep text-[11px]">
                                                Menunggu Review
                                            </span>
                                        @elseif($sub->status === \App\Enums\SubmissionStatus::Graded)
                                            <span class="px-2 py-0.5 rounded-cell font-bold bg-pass/10 text-pass text-[11px]">
                                                Nilai: {{ $sub->manual_score }}/100
                                            </span>
                                        @elseif($sub->status === \App\Enums\SubmissionStatus::Rejected)
                                            <span class="px-2 py-0.5 rounded-cell font-medium bg-sheet text-brand-deep border border-rule text-[11px]">
                                                Ditolak
                                            </span>
                                        @endif
                                    </div>

                                    @if($sub->test_results)
                                        @php
                                            $passed = $sub->test_results['passed'] ?? 0;
                                            $total = $sub->test_results['total'] ?? 0;
                                            $scoreVal = $sub->test_score ?? ($total > 0 ? round(($passed / $total) * 100) : 0);
                                            $ratioClass = ($passed === $total && $total > 0) ? 'text-pass font-bold' : 'text-gold-text font-semibold';
                                        @endphp
                                        <div class="text-[11px] font-mono {{ $ratioClass }}" title="Hasil Evaluasi Sistem">
                                            Uji Sistem: {{ $scoreVal }}/100 ({{ $passed }}/{{ $total }} Lulus)
                                        </div>
                                    @endif
                                </div>

                                {{-- Test results breakdown if available --}}
                                @if(isset($sub->test_results['details']))
                                    <div class="bg-sheet p-2 rounded-cell border border-rule text-[10px] font-mono space-y-1 text-ink-muted">
                                        <span class="font-bold text-ink block uppercase tracking-wider">Hasil Pengujian Sistem:</span>
                                        @foreach($sub->test_results['details'] as $cat => $val)
                                            <div class="flex justify-between">
                                                <span class="capitalize">{{ str_replace('_', ' ', $cat) }}:</span>
                                                <span class="font-bold text-ink">{{ $val }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Mentor Feedback Snippet --}}
                                @if($sub->feedback_md)
                                    <div class="bg-sheet p-2 rounded-cell border border-rule text-[11px] text-ink space-y-0.5">
                                        <span class="font-bold text-ink-muted block text-[10px] uppercase">Catatan Mentor:</span>
                                        <p class="line-clamp-3 italic">{{ $sub->feedback_md }}</p>
                                    </div>
                                @endif

                                <div class="pt-1 text-right">
                                    <a
                                        href="{{ route('download.submission', $sub->id) }}"
                                        class="text-[11px] text-brand-deep hover:underline font-medium focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                                    >
                                        Unduh berkas saya (.zip)
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-panel>
        </div>
    </div>
</div>
