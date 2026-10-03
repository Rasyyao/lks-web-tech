<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAttemptOwner
{
    /**
     * Ensure the practice attempt belongs to the authenticated user.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $attempt = $request->route('attempt');

        if (! $attempt || $attempt->user_id !== $request->user()->id) {
            abort(404);
        }

        return $next($request);
    }
}
