@props(['desktop' => null, 'mobile' => null])

@if ($desktop)
    <img src="{{ asset('images/jobmatch/login/'.$desktop.'.svg') }}" alt="" aria-hidden="true" class="jm-icon {{ $mobile ? 'jm-desktop' : '' }}" />
@endif
@if ($mobile)
    <img src="{{ asset('images/jobmatch/login/'.$mobile.'.svg') }}" alt="" aria-hidden="true" class="jm-icon jm-mobile" />
@endif
