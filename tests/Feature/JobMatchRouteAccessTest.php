<?php

use App\Enum\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Exercise actual endpoints so typed URLs are protected, not only hidden links.
test('portal endpoints enforce the route access matrix', function (string $role, string $path, bool $allowed) {
    $user = User::factory()->create(['role' => $role, 'is_active' => true]);
    $response = $this->actingAs($user)->get($path);

    if ($allowed) {
        $response->assertOk();
    } else {
        $response->assertForbidden();
    }
})->with(function () {
    $access = [
        '/dashboard' => ['student', 'alumni', 'employer', 'admin', 'placement_officer'],
        '/portal/profile' => ['student', 'alumni', 'employer', 'admin', 'placement_officer'],
        '/portal/resume' => ['student'],
        '/portal/skills' => ['student'],
        '/portal/certifications' => ['student'],
        '/portal/company' => ['employer'],
        '/portal/jobs' => ['employer'],
        '/portal/placements' => ['student', 'employer', 'admin', 'placement_officer'],
        '/portal/reports' => ['admin', 'placement_officer'],
    ];

    foreach (UserRole::cases() as $role) {
        foreach ($access as $path => $roles) {
            yield $role->value.' '.$path => [$role->value, $path, in_array($role->value, $roles, true)];
        }
    }
});

test('non-admin roles cannot access the admin Alumni URL directly', function (string $role) {
    $user = User::factory()->create(['role' => $role, 'is_active' => true]);

    $this->actingAs($user)->get('/admin/alumni')->assertForbidden();
})->with(['student', 'alumni', 'employer', 'placement_officer']);

test('guests must log in before entering protected portal pages', function (string $path) {
    $this->get($path)->assertRedirect(route('login'));
})->with(['/dashboard', '/portal/profile', '/portal/skills', '/portal/company', '/portal/reports', '/admin/alumni']);

test('inactive accounts cannot enter shared role pages or role-specific pages', function (string $role, string $path) {
    $user = User::factory()->create(['role' => $role, 'is_active' => false]);

    $this->actingAs($user)->get('/dashboard')->assertForbidden();
    $this->get($path)->assertForbidden();
})->with([
    ['student', '/portal/skills'],
    ['alumni', '/portal/profile'],
    ['employer', '/portal/company'],
    ['admin', '/admin/alumni'],
    ['placement_officer', '/portal/reports'],
]);

test('wrong roles cannot submit state-changing requests', function (string $role, string $method, string $path) {
    $user = User::factory()->create(['role' => $role, 'is_active' => true]);

    $this->actingAs($user)->call($method, $path)->assertForbidden();
})->with([
    ['student', 'POST', '/portal/jobs'],
    ['alumni', 'POST', '/portal/skills'],
    ['alumni', 'PUT', '/portal/company'],
    ['employer', 'POST', '/portal/certifications'],
    ['admin', 'POST', '/portal/jobs'],
    ['placement_officer', 'DELETE', '/portal/certifications/1'],
]);

test('read write delete and download routes carry explicit role middleware', function () {
    $studentRoutes = [
        'portal.resume', 'portal.resume.upload', 'portal.resume.download',
        'portal.skills', 'portal.skills.save', 'portal.skills.delete',
        'portal.certifications', 'portal.certifications.save', 'portal.certifications.delete',
    ];
    $employerRoutes = ['portal.company', 'portal.company.update', 'portal.jobs', 'portal.jobs.save', 'portal.jobs.update'];

    foreach ($studentRoutes as $name) {
        expect(Route::getRoutes()->getByName($name)->gatherMiddleware())->toContain('role:student');
    }

    foreach ($employerRoutes as $name) {
        expect(Route::getRoutes()->getByName($name)->gatherMiddleware())->toContain('role:employer');
    }

    expect(Route::getRoutes()->getByName('portal.reports')->gatherMiddleware())->toContain('role:admin,placement_officer');
    expect(Route::getRoutes()->getByName('admin.alumni')->gatherMiddleware())->toContain('role:admin');
});
