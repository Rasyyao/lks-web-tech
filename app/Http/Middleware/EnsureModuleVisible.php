<?php

namespace App\Http\Middleware;

use App\Enums\ModuleStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleVisible
{
    /**
     * Check that the module is published and the student's cohort matches.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $module = $request->route('module');

        if (! $module) {
            abort(404);
        }

        // Must be published
        if ($module->status !== ModuleStatus::Published) {
            abort(404);
        }

        $user = $request->user();

        // Mentors and admins can always see published modules
        if ($user->hasRole(['mentor', 'admin'])) {
            return $next($request);
        }

        // Check cohort visibility
        if (! $module->isVisibleTo($user)) {
            abort(404);
        }

        return $next($request);
    }
}
