<x-jobmatch-layout portal title="Home">
    @if($user->role === App\Enum\UserRole::STUDENT)
        @include('jobmatch.student-dashboard')
    @else
        <section class="card welcome">
            <div>
                <span class="overline">{{ $user->role->getLabel() }} PORTAL</span>
                <h1>Welcome back, {{ $user->first_name }}!</h1>
                <p>Manage your information and explore your next steps.</p>
            </div>
            <a class="button outline" href="{{ route('portal.profile') }}">Edit Profile →</a>
        </section>
        @if($user->role === App\Enum\UserRole::EMPLOYER)
            <div class="dashboard-grid role-dashboard">
                <section class="card">
                    <span class="section-icon">
                        <x-jobmatch-icon name="school"/>
                    </span>
                    <h2>Company Profile</h2>
                    <p>{{ $company?->company_name ?? 'Add your company profile to publish jobs.' }}</p>
                    <a class="button outline stretch" href="{{ route('portal.company') }}">Edit Company Profile →</a>
                </section>
                <section class="card">
                    <span class="section-icon">
                        <x-jobmatch-icon name="briefcase"/>
                    </span>
                    <h2>Job Postings</h2>
                    <strong class="stat-number">{{ $companyJobs->count() }}</strong>
                    <p>{{ $companyJobs->where('is_active', true)->count() }} active</p>
                    <a class="button outline stretch" href="{{ route('portal.jobs') }}">Manage Jobs →</a>
                </section>
                <section class="card">
                    <span class="section-icon">
                        <x-jobmatch-icon name="chart"/>
                    </span>
                    <h2>Placements</h2>
                    <p>View placement records associated with your company’s job postings.</p>
                    <a class="button outline stretch" href="{{ route('portal.placements') }}">View Placements →</a>
                </section>
            </div>
        @elseif(in_array($user->role,[App\Enum\UserRole::ADMIN,App\Enum\UserRole::PLACEMENT_OFFICER],true))
            <section class="card staff-workspace">
                <span class="section-icon">
                    <x-jobmatch-icon name="school"/>
                </span>
                <span class="overline">PLACEMENT OFFICE</span>
                <h2>Your placement workspace</h2>
                <p class="muted">Review recorded placements and download the current placement report.</p>
                <div class="pathway-grid">
                    <a class="pathway-card" href="{{ route('portal.placements') }}">
                        <x-jobmatch-icon name="chart"/>
                        <h3>Placement Records</h3>
                        <p>Browse and filter the recorded placement list.</p>
                        <span>View records →</span>
                    </a>
                    <a class="pathway-card" href="{{ route('portal.reports') }}">
                        <x-jobmatch-icon name="download"/>
                        <h3>Export Placement Report</h3>
                        <p>Download the existing placement records as a CSV file.</p>
                        <span>Download CSV →</span>
                    </a>
                </div>
            </section>
        @else
            <section class="card alumni-workspace">
                <span class="section-icon">
                    <x-jobmatch-icon name="school"/>
                </span>
                <span class="overline">YOUR NEXT CHAPTER</span>
                <h2>Your career profile</h2>
                <p class="muted">Keep your alumni profile up to date and explore current career opportunities.</p>
                <div class="actions">
                    <a class="button" href="{{ route('portal.profile') }}">Update Profile</a>
                    <a class="button outline" href="{{ route('jobs.index') }}">Browse Jobs →</a>
                </div>
            </section>
        @endif
    @endif
</x-jobmatch-layout>
