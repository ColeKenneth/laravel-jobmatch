<?php

namespace App\Http\Middleware;

use App\Enum\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Allow any listed role, for example role:admin,placement_officer.
     * Invalid or empty role lists fail closed; admin has no implicit bypass.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user, Response::HTTP_UNAUTHORIZED, 'Unauthorized');

        $allowedRoles = [];

        foreach ($roles as $role) {
            $allowedRole = UserRole::tryFrom($role);

            abort_if(
                $allowedRole === null,
                Response::HTTP_FORBIDDEN,
                'You do not have permission to access this page.',
            );

            $allowedRoles[] = $allowedRole;
        }

        abort_unless(
            $user->is_active && in_array($user->role, $allowedRoles, true),
            Response::HTTP_FORBIDDEN,
            'You do not have permission to access this page.',
        );

        return $next($request);
    }
}
