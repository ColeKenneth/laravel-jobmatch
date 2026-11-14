@props(['heading', 'description'])
<x-guest-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/jobmatch-login.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/jobmatch-auth.css') }}" />
    @endpush
    <main class="jm-login jm-auth">
        <section class="jm-login-shell" aria-labelledby="auth-heading">
            <a class="jm-auth-brand" href="{{ url('/design-preview/home') }}"><img src="{{ asset('images/jobmatch/catalog/wcc-seal.png') }}" alt="World Citi Colleges"><span>WORLD CITI COLLEGES<small>JobMatchAI · Quezon City</small></span></a>
            <div class="jm-login-intro"><span class="jm-campus-pill">YOUR NEXT CHAPTER STARTS HERE</span><h1 id="auth-heading">{{ $heading }}</h1><p>{{ $description }}</p></div>
            <x-validation-errors class="jm-auth-errors" />
            @if (session('status'))<p class="jm-auth-status" role="status">{{ session('status') }}</p>@endif
            {{ $slot }}
            <p class="jm-auth-home"><a href="{{ url('/design-preview/home') }}">← Back to JobMatchAI</a></p>
        </section>
    </main>
</x-guest-layout>
