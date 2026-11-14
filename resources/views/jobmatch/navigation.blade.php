@if($portal)
    <a @class(['active'=>request()->routeIs('dashboard')]) @if(request()->routeIs('dashboard')) aria-current="page" @endif href="{{ route('dashboard') }}">Home</a>
    <a @class(['active'=>request()->routeIs('jobs.*')]) @if(request()->routeIs('jobs.*')) aria-current="page" @endif href="{{ route('jobs.index') }}">Jobs</a>
    <a @class(['active'=>request()->routeIs('portal.profile')]) @if(request()->routeIs('portal.profile')) aria-current="page" @endif href="{{ route('portal.profile') }}">My Profile</a>
    @if(auth()->user()->role === App\Enum\UserRole::STUDENT)
        <a @class(['active'=>request()->routeIs('portal.resume')]) @if(request()->routeIs('portal.resume')) aria-current="page" @endif href="{{ route('portal.resume') }}">My Resume</a>
        <a @class(['active'=>request()->routeIs('portal.skills','portal.certifications')]) @if(request()->routeIs('portal.skills','portal.certifications')) aria-current="page" @endif href="{{ route('portal.skills') }}">Skills &amp; Certifications</a>
        <a @class(['active'=>request()->routeIs('portal.placements')]) @if(request()->routeIs('portal.placements')) aria-current="page" @endif href="{{ route('portal.placements') }}">My Placements</a>
    @endif
    @if(auth()->user()->role === App\Enum\UserRole::EMPLOYER)
        <a @class(['active'=>request()->routeIs('portal.company')]) @if(request()->routeIs('portal.company')) aria-current="page" @endif href="{{ route('portal.company') }}">Company Profile</a>
        <a @class(['active'=>request()->routeIs('portal.jobs')]) @if(request()->routeIs('portal.jobs')) aria-current="page" @endif href="{{ route('portal.jobs') }}">Manage Jobs</a>
        <a @class(['active'=>request()->routeIs('portal.placements')]) @if(request()->routeIs('portal.placements')) aria-current="page" @endif href="{{ route('portal.placements') }}">Placements</a>
    @endif
    @if(in_array(auth()->user()->role,[App\Enum\UserRole::ADMIN,App\Enum\UserRole::PLACEMENT_OFFICER],true))
        <a @class(['active'=>request()->routeIs('portal.placements')]) @if(request()->routeIs('portal.placements')) aria-current="page" @endif href="{{ route('portal.placements') }}">Placement Records</a>
        <a href="{{ route('portal.reports') }}">Export Report</a>
    @endif
    <a @class(['active'=>request()->routeIs('about')]) @if(request()->routeIs('about')) aria-current="page" @endif href="{{ route('about') }}">About Us</a>
@else
    <a @class(['active'=>request()->routeIs('jobs.*')]) @if(request()->routeIs('jobs.*')) aria-current="page" @endif href="{{ route('jobs.index') }}">Jobs</a>
    <a @class(['active'=>request()->routeIs('skills.*')]) @if(request()->routeIs('skills.*')) aria-current="page" @endif href="{{ route('skills.index') }}">Browse Skills</a>
    <a @class(['active'=>request()->routeIs('employers.*')]) @if(request()->routeIs('employers.*')) aria-current="page" @endif href="{{ route('employers.index') }}">Employer Directory</a>
    <a @class(['active'=>request()->routeIs('about')]) @if(request()->routeIs('about')) aria-current="page" @endif href="{{ route('about') }}">About Us</a>
    @auth
        <a class="button" href="{{ route('dashboard') }}">Home</a>
    @endauth
@endif
