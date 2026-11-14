<x-jobmatch-layout :title="$job->
    title">
    <div class="page-container job-detail-page">
        <nav class="detail-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('jobs.index') }}">Jobs</a>
            <span aria-hidden="true">/</span>
            <span>{{ $job->title }}</span>
        </nav>
        <div class="job-detail-grid">
            <article class="card detail-card">
                <div class="detail-employer-heading">
                    <x-jobmatch-mark :src="$job->
                        employer?->logo_url" :name="$job->employer?->company_name ?? 'WCC'"/>
                        <div>
                            <p class="eyebrow">{{ $job->employer?->company_name }}</p>
                            <h1>{{ $job->title }}</h1>
                        </div>
                    </div>
                    <div class="tag-row">
                        <span>⌖ {{ $job->location }}</span>
                        <span>{{ $job->employment_type->getLabel() }}</span>
                        <span>{{ $job->min_experience_years }} years minimum experience</span>
                    </div>
                    <section class="detail-section">
                        <h2>
                            <x-jobmatch-icon name="briefcase"/>
                            Job description
                        </h2>
                        <p class="preserve-lines">{{ $job->description }}</p>
                    </section>
                    @if($job->requirements)
                        <section class="detail-section">
                            <h2>
                                <x-jobmatch-icon name="school"/>
                                Qualifications
                            </h2>
                            <p class="preserve-lines">{{ $job->requirements }}</p>
                        </section>
                    @endif
                    @if($job->requiredSkills->isNotEmpty())
                        <section class="detail-section">
                            <h2>
                                <x-jobmatch-icon name="skills"/>
                                Required skills
                            </h2>
                            <div class="tag-row">
                                @foreach($job->requiredSkills as $item)
                                    <span>{{ $item->skill?->name }}</span>
                                @endforeach
                            </div>
                        </section>
                    @endif
                    @if($job->preferred_skills)
                        <section class="detail-section">
                            <h2>
                                <x-jobmatch-icon name="certificate"/>
                                Preferred skills
                            </h2>
                            <p class="preserve-lines">{{ $job->preferred_skills }}</p>
                        </section>
                    @endif
                </article>
                <aside class="job-detail-sidebar">
                    <section class="card">
                        <span class="overline">OPPORTUNITY OVERVIEW</span>
                        <h2>At a glance</h2>
                        <dl class="detail-facts">
                            <div>
                                <dt>Salary range</dt>
                                <dd>@if($job->min_salary !== null && $job->max_salary !== null)₱{{ number_format($job->min_salary) }}–₱{{ number_format($job->max_salary) }}@else Salary not specified @endif</dd>
                            </div>
                            <div>
                                <dt>Employment type</dt>
                                <dd>{{ $job->employment_type->getLabel() }}</dd>
                            </div>
                            <div>
                                <dt>Location</dt>
                                <dd>{{ $job->location }}</dd>
                            </div>
                            <div>
                                <dt>Deadline</dt>
                                <dd>{{ $job->deadline?->format('M j, Y') ?? 'Not specified' }}</dd>
                            </div>
                        </dl>
                        <button class="button outline stretch" data-share type="button">Share job ↗</button>
                    </section>
                    <section class="card">
                        <span class="section-icon">
                            <x-jobmatch-icon name="team"/>
                        </span>
                        <h2>{{ $job->employer?->company_name }}</h2>
                        <p class="muted">Learn about the employer and their available opportunities.</p>
                        <a class="button stretch" href="{{ route('employers.show', $job->employer) }}">View employer →</a>
                        <p class="detail-note">Contact the employer for application instructions.</p>
                    </section>
                </aside>
            </div>
            <a class="back-link" href="{{ route('jobs.index') }}">← Back to jobs</a>
        </div>
    </x-jobmatch-layout>
