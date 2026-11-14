<x-jobmatch-layout title="Jobs">
    <section class="directory-hero">
        <div>
            <span class="overline">CAREER OPPORTUNITIES</span>
            <h1>Find your next opportunity.</h1>
            <p>Explore active job postings and find a role that fits your experience.</p>
            @include('jobmatch.search')
        </div>
        <span class="directory-hero-icon">
            <x-jobmatch-icon name="briefcase"/>
        </span>
    </section>
    <div class="directory-shell">
        <aside class="filter-panel">
            <div class="filter-panel-heading">
                <h2>Filter opportunities</h2>
                <a href="{{ route('jobs.index') }}">Reset</a>
            </div>
            <div class="directory-filters">
                <fieldset>
                    <legend>Job Type</legend>
                    <div class="chip-row">
                        @foreach(['full_time'=>'Full-time','part_time'=>'Part-time','internship'=>'Internship','contract'=>'Contract','freelance'=>'Freelance'] as $value=>$label)
                            <x-jobmatch-filter-chip param="type" :value="$value" :label="$label" />
                        @endforeach
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Experience Level</legend>
                    <div class="chip-row">
                        @foreach(['entry'=>'Entry','mid'=>'Mid','senior'=>'Senior'] as $value=>$label)
                            <x-jobmatch-filter-chip param="level" :value="$value" :label="$label" />
                        @endforeach
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Date Posted</legend>
                    <div class="chip-row">
                        @foreach([1=>'Last 24h',7=>'Last Week',30=>'Last Month'] as $value=>$label)
                            <x-jobmatch-filter-chip param="posted" :value="$value" :label="$label" />
                        @endforeach
                    </div>
                </fieldset>
            </div>
        </aside>
        <section class="directory-results" aria-labelledby="results-title">
            <div class="section-heading">
                <div>
                    <h2 id="results-title">Active opportunities</h2>
                    <p class="muted">{{ $jobs->total() }} {{ $jobs->total() === 1 ? 'job' : 'jobs' }} found</p>
                </div>
                @if(request()->hasAny(['q','type','level','posted']))
                    <a class="clear-filters" href="{{ route('jobs.index') }}">Clear filters</a>
                @endif
            </div>
            <div class="card-grid">
                @forelse($jobs as $job)
                    @include('jobmatch.job-card')
                @empty
                    <div class="empty">
                        <x-jobmatch-icon name="briefcase"/>
                        <h3>No jobs match your search</h3>
                        <p>Try another keyword or clear the filters.</p>
                        <a class="button outline" href="{{ route('jobs.index') }}">Clear filters</a>
                    </div>
                @endforelse
            </div>
            @include('jobmatch.directory-pagination',['items'=>$jobs,'label'=>'Jobs'])
        </section>
    </div>
</x-jobmatch-layout>
