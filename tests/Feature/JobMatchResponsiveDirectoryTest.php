<?php

use App\Enum\CompanySize;
use App\Models\User;

function responsiveCompany(string $name, string $industry, string $size): void
{
    $user = User::factory()->employer()->create(['is_active' => true]);
    $user->employer->update(['company_name' => $name, 'industry' => $industry, 'company_size' => $size]);
}

test('home replaces dashboard text in portal navigation and browser title', function () {
    $user = User::factory()->student()->create(['is_active' => true]);
    $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('<title>Home | WCC JobMatchAI</title>', false)->assertDontSee('>Dashboard</a>', false)->assertSee('>Home</a>', false);
});

test('government and call center aliases are searchable by industry', function () {
    responsiveCompany('City Office', 'public sector', '51-200');
    responsiveCompany('Customer Support Company', 'Call Center', '501-1000');
    $this->get('/employers?industry=Government')->assertOk()->assertSee('City Office')->assertDontSee('Customer Support Company');
    $this->get('/employers?industry=BPO%20%26%20Call%20Center')->assertOk()->assertSee('Customer Support Company')->assertDontSee('City Office');
});

test('old broad size filters include newly detailed buckets without guessing unknown counts', function () {
    responsiveCompany('Small Employer', 'Education', '11-50');
    responsiveCompany('Large Employer', 'Government', '1001-5000');
    responsiveCompany('Legacy Large Employer', 'Healthcare', '500+');
    $this->get('/employers?size=1-50')->assertOk()->assertSee('Small Employer')->assertDontSee('Large Employer');
    $this->get('/employers?size=500%2B')->assertOk()->assertSee('Large Employer')->assertSee('Legacy Large Employer')->assertDontSee('Small Employer');
    $this->get('/employers?size=1001-5000')->assertOk()->assertSee('Large Employer')->assertDontSee('Legacy Large Employer');
});

test('all new size ranges can be selected and saved in company profile', function (string $size) {
    $user = User::factory()->employer()->create(['is_active' => true]);
    $data = ['company_name' => 'Registered Employer', 'industry' => 'BPO & Call Center', 'location' => 'Quezon City', 'company_size' => $size, 'logo_url' => 'https://example.test/logo.png'];
    $this->actingAs($user)->put('/portal/company', $data)->assertSessionHasNoErrors()->assertRedirect();
    expect($user->employer->fresh()->company_size)->toBe($size);
    $this->get('/employers?'.http_build_query(['size' => $size]))->assertOk()->assertSee('Registered Employer');
})->with(array_map(fn ($size) => $size->value, CompanySize::cases()));

test('footer uses the WCC wordmark on a transparent background and keeps campus navigation', function () {
    $response = $this->get('/');
    $response->assertOk()->assertSee('class="footer-logo"', false)->assertSee('id="page-top"', false)->assertSee('Back to top')->assertSee('Open campus map');
    expect(substr_count($response->getContent(), 'images/jobmatch/current/wcc-logo.png'))->toBe(1);
    $response->assertSee('images/jobmatch/current/wcc-footer-transparent.png');
});
