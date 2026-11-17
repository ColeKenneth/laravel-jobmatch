@php($catalog = json_decode(file_get_contents(resource_path('views/design-preview/catalog.json')), true))
<x-jobmatch-public-layout active="skills">
    <div data-catalog="skills">
        <div class="wc-taxonomy-strip"><div class="wc-container">Institutional Portal › Curricular Career Pathways › <strong>Skill Ontology Framework 2024–2025</strong><span>CHED &amp; Industry ATS Aligned</span></div></div>
        <section class="wc-container wc-skills-intro">
            <div class="wc-heading-line"><div><p class="wc-eyebrow">✧ INSTITUTIONAL TAXONOMY ENGINE</p><h1>WCC Skill Ontology &amp; Career Competencies</h1><p>Explore high-demand technical competencies, clinical certifications, and professional skills cross-mapped with World Citi Colleges academic curricula and employer demand.</p></div><div class="wc-mini-stats"><div><strong>5 Colleges</strong><small>Fully Mapped</small></div><div><strong>1,840+</strong><small>Verified Openings</small></div><div><strong>96.4%</strong><small>Placement Match</small></div></div></div>
            <form class="wc-panel wc-skills-search" data-search-form><div class="wc-search-composite"><label><input type="search" data-filter="query" name="q" placeholder="Live search competencies (e.g., Pediatric Care, React.js, Flight Dispatch)..." aria-label="Search competencies" /></label><label><select data-filter="category" aria-label="College or discipline"><option value="">All WCC Colleges &amp; Disciplines</option><option value="healthcare">Nursing &amp; Allied Health</option><option value="it">Information Technology</option><option value="aviation">Aviation &amp; Operations</option><option value="business">Business &amp; Accountancy</option></select></label><label><select data-filter="sort" aria-label="Sort skills"><option value="original">Figma order</option><option value="demand">Highest Market Demand</option><option value="name">Competency A–Z</option></select></label><button class="wc-btn-light" data-reset type="button">↶ Reset</button></div><div class="wc-chip-line"><span>Quick Filter:</span>@foreach(['' => 'All Competencies', 'healthcare' => 'Nursing & Allied Health', 'it' => 'Information Technology', 'aviation' => 'Aviation & Operations', 'business' => 'Business & Accountancy'] as $key => $label)<button type="button" data-category-chip="{{ $key }}" aria-pressed="{{ $key === '' ? 'true' : 'false' }}">{{ $label }}</button>@endforeach</div></form>
        </section>
        <section class="wc-container wc-section"><div class="wc-heading-line"><h2>▦ Core Academic Competency Clusters</h2><p>Validated against CHED CMO Standards &amp; WCC Syllabi</p></div><div class="wc-cluster-grid">
            @foreach([
                ['healthcare','Clinical & Nursing Care','BSN MAPPED','Pediatric resuscitation, IV therapy, clinical EHR, triage, pharmacology.','412','98','medical'],
                ['it','Software & Cloud Systems','BSIT / BSCS MAPPED','React/Next.js, Node APIs, Python data analytics, PostgreSQL, AWS deploy.','328','95','orangeapps'],
                ['aviation','Aviation & Flight Ops','AERONAUTICAL MAPPED','Flight dispatch, ICAO safety compliance, aviation meteorology, ramp ops.','189','92','aviation'],
                ['business','Business & Finance','BSBA / BSA MAPPED','Financial statement audit, payroll, BIR compliance, talent acquisition.','275','89','fintech'],
                ['healthcare','Allied Health Diagnostics','BSMT / BSRT MAPPED','Phlebotomy, clinical lab safety, specimen prep, radiography protocols.','214','94','medical']
            ] as $cluster)
                <button class="wc-panel wc-cluster" type="button" data-category-chip="{{ $cluster[0] }}"><img src="{{ asset('images/jobmatch/catalog/'.$cluster[6].'.png') }}" alt="" /><small>{{ $cluster[2] }}</small><h3>{{ $cluster[1] }}</h3><p>{{ $cluster[3] }}</p><div><strong>{{ $cluster[4] }} Openings</strong><span>✧ {{ $cluster[5] }}% Match</span></div></button>
            @endforeach
        </div></section>
        <section class="wc-container wc-section" aria-labelledby="skills-heading"><div class="wc-heading-line"><div><h2 id="skills-heading">Verified Curricular Competencies</h2><p>Employer-requested competencies represented in your Figma design.</p></div><span class="wc-badge" data-result-count aria-live="polite">Showing 8 of 8 sample competencies</span></div>
            <div class="wc-skill-grid" data-record-list>
                @foreach($catalog['skills'] as $skill)
                    <article class="wc-panel wc-skill-card" data-record data-name="{{ $skill['title'] }}" data-search="{{ $skill['title'].' '.$skill['subtitle'].' '.$skill['college'].' '.$skill['course'].' '.implode(' ', $skill['partners']) }}" data-category="{{ $skill['category'] }}" data-demand="{{ $skill['openings'] }}">
                        <div class="wc-heading-line"><span class="wc-badge">{{ $skill['college'] }}</span><span class="wc-badge wc-badge-gold">{{ $skill['badge'] }}</span></div><h3>{{ $skill['title'] }}</h3><p>{{ $skill['subtitle'] }}</p><div class="wc-course"><span>♧ {{ $skill['course'] }}</span><span>✓ {{ $skill['practice'] }}</span></div><dl class="wc-skill-stats"><div><dt>Active Metro QC Jobs:</dt><dd>{{ $skill['openings'] }} Openings</dd></div><div><dt>Avg. Base Salary:</dt><dd>{{ $skill['salary'] }}</dd></div></dl><small class="wc-subheading">TOP HIRING PARTNERS</small><div class="wc-tags">@foreach($skill['partners'] as $partner)<span>{{ $partner }}</span>@endforeach</div><a class="wc-btn-light" href="{{ url('/design-preview/jobs?category='.$skill['category']) }}">View Matching Roles →</a>
                    </article>
                @endforeach
            </div><div class="wc-panel wc-empty" data-empty hidden><h3>No matching competencies</h3><button class="wc-btn" type="button" data-reset>Clear filters</button></div>
        </section>
        <section class="wc-methodology"><div class="wc-container"><p class="wc-eyebrow">ALGORITHMIC PRECISION</p><h2>How WCC Maps Academic Syllabi to Employer Needs</h2><p>Our 3-layer framework connects lecture hall outcomes and real-world hiring requirements.</p><div class="wc-method-grid">
            @foreach([
                ['Curriculum Learning Outcomes','Every course syllabus across WCC Quezon City is parsed and decomposed into discrete competencies, laboratory hours, and validated learning standards.','Undergraduate Programs'],
                ['Transcript SIS Verification','Student grades, clinical rotation hours, and completed capstone projects can provide evidence of acquired competencies.','Academic Verification'],
                ['Real-Time Industry ATS Matching','Employer requirements across healthcare, tech, and aviation can be correlated with candidate skills and opportunities.','Industry Requirements']
            ] as $step)<article class="wc-panel"><span class="wc-step-number">{{ $loop->iteration }}</span><small>LAYER 0{{ $loop->iteration }}</small><h3>{{ $step[0] }}</h3><p>{{ $step[1] }}</p><strong>{{ $step[2] }}</strong></article>@endforeach
        </div></div></section>
        <section class="wc-container wc-section"><div class="wc-panel wc-verification-cta"><div><p class="wc-eyebrow">STUDENT &amp; ALUMNI VERIFICATION</p><h2>Are you a WCC student looking to verify your acquired skills?</h2><p>Start by creating your account and completing your academic profile.</p></div><a class="wc-btn" href="{{ url('/design-preview/register') }}">⇄ Sync SIS Student Records</a><a href="{{ url('/design-preview/about') }}">Learn More</a></div></section>
    </div>
</x-jobmatch-public-layout>
