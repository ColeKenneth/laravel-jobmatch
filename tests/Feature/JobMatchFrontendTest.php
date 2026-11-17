<?php

use App\Enum\UserRole;
use App\Models\Certification;
use App\Models\Employer;
use App\Models\JobPosting;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function jmStudent(): array
{
    $user = User::factory()->student()->create(['is_active' => true]);

    return [$user, $user->student];
}

function jmCompany(): array
{
    $user = User::factory()->employer()->create(['is_active' => true]);
    $company = $user->employer;
    $company->update(['company_name' => 'Example Company', 'industry' => 'Information Technology']);

    return [$user, $company];
}

function jmJob(Employer $employer, array $attributes = []): JobPosting
{
    return JobPosting::factory()->create(array_merge(['employer_id' => $employer->id, 'is_active' => true, 'deadline' => now()->addMonth(), 'posted_date' => now()], $attributes));
}

test('public frontend renders empty database states without fake jobs or paid map APIs', function (string $url) {
    $this->get($url)->assertOk()->assertDontSee('WCC Google Workspace')->assertDontSee('98.4% Accuracy')->assertDontSee('FRONTEND REVIEW')->assertDontSee('maps.googleapis.com');
})->with(['/', '/jobs', '/skills', '/employers', '/about', '/login', '/register', '/forgot-password']);

test('only open jobs of active employers appear publicly', function () {
    [, $company] = jmCompany();
    $open = jmJob($company, ['title' => 'Open Developer']);
    $closed = jmJob($company, ['title' => 'Closed Developer', 'is_active' => false]);
    $expired = jmJob($company, ['title' => 'Expired Developer', 'deadline' => now()->subDay()]);
    $this->get('/jobs')->assertOk()->assertSee('Open Developer')->assertDontSee('Closed Developer')->assertDontSee('Expired Developer');
    $this->get('/jobs/'.$open->id)->assertOk();
    $this->get('/jobs/'.$closed->id)->assertNotFound();
    $this->get('/jobs/'.$expired->id)->assertNotFound();
});

test('search filters combine keyword employment type experience and posted date', function () {
    [, $company] = jmCompany();
    jmJob($company, ['title' => 'Junior Python Developer', 'employment_type' => 'full_time', 'min_experience_years' => 0]);
    jmJob($company, ['title' => 'Senior Python Developer', 'employment_type' => 'part_time', 'min_experience_years' => 3]);
    $this->get('/jobs?q=Python&type=full_time&level=entry&posted=7')->assertOk()->assertSee('Junior Python Developer')->assertDontSee('Senior Python Developer');
});

test('student dashboard requires authentication and renders the current student', function () {
    $this->get('/dashboard')->assertRedirect('/login');
    [$user] = jmStudent();
    $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee($user->first_name)->assertDontSee('Ralph M. Malicsi')->assertDontSee('95% Match');
});

test('student portal pages render with the supplied database schema', function (string $url) {
    [$user] = jmStudent();
    $this->actingAs($user)->get($url)->assertOk();
})->with(['/portal/profile', '/portal/resume', '/portal/skills', '/portal/certifications', '/portal/placements']);

test('profile update saves identity and student education atomically', function () {
    [$user, $student] = jmStudent();
    $this->actingAs($user)->put('/portal/profile', ['first_name' => 'John', 'middle_name' => null, 'last_name' => 'Gadier', 'email' => $user->email, 'student_id_number' => $student->student_id_number, 'program' => 'BSIT', 'year_level' => 'fourth_year', 'gwa' => 1.75])->assertSessionHasNoErrors()->assertRedirect();
    expect($user->fresh()->first_name)->toBe('John');
    expect($student->fresh()->program)->toBe('BSIT');
});

test('student skills are saved and cannot be removed by another student', function () {
    [$owner, $student] = jmStudent();
    [$other] = jmStudent();
    $skill = Skill::factory()->create();
    $this->actingAs($owner)->post('/portal/skills', ['skill_id' => $skill->id, 'proficiency_level' => 4, 'years_of_experience' => 2])->assertSessionHasNoErrors()->assertRedirect();
    $record = $student->studentSkills()->firstOrFail();
    $this->actingAs($other)->delete('/portal/skills/'.$record->id)->assertNotFound();
    $this->assertDatabaseHas('student_skills', ['id' => $record->id]);
    $this->actingAs($owner)->delete('/portal/skills/'.$record->id)->assertRedirect();
    $this->assertDatabaseMissing('student_skills', ['id' => $record->id]);
});

