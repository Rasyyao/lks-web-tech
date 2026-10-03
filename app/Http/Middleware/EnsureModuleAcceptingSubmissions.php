<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleAcceptingSubmissions
{
    /**
     * Check that the module is open and the daily attempt limit isn't reached.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $module = $request->route('module');

        if (! $module) {
            abort(404);
        }

        if (! $module->isOpen()) {
            abort(422, __('modules.not_accepting'));
        }

        $user = $request->user();
        $todayCount = $module->todaySubmissionCount($user);

        if ($todayCount >= $module->max_attempts_per_day) {
            abort(422, __('modules.daily_limit_reached', [
                'limit' => $module->max_attempts_per_day,
            ]));
        }

        return $next($request);
    }
}
