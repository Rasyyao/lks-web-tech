<?php

use App\Http\Middleware\EnforceSelectionFreeze;
use App\Http\Middleware\EnsureAttemptOpen;
use App\Http\Middleware\EnsureAttemptOwner;
use App\Http\Middleware\EnsureModuleAcceptingSubmissions;
use App\Http\Middleware\EnsureModuleVisible;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\ForcePasswordChange;
use App\Http\Middleware\LogAdminAction;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'active' => EnsureUserIsActive::class,
            'password.fresh' => ForcePasswordChange::class,
            'module.visible' => EnsureModuleVisible::class,
            'module.accepting' => EnsureModuleAcceptingSubmissions::class,
            'attempt.owner' => EnsureAttemptOwner::class,
            'attempt.open' => EnsureAttemptOpen::class,
            'locale' => SetLocale::class,
            'audit' => LogAdminAction::class,
            'headers' => SecurityHeaders::class,
            'selection.freeze' => EnforceSelectionFreeze::class,
        ]);

        $middleware->web(append: [
            SecurityHeaders::class,
            SetLocale::class,
        ]);

        // Redirect unauthenticated users to login
        $middleware->redirectGuestsTo('/masuk');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
