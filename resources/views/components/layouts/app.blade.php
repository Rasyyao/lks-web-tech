{{-- App layout: white header, school logo, navigation, brand rule underneath --}}
{{-- DESIGN_RULES: header is white bar, logo left, nav, user menu right, 3px brand rule underneath --}}
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
<body class="min-h-screen bg-paper text-ink font-sans antialiased">
    {{-- Header --}}
    <header class="bg-sheet border-b-[3px] border-brand">
        <div class="mx-auto max-w-7xl xl:max-w-[1400px] px-4 sm:px-6 lg:px-8 flex items-center justify-between h-14 sm:h-16">
            {{-- Logo and school name --}}
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="flex items-center gap-3 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                @if(file_exists(public_path('brand/logo-smk-telkom-purwokerto.png')))
                    <img
                        src="{{ asset('brand/logo-smk-telkom-purwokerto.png') }}"
                        alt="Logo SMK Telkom Purwokerto"
                        class="h-8 sm:h-10 w-auto"
                    >
                @endif
                <span class="text-sm sm:text-base font-medium text-ink hidden sm:inline">
                    {{ __('general.site_name') }}
                </span>
            </a>

            {{-- Desktop navigation --}}
            @auth
                <nav class="hidden md:flex items-center gap-1" aria-label="Navigasi utama">
                    @if(auth()->user()->hasRole('student'))
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            {{ __('general.dashboard') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('modules.index') }}" :active="request()->routeIs('modules.*')">
                            {{ __('general.modules') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('practice.start') }}" :active="request()->routeIs('practice.*')">
                            {{ __('general.practice') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('materials.index') }}" :active="request()->routeIs('materials.*')">
                            {{ __('general.materials') }}
                        </x-nav-link>
                    @endif
                    @if(auth()->user()->hasRole(['mentor', 'admin']))
                        <x-nav-link href="{{ route('mentor.submissions') }}" :active="request()->routeIs('mentor.*')">
                            {{ __('general.mentor_inbox') }}
                        </x-nav-link>
                    @endif
                    <x-nav-link href="{{ route('leaderboard') }}" :active="request()->routeIs('leaderboard')">
                        {{ __('general.leaderboard') }}
                    </x-nav-link>
                </nav>

                {{-- User menu --}}
                <div class="hidden md:flex items-center gap-3">
                    @if(auth()->user()->hasRole('admin'))
                        <a href="/admin" class="text-sm text-ink-muted hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                            {{ __('general.admin') }}
                        </a>
                    @endif
                    <a href="{{ route('profile') }}" class="text-sm text-ink-muted hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                        {{ auth()->user()->name }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-ink-muted hover:text-brand-deep focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                            {{ __('auth.logout') }}
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('error'))
        <div class="mx-auto max-w-7xl xl:max-w-[1400px] px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-tint border border-brand-deep/20 rounded-panel px-4 py-3 text-sm text-brand-deep" role="alert">
                {{ session('error') }}
            </div>
        </div>
    @endif
    @if(session('warning'))
        <div class="mx-auto max-w-7xl xl:max-w-[1400px] px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-tint border border-brand/20 rounded-panel px-4 py-3 text-sm text-ink" role="alert">
                {{ session('warning') }}
            </div>
        </div>
    @endif
    @if(session('success'))
        <div class="mx-auto max-w-7xl xl:max-w-[1400px] px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-pass/10 border border-pass/20 rounded-panel px-4 py-3 text-sm text-pass" role="alert">
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Main content --}}
    <main class="mx-auto max-w-7xl xl:max-w-[1400px] px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="border-t border-rule mt-auto">
        <div class="mx-auto max-w-7xl xl:max-w-[1400px] px-4 sm:px-6 lg:px-8 py-6 text-sm text-ink-muted">
            {{ __('general.school_name') }} — {{ __('general.site_name') }}
        </div>
    </footer>

    {{-- Mobile bottom navigation --}}
    @auth
        @if(auth()->user()->hasRole('student'))
            <nav class="md:hidden fixed bottom-0 inset-x-0 bg-sheet border-t border-rule z-50" aria-label="Navigasi mobile">
                <div class="flex items-center justify-around h-14">
                    <x-mobile-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="home">
                        {{ __('general.dashboard') }}
                    </x-mobile-nav-link>
                    <x-mobile-nav-link href="{{ route('modules.index') }}" :active="request()->routeIs('modules.*')" icon="book">
                        {{ __('general.modules') }}
                    </x-mobile-nav-link>
                    <x-mobile-nav-link href="{{ route('practice.start') }}" :active="request()->routeIs('practice.*')" icon="pencil">
                        {{ __('general.practice') }}
                    </x-mobile-nav-link>
                    <x-mobile-nav-link href="{{ route('leaderboard') }}" :active="request()->routeIs('leaderboard')" icon="trophy">
                        {{ __('general.leaderboard') }}
                    </x-mobile-nav-link>
                    <x-mobile-nav-link href="{{ route('profile') }}" :active="request()->routeIs('profile*')" icon="user">
                        {{ __('general.profile') }}
                    </x-mobile-nav-link>
                </div>
            </nav>
        @endif
    @endauth

    {{-- Add bottom padding on mobile for the bottom nav --}}
    @auth
        @if(auth()->user()->hasRole('student'))
            <div class="md:hidden h-14"></div>
        @endif
    @endauth

    @livewireScripts
</body>
</html>
