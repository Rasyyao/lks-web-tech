{{-- Guest layout: login and password change --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Masuk ke platform LKS Web Technology SMK Telkom Purwokerto">
    <title>{{ $title ?? __('auth.login') }} — {{ __('general.school_name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-paper text-ink font-sans antialiased flex flex-col items-center justify-center px-4">
    <div class="w-full max-w-sm">
        {{ $slot }}
    </div>

    <footer class="mt-8 text-sm text-ink-muted text-center">
        {{ __('general.school_name') }}
    </footer>
</body>
</html>
