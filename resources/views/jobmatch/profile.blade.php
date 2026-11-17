<x-jobmatch-layout portal title="My Profile">
    <div class="profile-page">
        <nav class="profile-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}">Home</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">My Profile</span>
        </nav>
        <header class="profile-heading">
            <div>
                <span class="profile-eyebrow">YOUR ACCOUNT</span>
                <h1>My Profile</h1>
                <p>Keep your personal and academic information up to date.</p>
            </div>
            <a class="profile-back" href="{{ route('dashboard') }}">← Back to Home</a>
        </header>
        <section class="profile-identity" aria-label="Account summary">
            <div class="profile-identity-main">
                <div class="profile-large-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->first_name,0,1).mb_substr($user->last_name,0,1)) }}</div>
                <div>
                    <h2>{{ $user->full_name }}</h2>
                    <p>{{ $user->email }}</p>
                    <span class="profile-role">{{ $user->role->getLabel() }}</span>
                </div>
            </div>
            <span class="profile-active">
                <i aria-hidden="true"></i>
                Active account
            </span>
        </section>
        <div class="profile-grid">
            <aside class="profile-sidebar">
                <section class="profile-side-card">
                    <h2>Profile details</h2>
                    <a href="#personal-information" class="profile-section-link">
                        <span aria-hidden="true">01</span>
                        Personal information ↗
                    </a>
                    @if(in_array($user->role,[App\Enum\UserRole::STUDENT,App\Enum\UserRole::ALUMNI],true))
                        <a href="#education-information" class="profile-section-link">
                            <span aria-hidden="true">02</span>
                            Educational background ↗
                        </a>
                    @endif
                    <div class="profile-side-divider"></div>
                    <p>Your account type</p>
                    <strong>{{ $user->role->getLabel() }}</strong>
                </section>
                @if($user->role === App\Enum\UserRole::STUDENT)
                    <section class="profile-side-card profile-quick-links">
                        <h2>Your career profile</h2>
                        <a href="{{ route('portal.resume') }}">
                            My Resume
                            <span aria-hidden="true">→</span>
                        </a>
                        <a href="{{ route('portal.skills') }}">
                            Skills
                            <span aria-hidden="true">→</span>
                        </a>
                        <a href="{{ route('portal.certifications') }}">
                            Certifications
                            <span aria-hidden="true">→</span>
                        </a>
                    </section>
                @elseif($user->role === App\Enum\UserRole::EMPLOYER)
                    <section class="profile-side-card profile-quick-links">
                        <h2>Your company</h2>
                        <a href="{{ route('portal.company') }}">
                            Company profile
                            <span aria-hidden="true">→</span>
                        </a>
                        <a href="{{ route('portal.jobs') }}">
                            Manage jobs
                            <span aria-hidden="true">→</span>
                        </a>
                    </section>
                @endif
            </aside>
            <form class="profile-form" method="POST" action="{{ route('portal.profile.update') }}" data-profile-form data-progressive-submit>
                @csrf
                @method('PUT')
                <section class="profile-form-section" id="personal-information" aria-labelledby="personal-title">
                    <div class="profile-section-heading">
                        <span class="profile-section-number">
                            <x-jobmatch-icon name="person"/>
                        </span>
                        <div>
                            <h2 id="personal-title">Personal information</h2>
                            <p>Your name and contact details for your JobMatchAI account.</p>
                        </div>
                    </div>
                    <div class="form-grid">
                        <label>
                            First name
                            <input name="first_name" type="text" value="{{ old('first_name',$user->first_name) }}" autocomplete="given-name" maxlength="100" required>
                        </label>
                        <label>
                            Middle name
                            <span class="field-optional">Optional</span>
                            <input name="middle_name" type="text" value="{{ old('middle_name',$user->middle_name) }}" autocomplete="additional-name" maxlength="100">
                        </label>
                        <label>
                            Last name
                            <input name="last_name" type="text" value="{{ old('last_name',$user->last_name) }}" autocomplete="family-name" maxlength="100" required>
                        </label>
                        <label>
                            Email address
                            <input name="email" type="email" value="{{ old('email',$user->email) }}" autocomplete="email" maxlength="255" required>
                            <small>Use an email address you can access.</small>
                        </label>
                    </div>
                </section>
                @if(in_array($user->role,[App\Enum\UserRole::STUDENT,App\Enum\UserRole::ALUMNI],true))
                    <section class="profile-form-section" id="education-information" aria-labelledby="education-title">
                        <div class="profile-section-heading">
                            <span class="profile-section-number">
                                <x-jobmatch-icon name="school"/>
                            </span>
                            <div>
                                <h2 id="education-title">Educational background</h2>
                                <p>Keep your academic details accurate and current.</p>
                            </div>
                        </div>
                        <div class="form-grid">
                            @if($user->role === App\Enum\UserRole::STUDENT)
                                <label>
                                    Student ID
                                    <input name="student_id_number" value="{{ old('student_id_number',$student?->student_id_number) }}" maxlength="30" required>
                                </label>
                                <label>
                                    Degree program
                                    <input name="program" value="{{ old('program',$student?->program === 'Not specified' ? '' : $student?->program) }}" placeholder="e.g. BS Information Technology" maxlength="100" required>
                                </label>
                                <label>
                                    Year level
                                    <select name="year_level" required>
                                        @foreach(App\Enum\YearLevel::cases() as $level)
                                            <option value="{{ $level->value }}" @selected(old('year_level',$student?->year_level?->value)===$level->value)>{{ $level->getLabel() }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <label>
                                    GWA
                                    <input name="gwa" type="number" value="{{ old('gwa', $student?->gwa > 0 ? $student->gwa : '') }}" placeholder="e.g. 1.75" min="1" max="5" step="0.01" required>
                                    <small>Enter your current general weighted average (1.00–5.00).</small>
                                </label>
                                <label>
                                    Graduation date
                                    <span class="field-optional">Optional</span>
                                    <input name="graduation_date" type="date" value="{{ old('graduation_date',$student?->graduation_date?->format('Y-m-d')) }}">
                                </label>
                            @else
                                <label>
                                    Degree program
                                    <input name="program" value="{{ old('program',$alumni?->program === 'Not specified' ? '' : $alumni?->program) }}" placeholder="e.g. BS Information Technology" maxlength="100" required>
                                </label>
                                <label>
                                    Graduation date
                                    <input name="graduation_date" type="date" value="{{ old('graduation_date',$alumni?->graduation_date?->format('Y-m-d')) }}" required>
                                </label>
                            @endif
                        </div>
                    </section>
                @endif
                <div class="profile-save-bar">
                    <div>
                        <strong data-profile-state role="status">Your profile, your next opportunity.</strong>
                        <p>Review your details before saving.</p>
                    </div>
                    <div class="profile-save-actions">
                        <button type="reset" class="button outline">Reset form</button>
                        <button type="submit" class="button" data-submit-button>
                            <span class="submit-spinner" aria-hidden="true"></span>
                            <span data-submit-label>Save Profile</span>
                            <span aria-hidden="true">→</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-jobmatch-layout>
