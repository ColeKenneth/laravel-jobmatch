<x-jobmatch-layout title="About Us">
    <section class="about-hero" aria-labelledby="about-title">
        <div class="about-campus">
            <img src="{{ asset('images/jobmatch/current/campus.jpg') }}" alt="World Citi Colleges campus building" fetchpriority="high">
            <div class="about-photo-caption">
                <x-jobmatch-icon name="school"/>
                <span>
                    WORLD CITI COLLEGES
                    <strong>Quezon City Campus</strong>
                </span>
            </div>
        </div>
        <div class="about-intro">
            <span class="profile-eyebrow">OUR PURPOSE · YOUR FUTURE</span>
            <h1 id="about-title">
                About WCC
                <span>JobMatchAI</span>
            </h1>
            <p class="about-lead">Connecting the WCC community with career opportunities, partner employers, and the skills to move forward.</p>
            <div class="about-values">
                <section class="about-value">
                    <span class="about-value-icon">
                        <x-jobmatch-icon name="eye"/>
                    </span>
                    <div>
                        <h2>Vision: Empowering Careers</h2>
                        <p>Connect students and alumni with meaningful career opportunities and partner employers.</p>
                    </div>
                </section>
                <section class="about-value">
                    <span class="about-value-icon">
                        <x-jobmatch-icon name="target"/>
                    </span>
                    <div>
                        <h2>Mission: Precision Talent Matching</h2>
                        <p>Bridge academic skills and workplace needs through clear, useful career information.</p>
                    </div>
                </section>
                <section class="about-value">
                    <span class="about-value-icon">
                        <x-jobmatch-icon name="team"/>
                    </span>
                    <div>
                        <h2>Our AI &amp; Development Team</h2>
                        <p>Developed for the WCC community with a focus on transparent information and practical career support.</p>
                    </div>
                </section>
                <section class="about-value">
                    <span class="about-value-icon">
                        <x-jobmatch-icon name="school"/>
                    </span>
                    <div>
                        <h2>WCC’s Commitment to Student Success</h2>
                        <p>Supporting students and graduates as they explore opportunities, develop their skills, and build their careers.</p>
                    </div>
                </section>
            </div>
        </div>
    </section>
    <section class="campus-location page-container about-location" id="campus-location" aria-labelledby="location-title">
        <div>
            <span class="about-location-icon">
                <x-jobmatch-icon name="pin"/>
            </span>
            <span class="profile-eyebrow">VISIT &amp; CONNECT</span>
            <h2 id="location-title">Visit WCC Quezon City</h2>
            <p>960 Aurora Boulevard, Quezon City, Metro Manila</p>
            <a class="button" href="https://www.openstreetmap.org/way/120115733" target="_blank" rel="noopener noreferrer">Open campus map ↗</a>
            <a class="map-google-link" href="https://www.google.com/maps/search/?api=1&amp;query=World%20Citi%20Colleges%20960%20Aurora%20Boulevard%20Quezon%20City" target="_blank" rel="noopener noreferrer">Open in Google Maps ↗</a>
        </div>
        <iframe title="OpenStreetMap: World Citi Colleges Quezon City" src="https://www.openstreetmap.org/export/embed.html?bbox=121.0592%2C14.6242%2C121.0672%2C14.6302&amp;layer=mapnik&amp;marker=14.62723%2C121.06324" loading="lazy"></iframe>
    </section>
</x-jobmatch-layout>
