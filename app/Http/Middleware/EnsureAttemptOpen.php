<?php

namespace App\Http\Middleware;

use App\Services\Practice\AttemptScorer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAttemptOpen
{
    /**
     * Ensure the attempt is still open (not submitted and timer not expired).
     * Auto-submits if the timer has expired.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $attempt = $request->route('attempt');

        if (! $attempt) {
            abort(404);
        }

        // Already submitted — redirect to result
        if ($attempt->submitted_at !== null) {
            return redirect()->route('practice.result', $attempt);
        }

        // Timer expired — auto-submit and redirect
        if ($attempt->isTimerExpired()) {
            app(AttemptScorer::class)->score($attempt);

            return redirect()->route('practice.result', $attempt);
        }

        return $next($request);
    }
}
