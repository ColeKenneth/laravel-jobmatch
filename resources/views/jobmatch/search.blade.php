<form class="search-bar" action="{{ route('jobs.index') }}" method="GET" role="search">
    <label class="sr-only" for="job-search">Search jobs, companies, or locations</label>
    <input id="job-search" name="q" type="search" value="{{ request('q') }}" placeholder="Search jobs, companies, or locations">
    <label class="sr-only" for="job-type">Employment type</label>
    <select id="job-type" name="type">
        <option value="">All employment types</option>
        @foreach(App\Enum\EmploymentType::cases() as $type)
            <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->getLabel() }}</option>
        @endforeach
    </select>
    @foreach(['level','posted'] as $field)
        <input type="hidden" name="{{ $field }}" value="{{ request($field) }}">
    @endforeach
    <button class="button" type="submit">Find Jobs ⌕</button>
</form>
