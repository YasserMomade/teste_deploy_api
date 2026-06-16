<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
 ->withMiddleware(function (Middleware $middleware) {
    $middleware->redirectGuestsTo(fn () => null);

    $middleware->alias([
        'active.user' => \App\Http\Middleware\EnsureUserIsActive::class,
        'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        'audit.context' => \App\Http\Middleware\SetAuditContext::class,
    ]);
    
    $middleware->preventRequestForgery(except: [
        'api/v1/webhook/stripe',
    ]);
})
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
        });
    })->create();