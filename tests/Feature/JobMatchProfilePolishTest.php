<?php

use App\Enum\UserRole;
use App\Models\User;

function polishedEmployer(string $name, string $size): void
{
    $user = User::factory()->employer()->create(['is_active' => true]);
    $user->employer->update(['company_name' => $name, 'industry' => 'Government', 'company_size' => $size]);
}

test('profile renders appropriate personal and academic sections for each role', function (string $role) {
    $user = User::factory()->create(['role' => UserRole::from($role), 'is_active' => true]);
    $response = $this->actingAs($user)->get('/portal/profile')->assertOk()->assertSee($user->full_name)->assertSee('Personal information')->assertSee('data-profile-form', false)->assertSee('Save Profile');
    if (in_array($role, ['student', 'alumni'], true)) {
        $response->assertSee('Educational background')->assertSee('name="program"', false);
    } else {
        $response->assertDontSee('id="education-information"', false);
    }
})->with(['student', 'alumni', 'employer', 'admin', 'placement_officer']);

test('profile rejects invalid details and retains entered field values', function () {
    $user = User::factory()->alumni()->create(['is_active' => true]);
    $this->actingAs($user)->from('/portal/profile')->put('/portal/profile', ['first_name' => 'Updated Name', 'last_name' => $user->last_name, 'email' => 'invalid-email', 'program' => 'BSIT', 'graduation_date' => '2025-01-01'])->assertSessionHasErrors('email')->assertRedirect('/portal/profile');
    $this->get('/portal/profile')->assertOk()->assertSee('Updated Name')->assertSee('invalid-email');
    expect($user->fresh()->first_name)->not->toBe('Updated Name');
});

test('directory company size is one compact dropdown with broad groups', function () {
    $response = $this->get('/employers')->assertOk()->assertSee('id="directory-company-size"', false)->assertSee('1–500 employees')->assertSee('500+ employees');
    expect(substr_count($response->getContent(), 'name="size"'))->toBe(2);
    expect($response->getContent())->not->toContain('size=501-1000');
});

test('one to five hundred filter includes legacy small and medium companies', function () {
    polishedEmployer('Small Team', '11-50');
    polishedEmployer('Medium Team', '201-500');
    polishedEmployer('Broad Small Team', '1-500');
    polishedEmployer('Large Team', '501-1000');
    $this->get('/employers?size=1-500')->assertOk()->assertSee('Small Team')->assertSee('Medium Team')->assertSee('Broad Small Team')->assertDontSee('Large Team');
});

test('legacy precise size links remain usable through the compact selector', function () {
    polishedEmployer('Precise Team', '51-200');
    $this->get('/employers?size=51-200')->assertOk()->assertSee('Precise Team')->assertSee('value="51-200" selected', false);
});
