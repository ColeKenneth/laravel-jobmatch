<nav class="jm-review-bar" aria-label="Frontend review pages">
    <strong>FRONTEND REVIEW</strong>
    @foreach (['home' => 'Home', 'jobs' => 'Jobs', 'skills' => 'Skills', 'employers' => 'Employers', 'employer' => 'Employer Profile', 'about' => 'About', 'job' => 'Job Details', 'register' => 'Register', 'forgot' => 'Forgot Password'] as $path => $label)
        <a href="{{ url('/design-preview/'.$path) }}">{{ $label }}</a>
    @endforeach
    <a href="{{ route('login') }}">Login</a>
    <span>Sample listings and match scores from your Figma • no live applications submitted</span>
</nav>
