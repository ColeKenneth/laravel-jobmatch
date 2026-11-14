<x-jobmatch-layout title="Home">
    <section class="stitch-hero">
        <div class="hero-image">
            <img src="{{ asset('images/jobmatch/current/campus.jpg') }}" alt="World Citi Colleges campus building" fetchpriority="high">
            <div class="hero-image-label">
                <x-jobmatch-icon name="school"/>
                <span>
                    World Citi Colleges
                    <strong>Quezon City Campus</strong>
                </span>
            </div>
        </div>
        <div class="hero-copy">
            <span class="overline">WCC JOBMATCHAI · CAREER PORTAL</span>
            <h1>
                Unlock Your Career
                <br>
                <span>Potential at WCC</span>
            </h1>
            <p>Explore career opportunities, build your skills, and connect with employers in the WCC community.</p>
            @include('jobmatch.search')
            <div class="hero-quick-links">
                <span>Explore:</span>
                <a href="{{ route('jobs.index',['type'=>'full_time']) }}">Full-time jobs ↗</a>
                <a href="{{ route('jobs.index',['type'=>'internship']) }}">Internships ↗</a>
                <a href="{{ route('skills.index') }}">Browse skills ↗</a>
            </div>
        </div>
    </section>
    <section class="stitch-section">
        <div class="section-heading">
            <div>
                <span class="overline">YOUR NEXT OPPORTUNITY</span>
                <h2>Featured active job postings</h2>
                <p class="muted">Explore the latest opportunities from employer listings.</p>
            </div>
            <a class="button outline" href="{{ route('jobs.index') }}">View all jobs →</a>
        </div>
        <div class="card-grid">
            @forelse($jobs as $job)
                @include('jobmatch.job-card')
            @empty
                <div class="empty">
                    <x-jobmatch-icon name="briefcase"/>
                    <h3>No active job postings yet</h3>
                    <p>Check back soon for opportunities from employers.</p>
                </div>
            @endforelse
        </div>
    </section>
    <section class="stitch-section pathway-section">
        <div class="section-heading">
            <div>
                <span class="overline">START WITH YOUR NEXT STEP</span>
                <h2>A clearer path to your career</h2>
            </div>
        </div>
        <div class="pathway-grid">
            <a href="{{ route('jobs.index') }}" class="pathway-card">
                <span class="section-icon">
                    <x-jobmatch-icon name="briefcase"/>
                </span>
                <h3>Explore opportunities</h3>
                <p>Find job descriptions, requirements, and employer information in one place.</p>
                <span>Browse Jobs →</span>
            </a>
            <a href="{{ route('skills.index') }}" class="pathway-card">
                <span class="section-icon">
                    <x-jobmatch-icon name="skills"/>
                </span>
                <h3>Discover skills</h3>
                <p>Explore skills and competencies used in current job postings.</p>
                <span>Browse Skills →</span>
            </a>
            <a href="{{ route('employers.index') }}" class="pathway-card">
                <span class="section-icon">
                    <x-jobmatch-icon name="team"/>
                </span>
                <h3>Meet employers</h3>
                <p>Learn about companies and the opportunities they have available.</p>
                <span>Employer Directory →</span>
            </a>
        </div>
    </section>
</x-jobmatch-layout>
