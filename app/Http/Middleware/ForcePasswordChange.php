<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Redirect to password change when must_change_password is true.
     * Allows access to the profile/password page itself.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            // Allow the password change page and logout
            if ($request->routeIs('profile.password') || $request->routeIs('logout')) {
                return $next($request);
            }

            return redirect()->route('profile.password')
                ->with('warning', __('auth.must_change_password'));
        }

        return $next($request);
    }
}
