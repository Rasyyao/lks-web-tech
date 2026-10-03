{{-- Error layout: same white header + 3px brand rule as the app, centered panel with the status code --}}
{{-- DESIGN_RULES: no illustrations or emoji; JetBrains Mono for the code, panel + rule border, brand red only for emphasis --}}
@props([
    'code',
    'title',
    'message',
    'hint' => null,
])

@php
    // Error pages may render while the session/database itself is failing,
    // so never let the user lookup throw a second exception.
    try {
        $user = auth()->user();
        $homeUrl = $user ? $user->homeUrl() : route('login');
    } catch (\Throwable) {
        $user = null;
        $homeUrl = url('/masuk');
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>{{ $code }} — {{ $title }} — SMK Telkom Purwokerto</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-paper text-ink font-sans antialiased flex flex-col">
    <header class="bg-sheet border-b-[3px] border-brand">
        <div class="mx-auto max-w-7xl xl:max-w-[1400px] px-4 sm:px-6 lg:px-8 flex items-center h-14 sm:h-16">
            <a href="{{ $homeUrl }}" class="flex items-center gap-3 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                @if(file_exists(public_path('brand/logo-smk-telkom-purwokerto.png')))
                    <img src="{{ asset('brand/logo-smk-telkom-purwokerto.png') }}" alt="Logo SMK Telkom Purwokerto" class="h-8 sm:h-10 w-auto">
                @endif
                <span class="text-sm sm:text-base font-medium text-ink hidden sm:inline">LKS Web Technology</span>
            </a>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center px-4 py-10 sm:py-16">
        <div class="w-full max-w-xl">
            <x-panel class="space-y-6">
                <p class="font-mono text-6xl sm:text-7xl font-bold text-brand-deep leading-none" aria-hidden="true">{{ $code }}</p>

                <div class="space-y-2">
                    <h1 class="text-xl sm:text-2xl font-bold text-ink">{{ $title }}</h1>
                    <p class="text-sm sm:text-base text-ink-muted">{{ $message }}</p>
                </div>

                @if($hint)
                    <p class="bg-tint border border-brand-deep/20 rounded-panel px-4 py-3 text-sm text-ink">{{ $hint }}</p>
                @endif

                @if($user)
                    <p class="text-xs text-ink-muted border-t border-rule pt-4">
                        Masuk sebagai <span class="font-mono text-ink">{{ $user->username }}</span>
                    </p>
                @endif

                <div class="flex flex-wrap items-center gap-3">
                    @if($user)
                        <x-button :href="$homeUrl" size="lg">Ke beranda saya</x-button>
                        <button type="button" onclick="history.length > 1 ? history.back() : (location.href = '{{ $homeUrl }}')"
                            class="inline-flex items-center justify-center font-medium rounded-cell transition-colors px-6 py-3 text-base bg-sheet text-ink border border-rule hover:border-ink-muted focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                            Halaman sebelumnya
                        </button>
                        <form method="POST" action="{{ route('logout') }}" class="ml-auto">
                            @csrf
                            <button type="submit" class="text-sm text-ink-muted hover:text-brand-deep focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                                Ganti akun (keluar)
                            </button>
                        </form>
                    @else
                        <x-button :href="$homeUrl" size="lg">Masuk</x-button>
                    @endif
                </div>
            </x-panel>
        </div>
    </main>

    <footer class="border-t border-rule mt-auto bg-sheet">
        <div class="mx-auto max-w-7xl xl:max-w-[1400px] px-4 sm:px-6 lg:px-8 py-6 text-sm text-ink-muted">
            SMK Telkom Purwokerto — LKS Web Technology
        </div>
    </footer>
</body>
</html>
