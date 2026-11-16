<?php

use App\Models\JobPosting;
use App\Models\User;

function jmDeadlinePatchData(JobPosting $job, bool $active): array
{
    return [
        'title' => $job->title,
        'description' => 'Maintain an existing employer posting.',
        'requirements' => 'PHP, SQL',
        'preferred_skills' => null,
        'min_experience_years' => 0,
        'min_salary' => 30000,
        'max_salary' => 40000,
        'location' => 'Quezon City',
        'employment_type' => 'full_time',
        'deadline' => $job->deadline->toDateString(),
        'is_active' => $active ? 1 : 0,
    ];
}

test('an employer can close an expired owned job without rewriting its deadline', function () {
    $owner = User::factory()->employer()->create(['is_active' => true]);
    $deadline = now()->subDays(3)->startOfDay();
    $job = JobPosting::factory()->create(['employer_id' => $owner->employer->id, 'deadline' => $deadline, 'is_active' => true]);

    $this->actingAs($owner)->put('/portal/jobs/'.$job->id, jmDeadlinePatchData($job, false))
        ->assertRedirect()->assertSessionHasNoErrors();

    expect($job->fresh()->is_active)->toBeFalse();
    expect($job->fresh()->deadline->toDateString())->toBe($deadline->toDateString());
});

test('an employer cannot reopen an expired job without a valid new deadline', function () {
    $owner = User::factory()->employer()->create(['is_active' => true]);
    $job = JobPosting::factory()->create(['employer_id' => $owner->employer->id, 'deadline' => now()->subDay(), 'is_active' => false]);

    $this->actingAs($owner)->put('/portal/jobs/'.$job->id, jmDeadlinePatchData($job, true))
        ->assertSessionHasErrors('deadline');

    expect($job->fresh()->is_active)->toBeFalse();
});

test('an employer can reopen an expired job after renewing its deadline', function () {
    $owner = User::factory()->employer()->create(['is_active' => true]);
    $job = JobPosting::factory()->create(['employer_id' => $owner->employer->id, 'deadline' => now()->subDay(), 'is_active' => false]);
    $data = jmDeadlinePatchData($job, true);
    $data['deadline'] = now()->addWeek()->toDateString();

    $this->actingAs($owner)->put('/portal/jobs/'.$job->id, $data)
        ->assertRedirect()->assertSessionHasNoErrors();

    expect($job->fresh()->is_active)->toBeTrue();
    expect($job->fresh()->deadline->toDateString())->toBe($data['deadline']);
});

test('closing an expired job still enforces employer ownership', function () {
    $owner = User::factory()->employer()->create(['is_active' => true]);
    $other = User::factory()->employer()->create(['is_active' => true]);
    $job = JobPosting::factory()->create(['employer_id' => $owner->employer->id, 'deadline' => now()->subDay(), 'is_active' => true]);

    $this->actingAs($other)->put('/portal/jobs/'.$job->id, jmDeadlinePatchData($job, false))->assertForbidden();

    expect($job->fresh()->is_active)->toBeTrue();
});

test('new job postings still reject a past deadline even when initially closed', function () {
    $owner = User::factory()->employer()->create(['is_active' => true]);
    $owner->employer->update(['company_name' => 'Example Company', 'industry' => 'Information Technology']);
    $job = JobPosting::factory()->make(['title' => 'Invalid Past Draft', 'deadline' => now()->subDay()]);

    $this->actingAs($owner)->post('/portal/jobs', jmDeadlinePatchData($job, false))->assertSessionHasErrors('deadline');

    $this->assertDatabaseMissing('job_postings', ['title' => 'Invalid Past Draft']);
});

test('the shared Laravel layout registers offline fallback without loading the removed app script', function () {
    $this->get('/')->assertOk()->assertSee('data-jobmatch-offline', false)->assertDontSee('js/jobmatch-app.js', false);
});
