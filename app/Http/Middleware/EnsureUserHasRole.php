<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{

    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            abort(Response::HTTP_UNAUTHORIZED, "Unauthorized");
        }

        if ($request->user()->role->value !== $role) {
            abort(Response::HTTP_FORBIDDEN, "You do not have permission to access this page.");
        }

        return $next($request);
    }
}
