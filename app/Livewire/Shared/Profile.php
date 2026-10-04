<?php

namespace App\Livewire\Shared;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Profil')]
class Profile extends Component
{
    public string $password = '';

    public string $password_confirmation = '';

    public bool $isPasswordMode = false;

    public function mount(): void
    {
        $this->isPasswordMode = request()->routeIs('profile.password') || (Auth::user()?->must_change_password ?? false);
    }

    public function updatePassword(): void
    {
        $user = Auth::user();

        $rules = [
            'password' => [
                'required',
                'string',
                Password::min(8)->letters()->mixedCase()->symbols(),
                'confirmed',
            ],
        ];

        $this->validate($rules, [
            'password.confirmed' => __('auth.password_mismatch'),
            'password.required' => __('validation.required', ['attribute' => 'Password baru']),
        ]);

        $user->update([
            'password' => Hash::make($this->password),
            'must_change_password' => false,
        ]);

        AuditLog::record(
            action: 'user.password_changed',
            subject: $user,
        );

        session()->flash('success', __('auth.password_changed'));

        $this->redirectRoute('dashboard');
    }

    public function render()
    {
        return view('livewire.shared.profile', [
            'user' => Auth::user(),
        ]);
    }
}
