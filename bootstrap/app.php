<?php

use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/**')) {
                return response()->json(['message' => 'Resource not found.'], $e->getCode());
            }
        });

        $exceptions->render(function (BusinessRuleException $e, Request $request) {
            if ($request->is('api/**')) {
                return response()->json(['message' => $e->getMessage()], $e->getCode());
            }
        });

        $exceptions->render(function (AlreadyExistsException $e, Request $request)  {
            if ($request->is('api/**')) {
                return response()->json(['message' => $e->getMessage()], $e->getCode());
            }
        });
    })->create();
