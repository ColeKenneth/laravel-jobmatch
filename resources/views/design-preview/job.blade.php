@php
    $catalog = json_decode(file_get_contents(resource_path('views/design-preview/catalog.json')), true);
    $job = collect(array_merge($catalog['jobs'], $catalog['featuredJobs']))->firstWhere('id', request('job', 'pediatric'));
    $clinicalRoles = [
        'medical-technologist' => ['Medical Technologist I','₱28,000 - ₱36,000','Allied Health','Clinical laboratory testing, specimen handling, and safety protocols.'],
        'pharmacist' => ['Clinical Pharmacist','₱30,000 - ₱40,000','Pharmacy','Clinical pharmacy services, medication review, and patient-centered pharmaceutical care.'],
        'radiologic' => ['Radiologic Technologist','₱29,000 - ₱37,000','Allied Health','Diagnostic imaging services and safe patient positioning for radiologic procedures.'],
        'icu' => ['Staff ICU Nurse – Adult Critical Care','₱35,000 - ₱46,000','Nursing','Patient monitoring, critical-care nursing, and collaboration with the clinical team.'],
    ];
    $extra = $clinicalRoles[request('job')] ?? null;
    if (!$job && $extra) {
        $job = ['id'=>request('job'),'title'=>$extra[0],'salary'=>$extra[1],'description'=>$extra[3],'employer'=>'medical','category'=>'healthcare','location'=>'World Citi Medical Center, Quezon City','type'=>'Full-Time','match'=>94,'tags'=>[$extra[2],'Clinical Practice','Professional Licensure']];
    }
    $job ??= $catalog['jobs'][0];
    $company = collect($catalog['employers'])->firstWhere('id', $job['employer']);
@endphp
@if($job['id'] === 'pediatric')
    @include('design-preview.job-clinical')
@else
<x-jobmatch-public-layout active="jobs">
    <div class="wc-container wc-breadcrumb"><a href="{{ url('/design-preview/home') }}">Home</a> › <a href="{{ url('/design-preview/jobs') }}">Jobs</a> › {{ $job['title'] }}</div>
    <div class="wc-container wc-profile-grid">
        <div class="wc-profile-main"><section class="wc-panel wc-profile-section"><div class="wc-company-heading"><img class="wc-company-icon" src="{{ asset('images/jobmatch/catalog/'.$job['employer'].'.png') }}" alt="" /><div><span class="wc-badge wc-badge-gold">{{ $company['badge'] }}</span><h1 class="wc-detail-title">{{ $job['title'] }}</h1><a href="{{ url('/design-preview/employer?company='.$company['id']) }}">{{ $job['company'] ?? $company['name'] }} →</a></div></div><p>⌖ {{ $job['location'] }} • {{ $job['type'] }}</p><div class="wc-tags">@foreach($job['tags'] as $tag)<span>{{ $tag }}</span>@endforeach</div></section>
        <section class="wc-panel wc-profile-section"><span class="wc-badge wc-badge-gold">✧ {{ $job['match'] }}% Compatibility — sample score</span><h2>Role Overview &amp; Department Vision</h2><p>{{ $job['description'] }}</p><h2>Required Qualifications &amp; Competencies</h2><div class="wc-course">@foreach($job['tags'] as $tag)<span>✓ {{ $tag }}</span>@endforeach</div><p>This detail screen uses sample role information from the supplied designs. The employer must confirm the full requirements before publication.</p></section><section class="wc-panel wc-profile-section"><h2>About {{ $company['name'] }}</h2><p>{{ $company['industry'] }}</p><a class="wc-btn-light" href="{{ url('/design-preview/employer?company='.$company['id']) }}">View Employer Profile →</a></section></div>
        <aside class="wc-profile-sidebar"><section class="wc-panel wc-profile-section"><p class="wc-eyebrow">YOUR NEXT CAREER STEP</p><h2>Apply for this Role</h2><strong>{{ $job['salary'] }} / month</strong><p>Review the sample role and prepare your academic profile.</p><button class="wc-btn" type="button" data-preview-message="This is a frontend review. No application has been sent. The job application workflow still needs to be connected to your backend.">Quick Apply with WCC Profile</button><button type="button" class="wc-btn-light wc-all-openings" data-save="job-{{ $job['id'] }}" aria-pressed="false">♧ Save Job</button></section><section class="wc-panel wc-profile-section"><h2>Employer</h2><a class="wc-related-company" href="{{ url('/design-preview/employer?company='.$company['id']) }}"><img src="{{ asset('images/jobmatch/catalog/'.$company['id'].'.png') }}" alt="" /><strong>{{ $company['name'] }}</strong></a></section><a class="wc-btn-light" href="{{ url('/design-preview/jobs') }}">← Back to All Jobs</a></aside>
    </div>
</x-jobmatch-public-layout>
@endif
