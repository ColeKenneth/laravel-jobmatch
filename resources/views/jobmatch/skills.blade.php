<x-jobmatch-layout title="Browse Skills">
    <section class="directory-hero">
        <div>
            <span class="overline">SKILLS DIRECTORY</span>
            <h1>Build skills for your next step.</h1>
            <p>Explore competencies and skills used in current job postings.</p>
        </div>
        <span class="directory-hero-icon">
            <x-jobmatch-icon name="skills"/>
        </span>
    </section>
    <section class="directory-content">
        <form class="search-bar" method="GET" role="search">
            <input name="q" type="search" placeholder="Search skills, courses, or experience" value="{{ request('q') }}" aria-label="Search skills">
            <select name="category" aria-label="Skill type">
                <option value="">All Categories</option>
                @foreach(App\Enum\Category::cases() as $category)
                    <option value="{{ $category->value }}" @selected(request('category')===$category->value)>{{ $category->getLabel() }}</option>
                @endforeach
            </select>
            @foreach(['discipline','source'] as $field)
                <input type="hidden" name="{{ $field }}" value="{{ request($field) }}">
            @endforeach
            <button class="button">Find Skills ⌕</button>
        </form>
        <div class="directory-filters">
            <fieldset>
                <legend>Category</legend>
                <div class="chip-row">
                    @foreach(['programming'=>'Programming','marketing'=>'Marketing','healthcare'=>'Healthcare'] as $value=>$label)
                        <x-jobmatch-filter-chip param="discipline" :value="$value" :label="$label" />
                    @endforeach
                </div>
            </fieldset>
            <fieldset>
                <legend>Source</legend>
                <div class="chip-row">
                    <x-jobmatch-filter-chip param="source" value="courses" label="Courses" />
                    <x-jobmatch-filter-chip param="source" value="experience" label="Experience" />
                </div>
            </fieldset>
            <fieldset>
                <legend>Skill Type</legend>
                <div class="chip-row">
                    <x-jobmatch-filter-chip param="category" value="technical" label="Technical" />
                    <x-jobmatch-filter-chip param="category" value="soft" label="Soft Skills" />
                </div>
            </fieldset>
        </div>
        @if(request()->hasAny(['q','category','discipline','source']))
            <a class="clear-filters" href="{{ route('skills.index') }}">Clear filters</a>
        @endif
        <div class="card-grid">
            @forelse($skills as $skill)
                <article class="card job-card skill-directory-card">
                    <x-jobmatch-mark :src="$skill->
                        icon_url" :name="$skill->name" symbol="✧" />
                        <h3>{{ $skill->name }}</h3>
                        <p>{{ Str::limit($skill->description,120) }}</p>
                        <small>Used by {{ $skill->active_job_count }} active job {{ $skill->active_job_count === 1 ? 'posting' : 'postings' }}.</small>
                        <a class="button outline" href="{{ route('skills.show',$skill) }}">View Details</a>
                    </article>
                @empty
                    <p class="empty">No skills match these filters.</p>
                @endforelse
            </div>
            @include('jobmatch.directory-pagination',['items'=>$skills,'label'=>'Skills'])
        </section>
    </x-jobmatch-layout>
