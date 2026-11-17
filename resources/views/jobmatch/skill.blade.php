<x-jobmatch-layout :title="$skill->
    name">
    <div class="page-container">
        <a class="back-link" href="{{ route('skills.index') }}">← Browse skills</a>
        <article class="card detail-card">
            <p class="eyebrow">{{ $skill->category->getLabel() }}</p>
            <h1>{{ $skill->name }}</h1>
            <p class="preserve-lines">{{ $skill->description }}</p>
            <a class="button" href="{{ route('jobs.index',['q'=>$skill->name]) }}">Find related jobs</a>
        </article>
    </div>
</x-jobmatch-layout>
