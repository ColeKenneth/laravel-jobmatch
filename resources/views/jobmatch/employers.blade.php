<x-jobmatch-layout title="Employer Directory">
    <section class="directory-hero">
        <div>
            <span class="overline">EMPLOYER DIRECTORY</span>
            <h1>Find Your Next Partner</h1>
            <p>Explore companies, industries, and their active opportunities.</p>
        </div>
        <span class="directory-hero-icon">
            <x-jobmatch-icon name="team"/>
        </span>
    </section>
    <section class="directory-content">
        <form class="search-bar" method="GET" role="search">
            <input name="q" type="search" value="{{ request('q') }}" placeholder="Search Companies by Name, Industry, Location" aria-label="Search companies by name industry or location">
            <input type="hidden" name="size" value="{{ request('size') }}">
            <select name="industry" aria-label="Industry">
                <option value="">All industries</option>
                @foreach($industries as $industry)
                    <option value="{{ $industry }}" @selected(request('industry')===$industry)>{{ $industry }}</option>
                @endforeach
            </select>
            @foreach(['location'] as $field)
                <input type="hidden" name="{{ $field }}" value="{{ request($field) }}">
            @endforeach
            <button class="button">Find Partner ⌕</button>
        </form>
        <div class="directory-filters employer-filter-groups">
            <fieldset>
                <legend>Industry</legend>
                <div class="chip-row">
                    @foreach(['Government','BPO & Call Center','IT & Software','Healthcare','Education','Banking & Finance'] as $industry)
                        <x-jobmatch-filter-chip param="industry" :value="$industry" :label="$industry" />
                    @endforeach
                </div>
            </fieldset>
            <fieldset>
                <legend>Location</legend>
                <div class="chip-row">
                    @foreach(['Quezon City','Manila','Makati','Cebu','Davao'] as $city)
                        <x-jobmatch-filter-chip param="location" :value="$city" :label="$city" />
                    @endforeach
                </div>
            </fieldset>
            <form class="company-size-filter" method="GET">
                <label for="directory-company-size">Company Size</label>
                @foreach(['q','industry','location'] as $field)
                    <input type="hidden" name="{{ $field }}" value="{{ request($field) }}">
                @endforeach
                <div>
                    <select id="directory-company-size" name="size">
                        <option value="">Any company size</option>
                        <option value="1-500" @selected(request('size')==='1-500')>1–500 employees</option>
                        <option value="500+" @selected(request('size')==='500+')>500+ employees</option>
                        @if(request('size') && !in_array(request('size'),['1-500','500+'],true))
                            <option value="{{ request('size') }}" selected>{{ request('size') }} employees</option>
                        @endif
                    </select>
                    <button class="button" type="submit">Apply</button>
                </div>
            </form>
        </div>
        @if(request()->hasAny(['q','industry','location','size']))
            <a class="clear-filters" href="{{ route('employers.index') }}">Clear filters</a>
        @endif
        <div class="card-grid companies">
            @forelse($companies as $company)
                <article class="card job-card company-directory-card">
                    <div class="company-card-heading">
                        <x-jobmatch-mark :src="$company->
                            logo_url" :name="$company->company_name" />
                            <h3>{{ $company->company_name }}</h3>
                        </div>
                        <p>{{ Str::limit($company->company_description,110) }}</p>
                        <small>{{ $company->industry }}@if($company->location) · {{ $company->location }}@endif</small>
                        <small>{{ $company->job_postings_count }} open {{ $company->job_postings_count === 1 ? 'position' : 'positions' }}</small>
                        <a class="button outline" href="{{ route('employers.show',$company) }}">View Details</a>
                    </article>
                @empty
                    <p class="empty">No employers match these filters.</p>
                @endforelse
            </div>
            @include('jobmatch.directory-pagination',['items'=>$companies,'label'=>'Partners'])
        </section>
    </x-jobmatch-layout>
