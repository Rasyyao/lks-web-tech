<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('username', 'remember'))
                ->withErrors(['username' => __('auth.failed')]);
        }

        $user = Auth::user();

        // Check if user is active
        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();

            return back()
                ->withInput($request->only('username', 'remember'))
                ->withErrors(['username' => __('auth.account_inactive')]);
        }

        $request->session()->regenerate();

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Redirect to password change if needed
        if ($user->must_change_password) {
            return redirect()->route('profile.password');
        }

        // Honour the page the user originally asked for, but never send them
        // somewhere their role forbids (e.g. a mentor bounced to /admin),
        // and ignore trivial landing pages (root or login form).
        $intended = $request->session()->pull('url.intended');
        $intendedPath = $intended ? trim((string) parse_url($intended, PHP_URL_PATH), '/') : '';

        if ($intended && $intendedPath !== '' && $intendedPath !== 'masuk' && $user->canVisitPath($intendedPath)) {
            return redirect()->to($intended);
        }

        return redirect()->to($user->homeUrl());
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
