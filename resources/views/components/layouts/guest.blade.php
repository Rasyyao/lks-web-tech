{{-- Guest layout: login and password change --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-xscale=1.0">
    <meta name="description" content="Masuk ke platform LKS Web Technology SMK Telkom Purwokerto">
    <title>{{ $title ?? __('auth.login') }} — {{ __('general.school_name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-paper text-ink font-sans antialiased flex flex-col items-center justify-center px-4">
    {{-- Flash messages (SweetAlert2 carrier) --}}
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
    @endif

    <div class="w-full max-w-sm">
        {{ $slot }}
    </div>

    <footer class="mt-8 text-sm text-ink-muted text-center">
        {{ __('general.school_name') }}
    </footer>
</body>
</html>
