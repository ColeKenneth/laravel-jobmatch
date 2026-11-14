<header class="site-header">
    <a class="brand" href="{{ route('home') }}" aria-label="WCC JobMatchAI home">
        <img src="{{ asset('images/jobmatch/current/wcc-logo.png') }}" alt="WCC World Citi Colleges Quezon City" width="345" height="64">
    </a>
    <details class="mobile-menu">
        <summary aria-label="Open navigation">
            <span aria-hidden="true">☰</span>
            Menu
        </summary>
        <nav aria-label="Mobile navigation">@include('jobmatch.navigation', ['portal' => $portal])</nav>
    </details>
    <nav class="desktop-nav" aria-label="Main navigation">@include('jobmatch.navigation', ['portal' => $portal])</nav>
    @auth
        <details class="account-menu">
            <summary>
                <span class="profile-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->first_name,0,1)) }}</span>
                <span class="account-name">{{ auth()->user()->full_name }}</span>
                <span aria-hidden="true">⌄</span>
            </summary>
            <a href="{{ route('portal.profile') }}">My profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Log out</button>
            </form>
        </details>
    @else
        <div class="auth-nav">
            <a class="button" href="{{ route('register') }}">Register</a>
            <a href="{{ route('login') }}">Login</a>
        </div>
    @endauth
</header>
