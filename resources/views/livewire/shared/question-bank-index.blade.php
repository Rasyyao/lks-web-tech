{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Bank Soal LKS Web Technologies (/bank-soal) --}}
{{-- Primary job of the screen: Repository of official past LKS competition problem papers (PDFs) with multi-criteria filtering by Year, Competition Level (Kabupaten, Provinsi, Nasional), and Module Type (Client-Side, Server-Side), plus admin/mentor PDF upload up to 10MB. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, pass, paper, tint, gold --}}
{{-- Type roles: Schibsted Grotesk for prose & headings; tabular-nums for years & sizes; JetBrains Mono for badges & filenames --}}
{{-- Layout idea: 1400px widescreen workspace with summary KPI cards, interactive multi-criteria filter toolbar, 3-column card grid, and accessible administrative upload modal. --}}

<div class="space-y-6" x-data="{ modalOpen: @entangle('showModal') }">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="Breadcrumb">
        <ol class="flex items-center gap-1.5 text-xs text-ink-muted">
            <li>
                <a href="{{ auth()->check() && auth()->user()->hasRole('student') ? route('dashboard') : (auth()->user()?->hasRole(['mentor', 'admin']) ? route('mentor.submissions') : route('login')) }}" class="hover:text-brand-deep transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                    Beranda
                </a>
            </li>
            <li aria-hidden="true" class="text-rule">/</li>
            <li class="text-ink font-medium">
                Bank Soal LKS
            </li>
        </ol>
    </nav>

    {{-- Header Banner & Administrative Action --}}
    <x-panel class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-ink">
                        Bank Soal LKS Web Technologies
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-cell font-mono text-xs font-bold bg-tint text-brand-deep border border-brand-deep/20">
                        Arsip Resmi LKS
                    </span>
                </div>
                <p class="text-xs text-ink-muted mt-1 max-w-2xl leading-relaxed">
                    Kumpulan berkas soal lomba LKS resmi tingkat Kabupaten/Kota, Provinsi, dan Nasional untuk persiapan seleksi calon delegasi sekolah.
                </p>
            </div>

            @if($canManage)
                <div class="flex items-center gap-2">
                    <x-button
                        type="button"
                        wire:click="openCreateModal"
                        class="inline-flex items-center gap-2 text-xs"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Unggah Soal PDF (Maks. 10MB)</span>
                    </x-button>
                </div>
            @endif
        </div>

        {{-- KPI Summary Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 border-t border-rule">
            <div class="p-3 bg-paper rounded-cell border border-rule">
                <span class="text-[11px] text-ink-muted uppercase font-semibold block">Total Berkas Soal</span>
                <span class="text-2xl font-bold font-mono text-ink tabular-nums">{{ $totalCount }}</span>
            </div>

            <div class="p-3 bg-paper rounded-cell border border-rule">
                <span class="text-[11px] text-ink-muted uppercase font-semibold block">Modul Client-Side</span>
                <span class="text-2xl font-bold font-mono text-pass tabular-nums">{{ $clientCount }}</span>
            </div>

            <div class="p-3 bg-paper rounded-cell border border-rule">
                <span class="text-[11px] text-ink-muted uppercase font-semibold block">Modul Server-Side</span>
                <span class="text-2xl font-bold font-mono text-brand-deep tabular-nums">{{ $serverCount }}</span>
            </div>

            <div class="p-3 bg-paper rounded-cell border border-rule">
                <span class="text-[11px] text-ink-muted uppercase font-semibold block">Tingkatan Tersedia</span>
                <span class="text-2xl font-bold font-mono text-gold-text tabular-nums">3 Jenjang</span>
            </div>
        </div>
    </x-panel>

    {{-- Filter & Search Toolbar --}}
    <x-panel class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Search Input --}}
            <div class="lg:col-span-2 relative">
                <label for="searchSoal" class="sr-only">Cari Soal</label>
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-ink-muted">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    id="searchSoal"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari judul soal, topik, atau nama berkas..."
                    class="block w-full pl-9 pr-3 py-2 text-xs rounded-cell border border-rule bg-sheet text-ink placeholder-ink-muted focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                >
            </div>

            {{-- Filter 1: Tahun --}}
            <div>
                <label for="filterYear" class="sr-only">Filter Tahun</label>
                <select
                    id="filterYear"
                    wire:model.live="yearFilter"
                    class="block w-full py-2 px-3 text-xs rounded-cell border border-rule bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                >
                    <option value="">Semua Tahun</option>
                    @foreach($years as $yr)
                        <option value="{{ $yr }}">Tahun {{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter 2: Jenjang Tingkat Lomba --}}
            <div>
                <label for="filterLevel" class="sr-only">Filter Jenjang</label>
                <select
                    id="filterLevel"
                    wire:model.live="levelFilter"
                    class="block w-full py-2 px-3 text-xs rounded-cell border border-rule bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                >
                    <option value="">Semua Jenjang</option>
                    <option value="kabupaten">Tingkat Kabupaten / Kota</option>
                    <option value="provinsi">Tingkat Provinsi</option>
                    <option value="nasional">Tingkat Nasional</option>
                </select>
            </div>

            {{-- Filter 3: Tipe Modul (Client / Server) --}}
            <div>
                <label for="filterModuleType" class="sr-only">Filter Modul</label>
                <select
                    id="filterModuleType"
                    wire:model.live="moduleTypeFilter"
                    class="block w-full py-2 px-3 text-xs rounded-cell border border-rule bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                >
                    <option value="">Semua Tipe Modul</option>
                    <option value="client">Modul Client-Side</option>
                    <option value="server">Modul Server-Side</option>
                </select>
            </div>
        </div>

        {{-- Active Filter Pills and Reset --}}
        @if($search !== '' || $yearFilter !== '' || $levelFilter !== '' || $moduleTypeFilter !== '')
            <div class="flex items-center gap-2 flex-wrap pt-2 border-t border-rule text-xs">
                <span class="text-ink-muted">Filter aktif:</span>

                @if($search !== '')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-cell bg-paper text-ink border border-rule text-[11px]">
                        Kata kunci: "{{ $search }}"
                        <button type="button" wire:click="$set('search', '')" class="hover:text-brand-deep">&times;</button>
                    </span>
                @endif

                @if($yearFilter !== '')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-cell bg-paper text-ink border border-rule text-[11px]">
                        Tahun: {{ $yearFilter }}
                        <button type="button" wire:click="$set('yearFilter', '')" class="hover:text-brand-deep">&times;</button>
                    </span>
                @endif

                @if($levelFilter !== '')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-cell bg-paper text-ink border border-rule text-[11px]">
                        Jenjang: {{ ucfirst($levelFilter) }}
                        <button type="button" wire:click="$set('levelFilter', '')" class="hover:text-brand-deep">&times;</button>
                    </span>
                @endif

                @if($moduleTypeFilter !== '')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-cell bg-paper text-ink border border-rule text-[11px]">
                        Modul: {{ ucfirst($moduleTypeFilter) }}-Side
                        <button type="button" wire:click="$set('moduleTypeFilter', '')" class="hover:text-brand-deep">&times;</button>
                    </span>
                @endif

                <button
                    type="button"
                    wire:click="resetFilters"
                    class="text-xs text-brand-deep hover:underline font-semibold ml-auto"
                >
                    Reset Semua Filter
                </button>
            </div>
        @endif
    </x-panel>

    {{-- Question Papers Cards Grid --}}
    @if($papers->isEmpty())
        <x-panel class="text-center py-12 space-y-3">
            <div class="w-12 h-12 rounded-full bg-paper flex items-center justify-center mx-auto text-ink-muted border border-rule">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h2 class="text-base font-bold text-ink">Tidak Ada Berkas Soal</h2>
            <p class="text-xs text-ink-muted max-w-sm mx-auto">
                Tidak ditemukan arsip soal LKS yang sesuai dengan kriteria filter yang Anda pilih.
            </p>
            @if($search !== '' || $yearFilter !== '' || $levelFilter !== '' || $moduleTypeFilter !== '')
                <button
                    type="button"
                    wire:click="resetFilters"
                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-cell text-xs font-semibold bg-paper border border-rule text-ink hover:bg-sheet transition-colors"
                >
                    Reset Filter Pencarian
                </button>
            @endif
        </x-panel>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($papers as $paper)
                <div class="bg-sheet border border-rule rounded-panel p-5 flex flex-col justify-between space-y-4 hover:border-brand/40 transition-colors shadow-sm">
                    <div class="space-y-3">
                        {{-- Top Badge Row: Year, Level, Module Type --}}
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="px-2 py-0.5 rounded-cell font-mono text-[11px] font-bold bg-sheet border border-rule text-ink tabular-nums">
                                {{ $paper->year }}
                            </span>

                            <span class="px-2 py-0.5 rounded-cell text-[10px] font-medium border {{ $paper->level->badgeClass() }}">
                                {{ $paper->level->shortLabel() }}
                            </span>

                            <span class="px-2 py-0.5 rounded-cell font-mono text-[10px] font-bold border {{ $paper->module_type->badgeClass() }}">
                                {{ $paper->module_type === \App\Enums\CompetitionModuleType::Client ? 'Client-Side' : 'Server-Side' }}
                            </span>
                        </div>

                        {{-- Title --}}
                        <div>
                            <h2 class="text-base font-bold text-ink hover:text-brand-deep transition-colors leading-snug">
                                {{ $paper->title }}
                            </h2>
                            <p class="text-xs text-ink-muted mt-1.5 line-clamp-3 leading-relaxed">
                                {{ $paper->description ?: 'Dokumen soal resmi LKS Web Technologies mencakup instruksi tugas, studi kasus, arsitektur, dan kriteria penilaian.' }}
                            </p>
                        </div>
                    </div>

                    {{-- Card Footer & Actions --}}
                    <div class="pt-3 border-t border-rule space-y-3">
                        {{-- File Meta Info --}}
                        <div class="flex items-center justify-between text-[11px] text-ink-muted font-mono">
                            <span class="flex items-center gap-1 truncate max-w-[180px]" title="{{ $paper->file_name }}">
                                <svg class="w-3.5 h-3.5 text-brand-deep shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                </svg>
                                <span class="truncate">{{ $paper->file_name }}</span>
                            </span>
                            <span class="tabular-nums font-semibold text-ink shrink-0">{{ $paper->formatted_size }}</span>
                        </div>

                        <div class="flex items-center justify-between text-[11px] text-ink-muted">
                            <span class="tabular-nums">{{ $paper->download_count }}x diunduh</span>
                            <span>{{ $paper->created_at->diffForHumans() }}</span>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-2 pt-1">
                            <a
                                href="{{ route('download.question-paper', $paper->id) }}"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-cell text-xs font-semibold bg-brand text-sheet hover:bg-brand-deep transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep shadow-sm"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>Unduh PDF</span>
                            </a>

                            @if($canManage)
                                <button
                                    type="button"
                                    wire:click="edit({{ $paper->id }})"
                                    title="Edit Soal"
                                    class="p-2 rounded-cell text-xs text-ink-muted hover:text-ink bg-paper border border-rule hover:bg-sheet transition-colors"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                <button
                                    type="button"
                                    wire:confirm="Yakin ingin menghapus berkas soal '{{ $paper->title }}'?"
                                    wire:click="delete({{ $paper->id }})"
                                    title="Hapus Soal"
                                    class="p-2 rounded-cell text-xs text-brand-deep hover:text-sheet hover:bg-brand-deep bg-paper border border-rule transition-colors"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pt-4">
            {{ $papers->links() }}
        </div>
    @endif

    {{-- Administrative Upload / Edit Modal (Admin & Mentor Only) --}}
    @if($canManage)
        <div
            x-show="modalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div
                x-show="modalOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-ink/50 backdrop-blur-xs transition-opacity"
                x-on:click="modalOpen = false"
            ></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    x-show="modalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-panel bg-sheet border-2 border-rule text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl"
                >
                    <div class="px-6 py-5 border-b border-rule flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-bold text-ink" id="modal-title">
                                {{ $editingId ? 'Edit Berkas Bank Soal' : 'Unggah Soal LKS Baru' }}
                            </h2>
                            <p class="text-xs text-ink-muted mt-0.5">
                                Berkas format PDF dengan batas ukuran maksimal 10 MB.
                            </p>
                        </div>
                        <button
                            type="button"
                            x-on:click="modalOpen = false"
                            class="text-ink-muted hover:text-ink text-lg leading-none font-bold"
                        >
                            &times;
                        </button>
                    </div>

                    <form wire:submit="save" class="p-6 space-y-4">
                        {{-- Judul Soal --}}
                        <div class="space-y-1">
                            <label for="formTitle" class="block text-xs font-semibold text-ink">
                                Judul Berkas Soal <span class="text-brand-deep">*</span>
                            </label>
                            <input
                                type="text"
                                id="formTitle"
                                wire:model="title"
                                placeholder="Contoh: LKS Nasional XXXII 2024 - Modul Client-Side Interactive Map"
                                class="block w-full text-xs rounded-cell border border-rule bg-sheet text-ink px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                            >
                            @error('title')
                                <p class="text-[11px] text-brand-deep mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Grid: Tahun, Jenjang, Modul --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            {{-- Tahun --}}
                            <div class="space-y-1">
                                <label for="formYear" class="block text-xs font-semibold text-ink">
                                    Tahun Lomba <span class="text-brand-deep">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="formYear"
                                    wire:model="year"
                                    min="2010"
                                    max="2035"
                                    class="block w-full text-xs rounded-cell border border-rule bg-sheet text-ink px-3 py-2 font-mono tabular-nums focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                                >
                                @error('year')
                                    <p class="text-[11px] text-brand-deep mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Jenjang --}}
                            <div class="space-y-1">
                                <label for="formLevel" class="block text-xs font-semibold text-ink">
                                    Jenjang <span class="text-brand-deep">*</span>
                                </label>
                                <select
                                    id="formLevel"
                                    wire:model="level"
                                    class="block w-full text-xs rounded-cell border border-rule bg-sheet text-ink px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                                >
                                    <option value="kabupaten">Kabupaten/Kota</option>
                                    <option value="provinsi">Provinsi</option>
                                    <option value="nasional">Nasional</option>
                                </select>
                                @error('level')
                                    <p class="text-[11px] text-brand-deep mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Tipe Modul --}}
                            <div class="space-y-1">
                                <label for="formModuleType" class="block text-xs font-semibold text-ink">
                                    Tipe Modul <span class="text-brand-deep">*</span>
                                </label>
                                <select
                                    id="formModuleType"
                                    wire:model="module_type"
                                    class="block w-full text-xs rounded-cell border border-rule bg-sheet text-ink px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                                >
                                    <option value="client">Client-Side</option>
                                    <option value="server">Server-Side</option>
                                </select>
                                @error('module_type')
                                    <p class="text-[11px] text-brand-deep mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Deskripsi Soal --}}
                        <div class="space-y-1">
                            <label for="formDescription" class="block text-xs font-semibold text-ink">
                                Deskripsi / Catatan Singkat
                            </label>
                            <textarea
                                id="formDescription"
                                wire:model="description"
                                rows="3"
                                placeholder="Jelaskan ringkasan studi kasus, teknologi yang digunakan, atau instruksi khusus..."
                                class="block w-full text-xs rounded-cell border border-rule bg-sheet text-ink px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep font-sans"
                            ></textarea>
                            @error('description')
                                <p class="text-[11px] text-brand-deep mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Upload PDF File --}}
                        <div class="space-y-2 p-3 bg-paper rounded-cell border border-rule">
                            <div class="flex items-center justify-between">
                                <label for="pdfFile" class="block text-xs font-semibold text-ink">
                                    Berkas Soal (.pdf)
                                    @if(! $editingId)
                                        <span class="text-brand-deep">*</span>
                                    @endif
                                </label>
                                <span class="text-[10px] font-mono text-ink-muted">Maksimal 10 MB</span>
                            </div>

                            <input
                                type="file"
                                id="pdfFile"
                                wire:model="pdfFile"
                                accept=".pdf"
                                class="block w-full text-xs text-ink-muted file:mr-2.5 file:py-1.5 file:px-3 file:rounded-cell file:border file:border-rule file:text-xs file:font-semibold file:bg-sheet file:text-ink hover:file:bg-paper cursor-pointer border border-rule rounded-cell bg-sheet"
                            >

                            {{-- Upload Loading Indicator --}}
                            <div wire:loading wire:target="pdfFile" class="text-xs text-brand-deep flex items-center gap-1.5 pt-1">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Mengunggah dokumen PDF...</span>
                            </div>

                            @if($editingId && ! $pdfFile)
                                <p class="text-[11px] text-ink-muted italic">
                                    Kosongkan jika tidak ingin mengganti berkas PDF yang sudah ada.
                                </p>
                            @endif

                            @error('pdfFile')
                                <p class="text-[11px] text-brand-deep mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-rule">
                            <button
                                type="button"
                                x-on:click="modalOpen = false"
                                class="px-3 py-2 rounded-cell text-xs font-medium text-ink-muted hover:text-ink transition-colors"
                            >
                                Batal
                            </button>
                            <x-button type="submit" wire:loading.attr="disabled" wire:target="save, pdfFile">
                                <span wire:loading.remove wire:target="save">
                                    {{ $editingId ? 'Simpan Perubahan' : 'Unggah Soal' }}
                                </span>
                                <span wire:loading wire:target="save">Menyimpan...</span>
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
