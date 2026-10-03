{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Profile & Password (/profil, /profil/password) --}}
{{-- Primary job of the screen: View user details and update account password safely. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, tint, pass --}}
{{-- Type roles: Schibsted Grotesk for headings and labels; JetBrains Mono for username/NIS --}}
{{-- Layout idea: Stacked white panels for identity summary and password form --}}
{{-- What I changed after the "would any app have this?" check: Removed avatar upload dropzones and social links; focused on school identity (NIS, Class, Role) and secure password change. --}}

<div class="max-w-2xl mx-auto space-y-6">
    {{-- Notice if forced password change --}}
    @if($user->must_change_password)
        <div class="bg-tint border-l-4 border-brand p-4 rounded-panel" role="alert">
            <h2 class="text-sm font-bold text-brand-deep">
                {{ __('auth.must_change_password') }}
            </h2>
            <p class="text-xs text-ink mt-1">
                {{ __('auth.first_login_notice') }}
            </p>
        </div>
    @endif

    {{-- User identity details --}}
    <x-panel class="space-y-4">
        <div>
            <h1 class="text-xl font-bold text-ink">
                {{ __('general.profile') }}
            </h1>
            <p class="text-sm text-ink-muted">
                {{ __('general.school_name') }}
            </p>
        </div>

        <div class="border-t border-rule pt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="block text-xs text-ink-muted">{{ __('leaderboard.name') }}</span>
                <span class="font-medium text-ink">{{ $user->name }}</span>
            </div>
            <div>
                <span class="block text-xs text-ink-muted">{{ __('auth.username') }}</span>
                <span class="font-mono text-ink">{{ $user->username }}</span>
            </div>
            @if($user->email)
                <div>
                    <span class="block text-xs text-ink-muted">Email</span>
                    <span class="text-ink">{{ $user->email }}</span>
                </div>
            @endif
            <div>
                <span class="block text-xs text-ink-muted">{{ __('leaderboard.class') }} / Rombel</span>
                <span class="text-ink">
                    {{ $user->cohorts->pluck('name')->join(', ') ?: '—' }}
                </span>
            </div>
            <div>
                <span class="block text-xs text-ink-muted">Peran</span>
                <span class="capitalize text-ink">
                    {{ $user->roles->pluck('name')->join(', ') }}
                </span>
            </div>
        </div>
    </x-panel>

    {{-- Password change panel --}}
    <x-panel class="space-y-4">
        <div>
            <h2 class="text-lg font-bold text-ink">
                {{ __('auth.change_password') }}
            </h2>
            <p class="text-xs text-ink-muted">
                {{ __('auth.password_requirements') }}
            </p>
        </div>

        <form wire:submit="updatePassword" class="space-y-4 border-t border-rule pt-4">
            @if(! $user->must_change_password)
                <x-field
                    :label="__('auth.current_password')"
                    id="current_password"
                    type="password"
                    wire:model="current_password"
                    :error="$errors->first('current_password')"
                    :required="true"
                    autocomplete="current-password"
                />
            @endif

            <x-field
                :label="__('auth.new_password')"
                id="password"
                type="password"
                wire:model="password"
                :error="$errors->first('password')"
                :required="true"
                :help="__('auth.password_requirements')"
                autocomplete="new-password"
            />

            <x-field
                :label="__('auth.confirm_password')"
                id="password_confirmation"
                type="password"
                wire:model="password_confirmation"
                :error="$errors->first('password_confirmation')"
                :required="true"
                autocomplete="new-password"
            />

            <div class="flex items-center justify-end pt-2">
                <x-button type="submit">
                    {{ __('auth.change_password') }}
                </x-button>
            </div>
        </form>
    </x-panel>
</div>
