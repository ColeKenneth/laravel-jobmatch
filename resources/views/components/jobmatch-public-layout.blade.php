@props(['active' => 'home'])
<x-guest-layout>
    @push('styles')
        <link rel="stylesheet" href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" />
        <link rel="stylesheet" href="{{ asset('css/jobmatch-design-preview.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/jobmatch-catalog.css') }}" />
    @endpush
    <x-jobmatch-review-nav />
    <div class="wc-site" x-data="{ menuOpen: false }">
        <header class="wc-header">
            <div class="wc-header-inner">
                <button class="wc-menu-toggle" type="button" @click="menuOpen = !menuOpen" :aria-expanded="menuOpen" aria-controls="wc-main-navigation" aria-label="Toggle navigation">☰</button>
                <a class="wc-brand" href="{{ url('/design-preview/home') }}">
                    <span class="wc-mobile-mark">W</span>
                    <img class="wc-desktop-seal" src="{{ asset('images/jobmatch/catalog/wcc-seal.png') }}" alt="" />
                    <span><strong>WORLD CITI COLLEGES QUEZON CITY</strong><small><i></i> JobMatchAI Portal</small></span>
                </a>
                <nav class="wc-nav" id="wc-main-navigation" :class="{ 'is-open': menuOpen }" aria-label="Main navigation">
                    @foreach (['home' => 'Home', 'jobs' => 'Jobs', 'skills' => 'Browse Skills', 'employers' => 'Employer Directory', 'about' => 'About Us'] as $path => $label)
                        <a href="{{ url('/design-preview/'.$path) }}" @class(['active' => $active === $path]) @if($active === $path) aria-current="page" @endif>{{ $label }}</a>
                    @endforeach
                </nav>
                <div class="wc-auth-links">@auth<a class="wc-btn wc-btn-small" href="{{ route('dashboard') }}">Dashboard</a><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Log out</button></form>@else<a href="{{ route('login') }}">Login</a><a class="wc-btn wc-btn-small" href="{{ route('register') }}">Register</a><a class="wc-account" href="{{ route('login') }}" aria-label="Account">♙</a>@endauth</div>
            </div>
        </header>
        <main>{{ $slot }}</main>
        <footer class="wc-footer">
            <div class="wc-container wc-footer-grid">
                <section><h2>WCC JobMatchAI <span class="wc-badge wc-badge-gold">Quezon City</span></h2><p>Empowering World Citi Colleges students and alumni with intelligent career pathway alignment and industry connections.</p></section>
                <section><h3>Quick Links</h3>@foreach(['jobs' => 'Jobs', 'skills' => 'Browse Skills', 'employers' => 'Employer Directory', 'about' => 'About Us', 'register' => 'Register'] as $path => $label)<a href="{{ url('/design-preview/'.$path) }}">{{ $label }}</a>@endforeach</section>
                <section><h3>Contact Info</h3><p>960 Aurora Blvd, Quezon City, Metro Manila</p><p>(02) 348-7390</p><p>admissions@worldciti.edu.ph</p><p>Contact details shown as supplied in the design.</p></section>
                <section><h3>EXCELLENCE • SERVICE • LEADERSHIP</h3><p>Accredited higher educational institution preparing healthcare, business, and tech professionals for global readiness.</p></section>
            </div>
            <div class="wc-container wc-footer-bottom"><span>World Citi Colleges Quezon City • JobMatchAI frontend review</span><span><a href="{{ url('/design-preview/about') }}">About the Portal</a>@if(Route::has('policy.show'))<a href="{{ route('policy.show') }}">Privacy Policy</a>@endif @if(Route::has('terms.show'))<a href="{{ route('terms.show') }}">Terms of Service</a>@endif</span></div>
        </footer>
        <nav class="wc-bottom-nav" aria-label="Mobile navigation">
            @foreach(['home' => ['⌂','Home'], 'jobs' => ['▣','Jobs'], 'skills' => ['✧','Skills'], 'employers' => ['▤','Employers']] as $path => $item)<a href="{{ url('/design-preview/'.$path) }}" @class(['active' => $active === $path])><b aria-hidden="true">{{ $item[0] }}</b>{{ $item[1] }}</a>@endforeach
            <a href="{{ route('login') }}"><b aria-hidden="true">♙</b>Profile</a>
        </nav>
        <dialog id="wc-preview-dialog" class="wc-dialog" aria-labelledby="wc-dialog-title"><h2 id="wc-dialog-title">Frontend preview</h2><p id="wc-dialog-message"></p><form method="dialog"><button class="wc-btn">Got it</button></form></dialog>
        <div id="wc-toast" class="wc-toast" role="status" hidden></div>
    </div>
    <script src="{{ asset('js/jobmatch-catalog.js') }}" type="module"></script>
</x-guest-layout>
