<?php

use App\Http\Middleware\EnsureUserHasRole;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

function roleGuardResponse(?User $user, string $requiredRole): Response
{
    $request = Request::create('/admin/alumni');
    $request->setUserResolver(fn () => $user);

    return (new EnsureUserHasRole)->handle(
        $request,
        fn () => new Response('Allowed'),
        $requiredRole,
    );
}

test('the route string admin authorizes an active admin enum', function () {
    $user = User::factory()->admin()->create(['is_active' => true]);

    expect(roleGuardResponse($user, 'admin')->getStatusCode())->toBe(200);
});

test('role middleware rejects guests', function () {
    try {
        roleGuardResponse(null, 'admin');
        $this->fail('A guest must not enter an admin route.');
    } catch (HttpException $exception) {
        expect($exception->getStatusCode())->toBe(401);
    }
});

test('role middleware fails closed for wrong roles inactive users and unknown roles', function (string $role, bool $active, string $requiredRole) {
    $user = User::factory()->create(['role' => $role, 'is_active' => $active]);

    try {
        roleGuardResponse($user, $requiredRole);
        $this->fail('This account must not enter the protected route.');
    } catch (HttpException $exception) {
        expect($exception->getStatusCode())->toBe(403);
    }
})->with([
    ['student', true, 'admin'],
    ['alumni', true, 'admin'],
    ['employer', true, 'admin'],
    ['placement_officer', true, 'admin'],
    ['admin', false, 'admin'],
    ['admin', true, 'unknown-role'],
]);

test('a multi-role guard authorizes each explicitly listed staff role', function (string $role) {
    $user = User::factory()->create(['role' => $role, 'is_active' => true]);
    $request = Request::create('/portal/reports');
    $request->setUserResolver(fn () => $user);

    $response = (new EnsureUserHasRole)->handle(
        $request,
        fn () => new Response('Allowed'),
        'admin',
        'placement_officer',
    );

    expect($response->getStatusCode())->toBe(200);
})->with(['admin', 'placement_officer']);

test('empty lists and mixed valid-invalid lists fail closed', function (array $roles) {
    $user = User::factory()->admin()->create(['is_active' => true]);
    $request = Request::create('/portal/reports');
    $request->setUserResolver(fn () => $user);

    try {
        (new EnsureUserHasRole)->handle($request, fn () => new Response('Allowed'), ...$roles);
        $this->fail('An invalid or empty role declaration must deny access.');
    } catch (HttpException $exception) {
        expect($exception->getStatusCode())->toBe(403);
    }
})->with([
    'no roles' => [[]],
    'unknown role after admin' => [['admin', 'unknown-role']],
]);
