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
    public string $current_password = '';
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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        // If not forced password change, require current password
        if (! $user->must_change_password) {
            $rules['current_password'] = ['required', 'string'];
        }

        $this->validate($rules, [
            'password.min' => __('auth.password_requirements'),
            'password.confirmed' => __('auth.password_mismatch'),
            'password.required' => __('validation.required', ['attribute' => 'Password baru']),
            'current_password.required' => __('validation.required', ['attribute' => 'Password saat ini']),
        ]);

        if (! $user->must_change_password && ! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', __('auth.current_password_wrong'));
            return;
        }

        $user->update([
            'password' => Hash::make($this->password),
            'must_change_password' => false,
        ]);

        AuditLog::record(
            actorId: $user->id,
            action: 'user.password_changed',
            subject: $user,
            ip: request()->ip()
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