test('certificate ownership is enforced for deletions', function () {
    [$owner, $student] = jmStudent();
    [$other] = jmStudent();
    $this->actingAs($owner)->post('/portal/certifications', ['name' => 'CCNA', 'issuing_authority' => 'Cisco', 'date_issued' => '2026-01-01'])->assertSessionHasNoErrors();
    $id = Certification::where('student_id', $student->id)->firstOrFail()->id;
    $this->actingAs($other)->delete('/portal/certifications/'.$id)->assertNotFound();
});

test('resume uploads use private storage and download only the current students file', function () {
    Storage::fake('local');
    [$owner, $student] = jmStudent();
    [$other] = jmStudent();
    $this->actingAs($owner)->post('/portal/resume', ['resume' => UploadedFile::fake()->create('resume.pdf', 20, 'application/pdf')])->assertSessionHasNoErrors();
    $path = $student->fresh()->resume_file_path;
    Storage::disk('local')->assertExists($path);
    $this->actingAs($owner)->get('/portal/resume/download')->assertDownload('resume.pdf');
    $this->actingAs($other)->get('/portal/resume/download')->assertNotFound();
});

test('employer can create a job but cannot modify another employers job', function () {
    [$owner, $company] = jmCompany();
    [$other] = jmCompany();
    $data = ['title' => 'Backend Developer', 'description' => 'Build APIs', 'requirements' => 'PHP', 'min_experience_years' => 0, 'min_salary' => 30000, 'max_salary' => 40000, 'location' => 'Quezon City', 'employment_type' => 'full_time', 'deadline' => now()->addMonth()->toDateString(), 'is_active' => 1];
    $this->actingAs($owner)->post('/portal/jobs', $data)->assertSessionHasNoErrors();
    $job = JobPosting::where('employer_id', $company->id)->firstOrFail();
    $this->actingAs($other)->put('/portal/jobs/'.$job->id, $data + ['title' => 'Changed'])->assertForbidden();
    expect($job->fresh()->title)->toBe('Backend Developer');
});

test('students cannot use employer job management or staff reports', function () {
    [$user] = jmStudent();
    $this->actingAs($user)->get('/portal/jobs')->assertForbidden();
    $this->get('/portal/reports')->assertForbidden();
});

test('inactive users cannot open the portal', function () {
    $user = User::factory()->create(['is_active' => false]);
    $this->actingAs($user)->get('/dashboard')->assertForbidden();
});

test('staff can export actual placement records', function () {
    $user = User::factory()->admin()->create(['is_active' => true]);
    $this->actingAs($user)->get('/portal/reports')->assertOk()->assertDownload('jobmatch-placement-report.csv');
});

test('staff roles cannot be obtained through public signup', function (string $role) {
    $this->post('/register', ['first_name' => 'Test', 'last_name' => 'User', 'email' => 'staff@example.com', 'password' => 'password', 'password_confirmation' => 'password', 'role' => $role])->assertSessionHasErrors('role');
    $this->assertGuest();
})->with(['admin', 'placement_officer']);

test('all account roles render their own dashboard', function (string $role) {
    $user = User::factory()->create(['role' => UserRole::from($role), 'is_active' => true]);
    $this->actingAs($user)->get('/dashboard')->assertOk();
})->with(['student', 'alumni', 'employer', 'admin', 'placement_officer']);

test('employer profile and job forms render database records', function () {
    [$user, $company] = jmCompany();
    jmJob($company);
    $this->actingAs($user)->get('/portal/company')->assertOk();
    $this->get('/portal/jobs')->assertOk();
});

test('about keeps the embedded campus map and offers an external Google Maps link', function () {
    $this->get('/about')->assertOk()->assertSee('www.openstreetmap.org/export/embed.html', false)->assertSee('https://www.google.com/maps/search/?api=1', false)->assertSee('Open in Google Maps');
});
