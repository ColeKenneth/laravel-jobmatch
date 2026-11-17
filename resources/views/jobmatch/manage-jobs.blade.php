<x-jobmatch-layout portal title="Manage Jobs">
    <div class="workspace-heading">
        <span class="overline">YOUR WORKSPACE</span>
        <h1>Manage Jobs</h1>
        <p class="muted">Create and edit your company’s job postings.</p>
    </div>
    <section class="card">
        <h1>Manage Job Postings</h1>
        @if(!$company || $company->company_name === 'Not specified')
            <p>Add your company profile before posting a job.</p>
            <a class="button" href="{{ route('portal.company') }}">Company Profile</a>
        @else
            <details class="job-editor">
                <summary class="button">Post a Job</summary>
                <form class="form-grid" method="POST" action="{{ route('portal.jobs.save') }}">@csrf @include('jobmatch.job-form',['job'=>null])</form>
            </details>
            @forelse($jobs as $job)
                <details class="job-editor">
                    <summary>
                        <strong>{{ $job->title }}</strong>
                        ·
                        {{ $job->is_active ? 'Active' : 'Closed' }}
                        · Edit
                    </summary>
                    <form class="form-grid" method="POST" action="{{ route('portal.jobs.update',$job) }}">@csrf @method('PUT')@include('jobmatch.job-form')</form>
                </details>
            @empty
                <p class="empty">No job postings yet.</p>
            @endforelse
        @endif
    </section>
</x-jobmatch-layout>
