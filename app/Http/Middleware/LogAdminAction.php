<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAdminAction
{
    /**
     * Log mutating actions on mentor and admin routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log mutating requests that succeeded
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])
            && $response->isSuccessful()) {
            AuditLog::record(
                action: $request->method() . ' ' . $request->path(),
                meta: [
                    'route' => $request->route()?->getName(),
                    'method' => $request->method(),
                ],
            );
        }

        return $response;
    }
}
