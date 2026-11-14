<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <a class="footer-logo" href="{{ route('home') }}">
                <img src="{{ asset('images/jobmatch/current/wcc-footer-transparent.png') }}" alt="WCC World Citi Colleges Quezon City">
            </a>
            <strong>WCC JobMatchAI</strong>
            <p>Connecting the WCC community with career opportunities.</p>
        </div>
        <div class="footer-links">
            <h2>Explore</h2>
            <a href="{{ route('jobs.index') }}">Jobs</a>
            <a href="{{ route('skills.index') }}">Browse Skills</a>
            <a href="{{ route('employers.index') }}">Employer Directory</a>
            <a href="{{ route('about') }}">About Us</a>
        </div>
        <div class="footer-contact">
            <h2>Visit &amp; Connect</h2>
            <a href="{{ route('about') }}#campus-location">
                <span aria-hidden="true">⌖</span>
                960 Aurora Blvd, Quezon City
            </a>
            <a href="mailto:admissions@worldciti.edu.ph">
                <span aria-hidden="true">✉</span>
                admissions
                @worldciti
                .edu.ph
            </a>
            <a href="{{ route('about') }}#campus-location">Open campus map ↗</a>
            <button class="footer-install" type="button" data-install-app>
                <x-jobmatch-icon name="download"/>
                Install JobMatchAI
            </button>
        </div>
    </div>
    <div class="footer-bottom">
        <small>© {{ date('Y') }} World Citi Colleges Quezon City · JobMatchAI</small>
        <a href="#page-top">Back to top ↑</a>
    </div>
</footer>
