{{-- App layout with persistent Sidebar Navigation --}}
{{-- DESIGN_RULES: Left sidebar on desktop with school logo, structured navigation groups, user profile at bottom; responsive top bar and mobile drawer. --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Platform latihan dan seleksi LKS Web Technology SMK Telkom Purwokerto">
    <title>{{ $title ?? __('general.site_name') }} — {{ __('general.school_name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-paper text-ink font-sans antialiased flex flex-col" x-data="{ mobileSidebarOpen: false }">
    @auth
        {{-- ==================== DESKTOP SIDEBAR ==================== --}}
        <aside class="hidden md:flex md:w-64 lg:w-72 md:flex-col md:fixed md:inset-y-0 bg-sheet border-r border-rule border-t-[3px] border-t-brand z-30 shadow-xs">
            {{-- Brand / Logo Header --}}
            <div class="h-16 flex items-center gap-3 px-5 border-b border-rule shrink-0">
                @if(file_exists(public_path('brand/logo-smk-telkom-purwokerto.png')))
                    <img
                        src="{{ asset('brand/logo-smk-telkom-purwokerto.png') }}"
                        alt="Logo SMK Telkom Purwokerto"
                        class="h-9 w-auto shrink-0"
                    >
                @endif
                <div class="min-w-0">
                    <a href="{{ auth()->user()->homeUrl() }}" class="text-sm font-bold text-ink block truncate hover:text-brand-deep transition-colors focus-visible:outline-2 focus-visible:outline-brand-deep">
                        {{ __('general.site_name') }}
                    </a>
                    <span class="text-[11px] text-ink-muted block truncate">
                        {{ __('general.school_name') }}
                    </span>
                </div>
            </div>

            {{-- Sidebar Scrollable Navigation Links --}}
            <div class="flex-1 py-4 px-3 space-y-6 overflow-y-auto">
                {{-- Group 1: Navigasi Siswa / Utama --}}
                <div class="space-y-1">
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-ink-muted block">
                        Menu Utama
                    </span>

                    @if(auth()->user()->hasRole('student'))
                        <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            <x-slot:icon>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </x-slot:icon>
                            {{ __('general.dashboard') }}
                        </x-sidebar-link>

                        <x-sidebar-link href="{{ route('modules.index') }}" :active="request()->routeIs('modules.*')">
                            <x-slot:icon>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </x-slot:icon>
                            {{ __('general.modules') }}
                        </x-sidebar-link>

                        <x-sidebar-link href="{{ route('roadmap.index') }}" :active="request()->routeIs('roadmap.*') || request()->routeIs('materials.*')">
                            <x-slot:icon>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                            </x-slot:icon>
                            Belajar (Roadmap)
                        </x-sidebar-link>
                    @endif

                    <x-sidebar-link href="{{ route('question-bank.index') }}" :active="request()->routeIs('question-bank.*')">
                        <x-slot:icon>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </x-slot:icon>
                        Bank Soal LKS
                    </x-sidebar-link>

                    <x-sidebar-link href="{{ route('leaderboard') }}" :active="request()->routeIs('leaderboard')">
                        <x-slot:icon>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .982-3.172M12 2.25a2.25 2.25 0 0 0-2.25 2.25v.894m4.5 0V4.5A2.25 2.25 0 0 0 12 2.25Z" />
                            </svg>
                        </x-slot:icon>
                        {{ __('general.leaderboard') }}
                    </x-sidebar-link>
                </div>

                {{-- Group 2: Evaluasi & Mentor (if mentor or admin) --}}
                @if(auth()->user()->hasRole(['mentor', 'admin']))
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-ink-muted block">
                            Evaluasi Mentor
                        </span>

                        <x-sidebar-link href="{{ route('mentor.submissions') }}" :active="request()->routeIs('mentor.*')">
                            <x-slot:icon>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </x-slot:icon>
                            {{ __('general.mentor_inbox') }}
                        </x-sidebar-link>
                    </div>
                @endif

                {{-- Group 3: Administrasi (if admin) --}}
                @if(auth()->user()->hasRole('admin'))
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-ink-muted block">
                            Administrasi
                        </span>

                        <x-sidebar-link href="/admin">
                            <x-slot:icon>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </x-slot:icon>
                            Panel Admin
                        </x-sidebar-link>
                    </div>
                @endif
            </div>

            {{-- Sidebar User Profile & Logout Bottom Card --}}
            <div class="p-3 border-t border-rule bg-paper/60">
                <div class="flex items-center justify-between p-2 rounded-cell bg-sheet border border-rule/80">
                    <div class="flex items-center gap-2.5 min-w-0 pr-1">
                        <div class="w-8 h-8 rounded-full bg-brand/10 text-brand-deep font-bold text-xs flex items-center justify-center shrink-0 border border-brand/20">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <a href="{{ route('profile') }}" class="text-xs font-bold text-ink block truncate hover:text-brand-deep focus-visible:outline-2 focus-visible:outline-brand-deep">
                                {{ auth()->user()->name }}
                            </a>
                            <span class="text-[10px] text-ink-muted block truncate font-mono">
                                {{ auth()->user()->username }}
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <button
                            type="submit"
                            title="{{ __('auth.logout') }}"
                            class="p-1.5 text-ink-muted hover:text-brand-deep hover:bg-tint rounded-cell transition-colors focus-visible:outline-2 focus-visible:outline-brand-deep"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- ==================== MOBILE SLIDE-OVER DRAWER ==================== --}}
        <div
            x-show="mobileSidebarOpen"
            class="fixed inset-0 z-50 md:hidden"
            style="display: none;"
            x-cloak
        >
            {{-- Backdrop --}}
            <div
                x-show="mobileSidebarOpen"
                x-transition:enter="transition-opacity ease-linear duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-ink/40 backdrop-blur-xs"
                @click="mobileSidebarOpen = false"
            ></div>

            {{-- Drawer Content --}}
            <div
                x-show="mobileSidebarOpen"
                x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-300 transform"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="relative flex flex-col w-72 max-w-full h-full bg-sheet border-r border-rule shadow-xl"
            >
                <div class="h-16 flex items-center justify-between px-5 border-b border-rule">
                    <div class="flex items-center gap-2">
                        @if(file_exists(public_path('brand/logo-smk-telkom-purwokerto.png')))
                            <img src="{{ asset('brand/logo-smk-telkom-purwokerto.png') }}" alt="Logo" class="h-8 w-auto">
                        @endif
                        <span class="text-xs font-bold text-ink">{{ __('general.site_name') }}</span>
                    </div>
                    <button
                        type="button"
                        @click="mobileSidebarOpen = false"
                        class="p-1.5 rounded-cell text-ink-muted hover:text-ink hover:bg-paper"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Mobile Drawer Links --}}
                <div class="flex-1 py-4 px-3 space-y-4 overflow-y-auto">
                    <div class="space-y-1">
                        @if(auth()->user()->hasRole('student'))
                            <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                                {{ __('general.dashboard') }}
                            </x-sidebar-link>
                            <x-sidebar-link href="{{ route('modules.index') }}" :active="request()->routeIs('modules.*')">
                                {{ __('general.modules') }}
                            </x-sidebar-link>
                            <x-sidebar-link href="{{ route('roadmap.index') }}" :active="request()->routeIs('roadmap.*') || request()->routeIs('materials.*')">
                                Belajar (Roadmap)
                            </x-sidebar-link>
                        @endif
                        <x-sidebar-link href="{{ route('question-bank.index') }}" :active="request()->routeIs('question-bank.*')">
                            Bank Soal LKS
                        </x-sidebar-link>
                        <x-sidebar-link href="{{ route('leaderboard') }}" :active="request()->routeIs('leaderboard')">
                            {{ __('general.leaderboard') }}
                        </x-sidebar-link>
                        @if(auth()->user()->hasRole(['mentor', 'admin']))
                            <x-sidebar-link href="{{ route('mentor.submissions') }}" :active="request()->routeIs('mentor.*')">
                                {{ __('general.mentor_inbox') }}
                            </x-sidebar-link>
                        @endif
                        @if(auth()->user()->hasRole('admin'))
                            <x-sidebar-link href="/admin">
                                Panel Admin
                            </x-sidebar-link>
                        @endif
                    </div>
                </div>

                {{-- Mobile Drawer User Info --}}
                <div class="p-3 border-t border-rule bg-paper">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-ink">{{ auth()->user()->name }}</p>
                            <a href="{{ route('profile') }}" class="text-[11px] text-brand-deep hover:underline">Lihat Profil</a>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-xs text-ink-muted hover:text-brand-deep">
                                {{ __('auth.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endauth

    {{-- ==================== MAIN CONTENT WRAPPER ==================== --}}
    <div class="@auth md:pl-64 lg:pl-72 @endauth flex-1 flex flex-col min-w-0">
        {{-- Top Navigation Header Bar --}}
        <header class="bg-sheet border-b border-rule sticky top-0 z-20 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between h-14 sm:h-16">
                {{-- Left: Mobile Hamburger Toggle + Context Breadcrumb --}}
                <div class="flex items-center gap-3">
                    @auth
                        <button
                            type="button"
                            @click="mobileSidebarOpen = true"
                            class="md:hidden p-2 rounded-cell text-ink-muted hover:text-ink hover:bg-paper focus-visible:outline-2 focus-visible:outline-brand-deep"
                            aria-label="Buka menu"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    @endauth

                    <div class="flex items-center gap-2 text-xs">
                        <span class="inline-flex items-center gap-1.5 font-bold text-brand-deep uppercase tracking-wider px-2 py-0.5 rounded-cell bg-tint border border-brand-deep/15">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand"></span>
                            LKS 2026
                        </span>
                        <span class="text-rule hidden sm:inline">&bull;</span>
                        <span class="text-xs text-ink-muted hidden sm:inline font-medium">
                            Bidang Web Technologies
                        </span>
                    </div>
                </div>

                {{-- Right: Quick Status & User Link --}}
                <div class="flex items-center gap-3 text-xs">
                    @auth
                        <div class="hidden sm:flex items-center gap-2 text-ink-muted">
                            <span class="tabular-nums font-mono text-[11px] bg-paper px-2 py-0.5 rounded-cell border border-rule">
                                {{ auth()->user()->roles->pluck('name')->first() ? ucfirst(auth()->user()->roles->pluck('name')->first()) : 'Siswa' }}
                            </span>
                        </div>

                        <a href="{{ route('profile') }}" class="flex items-center gap-2 hover:text-brand-deep transition-colors focus-visible:outline-2 focus-visible:outline-brand-deep">
                            <div class="w-7 h-7 rounded-full bg-brand/10 text-brand-deep font-bold text-xs flex items-center justify-center border border-brand/20">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="font-medium text-ink hidden sm:inline">{{ auth()->user()->name }}</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="font-medium text-brand-deep hover:underline">
                            {{ __('auth.login') }}
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        {{-- Flash messages (SweetAlert2 carrier + noscript fallback) --}}
        @if(session('error') || session('warning') || session('success') || session('info') || session('status'))
            <div
                id="flash-alerts-data"
                class="hidden"
                @if(session('error')) data-error="{{ session('error') }}" @endif
                @if(session('warning')) data-warning="{{ session('warning') }}" @endif
                @if(session('success')) data-success="{{ session('success') }}" @endif
                @if(session('info')) data-info="{{ session('info') }}" @endif
                @if(session('status')) data-status="{{ session('status') }}" @endif
            ></div>
            <noscript>
                <div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
                    @if(session('error'))
                        <div class="bg-tint border border-brand-deep/20 rounded-panel px-4 py-3 text-sm text-brand-deep" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if(session('warning'))
                        <div class="bg-tint border border-brand/20 rounded-panel px-4 py-3 text-sm text-ink" role="alert">
                            {{ session('warning') }}
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="bg-pass/10 border border-pass/20 rounded-panel px-4 py-3 text-sm text-pass" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>
            </noscript>
        @endif

        {{-- Main Page Content Slot --}}
        <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="border-t border-rule mt-auto bg-sheet">
            <div class="w-full px-4 sm:px-6 lg:px-8 py-5 text-xs text-ink-muted flex flex-col sm:flex-row items-center justify-between gap-2">
                <span>{{ __('general.school_name') }} &bull; {{ __('general.site_name') }}</span>
                <span>Standar LKS SMK Tingkat Provinsi &amp; Nasional 2026</span>
            </div>
        </footer>
    </div>

    {{-- Mobile bottom navigation (for students on small screens) --}}
    @auth
        @if(auth()->user()->hasRole('student'))
            <nav class="md:hidden fixed bottom-0 inset-x-0 bg-sheet border-t border-rule z-40" aria-label="Navigasi mobile">
                <div class="flex items-center justify-around h-14">
                    <x-mobile-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="home">
                        {{ __('general.dashboard') }}
                    </x-mobile-nav-link>
                    <x-mobile-nav-link href="{{ route('modules.index') }}" :active="request()->routeIs('modules.*')" icon="book">
                        {{ __('general.modules') }}
                    </x-mobile-nav-link>
                    <x-mobile-nav-link href="{{ route('roadmap.index') }}" :active="request()->routeIs('roadmap.*') || request()->routeIs('materials.*')" icon="map">
                        Belajar
                    </x-mobile-nav-link>
                    <x-mobile-nav-link href="{{ route('leaderboard') }}" :active="request()->routeIs('leaderboard')" icon="trophy">
                        {{ __('general.leaderboard') }}
                    </x-mobile-nav-link>
                    <x-mobile-nav-link href="{{ route('profile') }}" :active="request()->routeIs('profile*')" icon="user">
                        {{ __('general.profile') }}
                    </x-mobile-nav-link>
                </div>
            </nav>
            <div class="md:hidden h-14"></div>
        @endif
    @endauth

    @livewireScripts
</body>
</html>
