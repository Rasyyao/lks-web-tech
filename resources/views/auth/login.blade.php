{{-- Login Screen --}}
{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Login (/masuk) --}}
{{-- Primary job of the screen: Authenticate students and mentors with NIS/username and password. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, tint --}}
{{-- Type roles: Schibsted Grotesk for headings and labels; JetBrains Mono for username/NIS input --}}
{{-- Layout idea: Centered white panel, school logo, username & password inputs, primary red button, help line --}}
{{-- What I changed after the "would any app have this?" check: Removed floating cards, colorful blobs, welcome wave emoji, social login buttons; kept clean school exam-portal aesthetic. --}}

<x-layouts.guest :title="__('auth.login')">
    <x-panel class="space-y-6">
        <div class="text-center space-y-2">
            @if(file_exists(public_path('brand/logo-smk-telkom-purwokerto.png')))
                <img
                    src="{{ asset('brand/logo-smk-telkom-purwokerto.png') }}"
                    alt="Logo SMK Telkom Purwokerto"
                    class="h-10 mx-auto w-auto"
                >
            @endif
            <h1 class="text-lg font-bold text-ink">
                {{ __('general.site_name') }}
            </h1>
            <p class="text-xs text-ink-muted">
                {{ __('general.school_name') }}
            </p>
        </div>

        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
            @csrf

            <x-field
                :label="__('auth.username')"
                id="username"
                name="username"
                type="text"
                class="font-mono"
                :value="old('username')"
                :error="$errors->first('username')"
                :required="true"
                autocomplete="username"
                autofocus
                placeholder="NIS atau username"
            />

            <x-field
                :label="__('auth.password')"
                id="password"
                name="password"
                type="password"
                :error="$errors->first('password')"
                :required="true"
                autocomplete="current-password"
            />

            <div class="flex items-center justify-between text-xs text-ink-muted">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input
                        type="checkbox"
                        name="remember"
                        class="rounded-cell border-rule text-brand focus:ring-brand-deep"
                    >
                    <span>{{ __('auth.remember_me') }}</span>
                </label>
            </div>

            <x-button type="submit" class="w-full">
                {{ __('auth.login') }}
            </x-button>
        </form>

        <div class="pt-2 border-t border-rule text-center">
            <p class="text-xs text-ink-muted">
                {{ __('auth.forgot_password_help') }}
            </p>
        </div>
    </x-panel>
</x-layouts.guest>
