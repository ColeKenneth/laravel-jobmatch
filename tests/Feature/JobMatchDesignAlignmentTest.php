<?php

use App\Models\Certification;
use App\Models\Employer;
use App\Models\JobPosting;
use App\Models\JobSkill;
use App\Models\PlacementRecord;
use App\Models\Skill;
use App\Models\User;
use App\Services\ProfileCompletionService;
use Database\Seeders\JobMatchDesignSampleSeeder;

function designCompany(array $attributes = []): Employer
{
    $user = User::factory()->employer()->create(['is_active' => true]);
    $company = $user->employer;
    $company->update(array_merge(['company_name' => 'Design Company', 'industry' => 'IT & Software', 'location' => 'Quezon City', 'company_size' => '51-200'], $attributes));

    return $company;
}

function designJob(Employer $company, array $attributes = []): JobPosting
{
    return JobPosting::factory()->create(array_merge(['employer_id' => $company->id, 'is_active' => true, 'deadline' => now()->addMonth(), 'posted_date' => now()], $attributes));
}

test('the job directory shows the design chips and applies mid and senior experience ranges', function () {
    $company = designCompany();
    designJob($company, ['title' => 'Mid Range Role', 'min_experience_years' => 2]);
    designJob($company, ['title' => 'Senior Range Role', 'min_experience_years' => 4]);
    $this->get('/jobs?level=mid')->assertOk()->assertSee('Mid Range Role')->assertDontSee('Senior Range Role')->assertSee('Job Type')->assertSee('Last 24h');
    $this->get('/jobs?level=senior')->assertOk()->assertSee('Senior Range Role')->assertDontSee('Mid Range Role');
});

test('skill discipline source and type filters are combined instead of ignored', function () {
    Skill::factory()->create(['name' => 'Course Python', 'category' => 'technical', 'discipline' => 'programming', 'source' => 'courses']);
    Skill::factory()->create(['name' => 'Experience Python', 'category' => 'technical', 'discipline' => 'programming', 'source' => 'experience']);
    $this->get('/skills?discipline=programming&source=courses&category=technical')->assertOk()->assertSee('Course Python')->assertDontSee('Experience Python')->assertSee('Skill Type');
});

test('skill usage counts include only active unexpired jobs from active employers', function () {
    $company = designCompany();
    $skill = Skill::factory()->create(['name' => 'PHP']);
    $open = designJob($company);
    $closed = designJob($company, ['is_active' => false]);
    foreach ([$open, $closed] as $job) {
        JobSkill::create(['job_posting_id' => $job->id, 'skill_id' => $skill->id, 'is_required' => true, 'min_proficiency_level' => 1, 'priority' => 1]);
    }
    $this->get('/skills')->assertOk()->assertSee('Used by 1 active job posting.');
});

test('employer city size and industry filters match stored directory fields', function () {
    designCompany(['company_name' => 'Matching Company']);
    designCompany(['company_name' => 'Different City', 'location' => 'Cebu']);
    designCompany(['company_name' => 'Different Size', 'company_size' => '500+']);
    $this->get('/employers?industry=IT%20%26%20Software&location=Quezon%20City&size=51-200')->assertOk()->assertSee('Matching Company')->assertDontSee('Different City')->assertDontSee('Different Size');
});

test('show more jobs is a real next page preserving active search filters', function () {
    $company = designCompany();
    for ($i = 0; $i < 4; $i++) {
        designJob($company, ['title' => 'Python Role '.$i]);
    }
    $this->get('/jobs?q=Python')->assertOk()->assertSee('Show More Jobs')->assertSee('q=Python&amp;page=2', false);
    $this->get('/jobs?q=Python&page=2')->assertOk()->assertDontSee('Show More Jobs');
});

test('profile completion reflects saved fields rather than the sample 85 percent', function () {
    $user = User::factory()->student()->create(['is_active' => true]);
    $student = $user->student;
    $student->update(['program' => 'BS Information Technology', 'gwa' => 1.75]);
    $completion = app(ProfileCompletionService::class)->calculate($user, $student, 1, false);
    expect($completion['percent'])->toBe(85);
    expect(array_column($completion['missing'], 'label'))->toContain('Upload detailed resume', 'Add a certification verification URL');
    $student->update(['resume_file_path' => 'resumes/'.$user->id.'/resume.pdf']);
    expect(app(ProfileCompletionService::class)->calculate($user, $student, 1, true)['percent'])->toBe(100);
});

