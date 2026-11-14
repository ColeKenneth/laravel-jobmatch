<section class="card welcome">
    <div>
        <h1>Welcome back, {{ $user->first_name }}! 👋</h1>
        <p>@if($student){{ $student->program }} · {{ $student->year_level->getLabel() }} · Student ID: {{ $student->student_id_number }}@else Complete your educational background to get started. @endif</p>
    </div>
    <div class="welcome-actions">
        <span class="account-status">
            <i aria-hidden="true"></i>
            Active Student Account
        </span>
        <a class="button" href="{{ route('portal.profile') }}">✎ Edit Profile</a>
    </div>
</section>
<div class="dashboard-grid design-dashboard">
    <section class="card completion-card">
        <h2 class="icon-heading">
            <span class="section-icon">
                <x-jobmatch-icon name="person"/>
            </span>
            Profile Completion
        </h2>
        <div class="completion-content">
            <div class="radial-progress" style="--completion: {{ $completion['percent'] }}%" role="progressbar" aria-label="Profile completion" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $completion['percent'] }}">
                <div class="radial-inner">
                    <strong>{{ $completion['percent'] }}%</strong>
                    <small>Complete</small>
                </div>
            </div>
            <div class="completion-checklist">
                @if(count($completion['missing']))
                    <strong>Missing items:</strong>
                    @foreach($completion['missing'] as $item)
                        <a href="{{ route($item['route']) }}">
                            <span aria-hidden="true">◉</span>
                            {{ $item['label'] }}
                            (
                            {{ $item['points'] }}
                            %)
                        </a>
                    @endforeach
                @else
                    <strong>Profile details completed.</strong>
                @endif
            </div>
        </div>
        <div class="dashboard-card-footer">
            <a class="button stretch" href="{{ route('portal.profile') }}">Update Profile</a>
        </div>
    </section>
    <section class="card inventory-card">
        <h2 class="icon-heading">
            <span class="section-icon">
                <x-jobmatch-icon name="skills"/>
            </span>
            Skills Inventory &amp; Proficiency
        </h2>
        <p class="dashboard-summary">Summarized Overview (Total: {{ $skillCount }} {{ $skillCount === 1 ? 'Skill' : 'Skills' }})</p>
        <div class="rows">
            @forelse($skills as $item)
                <div>
                    <strong>{{ $item->skill?->name }}</strong>
                    <div class="skill-level">
                        <span class="badge">{{ [1=>'Beginner',2=>'Basic',3=>'Intermediate',4=>'Advanced',5=>'Expert'][(int) $item->proficiency_level] ?? 'Not specified' }}</span>
                        <small>{{ $item->years_of_experience ?? 0 }} yrs exp</small>
                    </div>
                </div>
            @empty
                <p class="muted">No skills added yet.</p>
            @endforelse
        </div>
        <a class="card-link" href="{{ route('portal.skills') }}">Quick links: Manage Skills →</a>
    </section>
    <section class="card certification-card">
        <h2 class="icon-heading">
            <span class="section-icon">
                <x-jobmatch-icon name="certificate"/>
            </span>
            Certifications
        </h2>
        <p class="dashboard-summary">Summarized Overview (Total: {{ $certificationCount }})</p>
        @forelse($certifications as $item)
            <div class="certificate">
                <span class="certificate-icon" aria-hidden="true">✹</span>
                <div>
                    <strong>{{ $item->name }}</strong>
                    <small>Issued by {{ $item->issuing_authority }}@if($item->expiry_date) · Expires {{ $item->expiry_date->format('Y') }}@endif</small>
                </div>
            </div>
        @empty
            <p class="muted">No certifications added yet.</p>
        @endforelse
        <a class="card-link" href="{{ route('portal.certifications') }}">Quick links: Manage Certifications →</a>
    </section>
    <section class="card dashboard-jobs">
        <div class="section-heading dashboard-opportunities-heading">
            <div class="dashboard-section-title">
                <span class="section-icon">
                    <x-jobmatch-icon name="briefcase"/>
                </span>
                <div>
                    <h2>Job Opportunities</h2>
                    <p class="dashboard-summary">Recent active postings from employer listings</p>
                </div>
            </div>
            <label class="dashboard-search">
                <span class="sr-only">Search dashboard jobs</span>
                <input type="search" data-dashboard-search aria-controls="dashboard-job-list" placeholder="Search jobs...">
            </label>
        </div>
        <div class="card-grid" id="dashboard-job-list">
            @forelse($jobs as $job)
                @php($sharedSkills = $job->requiredSkills->whereIn('skill_id', $ownedSkillIds)->pluck('skill.name')->filter())
                <article class="card dashboard-job-card" data-dashboard-job data-job-search="{{ strtolower($job->title.' '.$job->employer?->company_name) }}">
                    <div class="dashboard-job-top">
                        <x-jobmatch-mark :src="$job->
                            employer?->logo_url" :name="$job->employer?->company_name ?? 'WCC'" />
                            @if($sharedSkills->count())
                                <span class="shared-skill-badge">{{ $sharedSkills->count() }} shared {{ $sharedSkills->count() === 1 ? 'skill' : 'skills' }}</span>
                            @endif
                        </div>
                        <h3>{{ $job->title }}</h3>
                        <p>{{ $job->employer?->company_name }}</p>
                        @if($sharedSkills->count())
                            <small class="shared-skills">✓ {{ $sharedSkills->join(', ') }}</small>
                        @else
                            <small>⌖ {{ $job->location }}</small>
                        @endif
                        <div class="dashboard-job-actions">
                            <a class="button outline" href="{{ route('jobs.show',$job) }}">Details</a>
                            <a class="button" href="{{ route('employers.show',$job->employer) }}">Employer</a>
                        </div>
                    </article>
                @empty
                    <p class="empty">No active job postings yet.</p>
                @endforelse
            </div>
            <p class="muted" data-dashboard-empty role="status" hidden>No jobs in this list match your search.</p>
            <div class="dashboard-opportunities-footer">
                <span class="muted">Explore current opportunities from the WCC employer network.</span>
                <a class="button" href="{{ route('jobs.index') }}">View All Jobs</a>
            </div>
        </section>
        <section class="card dashboard-stats">
            <div class="section-heading">
                <div>
                    <h2 class="icon-heading">
                        <span class="section-icon">
                            <x-jobmatch-icon name="chart"/>
                        </span>
                        Placement Status
                    </h2>
                    <p class="dashboard-summary">Tracking recorded placements</p>
                </div>
                <div class="total-records">
                    <strong>{{ $placements->count() }}</strong>
                    <small>Total Records</small>
                </div>
            </div>
            <div class="stats-grid">
                @foreach(['pending'=>'Pending','rejected'=>'Rejected','hired'=>'Hired','offer_accepted'=>'Offer Accepted'] as $status=>$label)
                    <a class="stat-tile stat-{{ $status }}" href="{{ route('portal.placements',['status'=>$status]) }}">
                        <strong>{{ $placements->where('placement_status', App\Enum\PlacementStatus::from($status))->count() }}</strong>
                        <small>{{ $label }}</small>
                    </a>
                @endforeach
            </div>
            <a class="card-link" href="{{ route('portal.placements') }}">Quick link: View Detailed Placements →</a>
        </section>
    </div>
