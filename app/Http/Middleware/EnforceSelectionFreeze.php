<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceSelectionFreeze
{
    /**
     * Placeholder for R3 selection mode: passes through in v1.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // R3: check if selection mode is active and freeze leaderboard
        return $next($request);
    }
}
