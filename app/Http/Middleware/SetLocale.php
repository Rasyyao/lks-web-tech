<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Set the application locale to Indonesian.
     */
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale('id');

        return $next($request);
    }
}
