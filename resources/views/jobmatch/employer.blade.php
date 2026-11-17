<x-jobmatch-layout :title="$company->
    company_name">
    <div class="page-container">
        <a class="back-link" href="{{ route('employers.index') }}">← Employer Directory</a>
        <article class="card detail-card">
            <p class="eyebrow">{{ $company->industry }}</p>
            <h1>{{ $company->company_name }}</h1>
            @if($company->location)
                <p>⌖ {{ $company->location }}</p>
            @endif
            @if($company->company_size)
                <p>{{ $company->company_size }} employees</p>
            @endif
            <p class="preserve-lines">{{ $company->company_description }}</p>
            @if($company->website && in_array(parse_url($company->website, PHP_URL_SCHEME),['https','http']))
                <a class="button" href="{{ $company->website }}" target="_blank" rel="noopener noreferrer">Official website ↗</a>
            @endif
        </article>
        <h2>Active job postings</h2>
        <div class="card-grid">
            @forelse($jobs as $job)
                @include('jobmatch.job-card')
            @empty
                <p class="empty">No active job postings.</p>
            @endforelse
        </div>
        <div class="pagination">{{ $jobs->links() }}</div>
    </div>
</x-jobmatch-layout>