test('student dashboard includes the completion ring and real skill and certificate summaries', function () {
    $user = User::factory()->student()->create(['is_active' => true]);
    $student = $user->student;
    $skill = Skill::factory()->create(['name' => 'Python']);
    $student->studentSkills()->create(['skill_id' => $skill->id, 'proficiency_level' => 4, 'years_of_experience' => 3]);
    $company = designCompany();
    $job = designJob($company, ['title' => 'Python Opportunity']);
    JobSkill::create(['job_posting_id' => $job->id, 'skill_id' => $skill->id, 'is_required' => true, 'min_proficiency_level' => 3, 'priority' => 1]);
    Certification::create(['student_id' => $student->id, 'name' => 'CCNA', 'issuing_authority' => 'Cisco', 'date_issued' => now()]);
    $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('Profile Completion')->assertSee('role="progressbar"', false)->assertSee('Total: 1 Skill')->assertSee('Advanced')->assertSee('CCNA')->assertSee('Python Opportunity')->assertSee('1 shared skill')->assertSee('data-dashboard-search', false)->assertDontSee('95% Match');
});

test('dashboard status links filter only the current students placements', function () {
    $user = User::factory()->student()->create(['is_active' => true]);
    $other = User::factory()->student()->create(['is_active' => true]);
    $company = designCompany();
    $pending = designJob($company, ['title' => 'Owned Pending Role']);
    $hired = designJob($company, ['title' => 'Owned Hired Role']);
    $foreign = designJob($company, ['title' => 'Other Student Role']);
    foreach ([[$user, $pending, 'pending'], [$user, $hired, 'hired'], [$other, $foreign, 'pending']] as [$person, $job, $status]) {
        PlacementRecord::create(['student_id' => $person->student->id, 'job_posting_id' => $job->id, 'placement_date' => now(), 'placement_status' => $status]);
    }
    $this->actingAs($user)->get('/portal/placements?status=pending')->assertOk()->assertSee('Owned Pending Role')->assertDontSee('Owned Hired Role')->assertDontSee('Other Student Role');
});

test('sample fixtures are explicit local only and idempotent', function () {
    $this->app->detectEnvironment(fn () => 'local');
    $this->seed(JobMatchDesignSampleSeeder::class);
    $counts = [User::count(), Employer::count(), JobPosting::count(), Skill::count()];
    $this->seed(JobMatchDesignSampleSeeder::class);
    expect([User::count(), Employer::count(), JobPosting::count(), Skill::count()])->toBe($counts);
    $this->get('/jobs')->assertOk()->assertSee('Data Analyst');
    $this->get('/skills')->assertOk()->assertSee('Artificial Intelligence')->assertSee('Public Speaking')->assertSee('Project Management');
    $job = JobPosting::firstOrFail();
    $this->get('/jobs/'.$job->id)->assertOk()->assertSee('Salary not specified');
});

test('sample fixtures cannot be run in production', function () {
    $this->app->detectEnvironment(fn () => 'production');
    expect(fn () => app(JobMatchDesignSampleSeeder::class)->run())->toThrow(LogicException::class);
    expect(User::count())->toBe(0);
});

test('portal job cards use the same open job rule as the public directory', function () {
    $student = User::factory()->student()->create(['is_active' => true]);
    $company = designCompany();
    $inactiveCompany = designCompany(['company_name' => 'Inactive Employer']);
    $inactiveCompany->user->update(['is_active' => false]);

    designJob($company, ['title' => 'Visible Portal Opportunity']);
    designJob($company, ['title' => 'Closed Portal Opportunity', 'is_active' => false]);
    designJob($company, ['title' => 'Expired Portal Opportunity', 'deadline' => now()->subDay()]);
    designJob($inactiveCompany, ['title' => 'Inactive Employer Opportunity']);

    $this->actingAs($student)->get('/dashboard')
        ->assertOk()
        ->assertSee('Visible Portal Opportunity')
        ->assertDontSee('Closed Portal Opportunity')
        ->assertDontSee('Expired Portal Opportunity')
        ->assertDontSee('Inactive Employer Opportunity');
});
