@php($catalog = json_decode(file_get_contents(resource_path('views/design-preview/catalog.json')), true))
@php($employers = collect($catalog['employers'])->keyBy('id'))
<x-jobmatch-public-layout active="jobs">
    <div data-catalog="jobs" class="wc-catalog">
        <section class="wc-search-canopy">
            <div class="wc-container">
                <form class="wc-search-composite" data-search-form>
                    <label><span>ROLE OR KEYWORD</span><input type="search" name="q" data-filter="query" placeholder="e.g. Registered Nurse, Python Developer, HR Specialist" aria-label="Search job title, company or skills" /></label>
                    <label><span>LOCATION</span><select data-filter="location" aria-label="Location"><option value="">All locations</option><option value="quezon">Quezon City (Near WCC)</option><option value="remote">Remote</option><option value="pasay">Pasay</option></select></label>
                    <label><span>WCC DISCIPLINE</span><select data-filter="category" aria-label="WCC discipline"><option value="">All WCC disciplines</option><option value="healthcare">Allied Health &amp; Nursing</option><option value="it">Information Technology</option><option value="aviation">Hospitality &amp; Aviation</option><option value="business">Business &amp; Accountancy</option><option value="education">Psychology &amp; Education</option></select></label>
                    <button class="wc-btn" type="submit">⌕ Search Jobs</button>
                </form>
                <div class="wc-chip-line"><span>✧ Recommended Tags:</span><button data-query="BSN">BSN Clinical Focus</button><button data-query="React">React / Mobile Apps</button><button data-query="Quezon City">Quezon City Campus Vicinity</button><button data-query="">All sample roles</button></div>
            </div>
        </section>
        <div class="wc-container wc-jobs-layout">
            <aside class="wc-panel wc-filters" id="job-filters">
                <div class="wc-heading-line"><h2>⚑ Filter Roles</h2><button class="wc-link" type="button" data-reset>Clear All</button></div>
                <div class="wc-match-filter"><strong>✧ AI Match Index <span class="wc-badge wc-badge-gold">Sample scores</span></strong><p>Match figures are supplied by the Figma mockup.</p><div class="wc-segments"><button data-match="90">90%+</button><button data-match="80">80%+</button><button data-match="0" aria-pressed="true">All</button></div></div>
                <fieldset><legend>EXPERIENCE LEVEL</legend>@foreach(['entry' => 'Entry Level / Fresh Grad', 'internship' => 'Internship / OJT', 'mid' => 'Mid-Level (1–3 yrs)', 'senior' => 'Senior Professional'] as $value => $label)<label><input type="checkbox" data-filter="experience" value="{{ $value }}" />{{ $label }}</label>@endforeach</fieldset>
                <fieldset><legend>EMPLOYMENT TYPE</legend>@foreach(['Full-Time', 'Part-Time', 'Contract', 'Internship'] as $type)<label><input type="checkbox" data-filter="type" value="{{ $type }}" />{{ $type }}</label>@endforeach</fieldset>
                <fieldset><legend>WCC ACADEMIC TRACK</legend>@foreach(['healthcare' => 'Allied Health & Nursing', 'it' => 'Information Technology', 'aviation' => 'Hospitality & Aviation', 'business' => 'Business & Accountancy', 'education' => 'Psychology & Education'] as $value => $label)<label><input type="checkbox" data-filter="categories" value="{{ $value }}" />{{ $label }}</label>@endforeach</fieldset>
                <label class="wc-range-label">MINIMUM MONTHLY SALARY <output data-salary-label>Any salary</output><input type="range" min="0" max="70000" step="5000" value="0" data-filter="salary" /></label>
                <div class="wc-soft-callout"><strong>⌖ Aurora Blvd Shuttle Access</strong><p>Explore roles with convenient transit to the Quezon City campus.</p></div>
            </aside>
            <section class="wc-results-section" aria-labelledby="jobs-heading">
                <div class="wc-panel wc-results-heading"><div><h1 id="jobs-heading">Active Openings for WCC Talent</h1><p data-result-count aria-live="polite">6 sample positions from your design</p></div><div class="wc-results-tools"><button type="button" class="wc-mobile-filter wc-btn-light" data-toggle-filters aria-expanded="false" aria-controls="job-filters">☷ Filters</button><select data-filter="sort" aria-label="Sort jobs"><option value="original">Figma order</option><option value="match">Highest AI Match</option><option value="salary">Highest Salary</option><option value="name">Job Title A–Z</option></select></div></div>
                <div class="wc-job-list" data-record-list>
                    @foreach($catalog['jobs'] as $job)
                        @php($employer = $employers[$job['employer']])
                        <article class="wc-panel wc-job-card" data-record data-name="{{ $job['title'] }}" data-search="{{ $job['title'].' '.($job['company'] ?? $employer['name']).' '.$job['location'].' '.implode(' ', $job['tags']) }}" data-category="{{ $job['category'] }}" data-location="{{ strtolower($job['location']) }}" data-experience="{{ $job['experience'] }}" data-type="{{ $job['type'] }}" data-salary="{{ $job['minimum'] }}" data-match="{{ $job['match'] }}">
                            <div class="wc-job-card-top"><img class="wc-company-icon" src="{{ asset('images/jobmatch/catalog/'.$job['employer'].'.png') }}" alt="" /><div class="wc-job-title"><p class="wc-mobile-company">{{ $job['company'] ?? $employer['name'] }}</p><h2><a href="{{ url('/design-preview/job?job='.$job['id']) }}">{{ $job['title'] }}</a> <span class="wc-badge wc-badge-gold wc-desktop">{{ $job['badge'] }}</span></h2><p>{{ $job['company'] ?? $employer['name'] }} • {{ $job['location'] }} <span class="wc-desktop">• Posted {{ $job['posted'] }}</span></p></div><span class="wc-badge wc-match-score wc-desktop">✧ {{ $job['match'] }}% AI Match</span><button type="button" class="wc-save" data-save="job-{{ $job['id'] }}" aria-label="Save {{ $job['title'] }}" aria-pressed="false">♧</button></div>
                            <p class="wc-job-description">{{ $job['description'] }}</p>
                            <div class="wc-mobile-score"><span class="wc-badge wc-badge-gold">✧ {{ $job['match'] }}% AI Match</span><span class="wc-badge">{{ $job['salary'] }} /mo</span></div>
                            <div class="wc-job-card-bottom"><div class="wc-tags">@foreach($job['tags'] as $tag)<span>{{ $tag }}</span>@endforeach</div><div class="wc-job-actions"><div class="wc-salary"><strong>{{ $job['salary'] }}</strong><small>/ month {{ $job['perk'] ? '+ '.$job['perk'] : '' }}</small></div><a class="wc-btn wc-btn-small" href="{{ url('/design-preview/job?job='.$job['id']) }}">View Details →</a></div></div>
                        </article>
                    @endforeach
                </div>
                <div class="wc-empty wc-panel" data-empty hidden><h2>No matching sample jobs</h2><p>Try a broader keyword or clear the filters.</p><button class="wc-btn" type="button" data-reset>Reset filters</button></div>
                <div class="wc-pagination wc-panel"><span data-result-summary>Showing all 6 sample openings</span><span class="wc-page-current" aria-current="page">1</span></div>
                <div class="wc-career-cta"><div><h2>Don't see your desired career match yet?</h2><p>Build your profile and prepare for your next opportunity.</p></div><a class="wc-btn wc-btn-gold" href="{{ route('login') }}">Run AI Profile Diagnostic</a></div>
            </section>
        </div>
    </div>
</x-jobmatch-public-layout>
