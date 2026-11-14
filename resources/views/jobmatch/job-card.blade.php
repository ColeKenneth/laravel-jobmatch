<article class="card job-card">
    <div class="job-card-top">
        <x-jobmatch-mark :src="$job->
            employer?->logo_url" :name="$job->employer?->company_name ?? 'WCC'" />
            <span class="badge">{{ $job->employment_type->getLabel() }}</span>
        </div>
        <h3>
            <a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a>
        </h3>
        <p>{{ $job->employer?->company_name }}</p>
        <small>⌖ {{ $job->location }}</small>
        <div class="job-card-meta">
            @if($job->min_salary !== null && $job->max_salary !== null)
                <strong>₱{{ number_format($job->min_salary) }}–₱{{ number_format($job->max_salary) }}</strong>
            @else
                <span>Salary not specified</span>
            @endif
            <small>{{ $job->min_experience_years }} {{ $job->min_experience_years == 1 ? 'year' : 'years' }} minimum experience</small>
        </div>
        <a class="button outline" href="{{ route('jobs.show', $job) }}">
            View Details
            <span aria-hidden="true">→</span>
        </a>
    </article>
