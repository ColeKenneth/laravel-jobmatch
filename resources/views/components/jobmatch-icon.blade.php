@props(['name' => 'person'])
<svg {{ $attributes->class(['jm-icon']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
@switch($name)
@case('download')<path d="M12 3v12m-5-5 5 5 5-5M4 17v4h16v-4"/>@break
@case('eye')<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>@break
@case('target')<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><path d="m12 12 8-8m-1-2v3h3"/>@break
@case('team')<circle cx="9" cy="8" r="3"/><path d="M3 21v-3a6 6 0 0 1 12 0v3M17 5a3 3 0 0 1 0 6m1 4a5 5 0 0 1 3 4v2"/>@break
@case('school')<path d="m3 9 9-6 9 6M5 10v11h14V10M9 21v-6h6v6M10 10h4M12 1v2"/>@break
@case('skills')<path d="m12 3 9 5-9 5-9-5 9-5Zm-9 9 9 5 9-5M3 16l9 5 9-5"/>@break
@case('certificate')<rect x="4" y="3" width="16" height="13" rx="2"/><path d="M8 7h8M8 10h5m-5 6v6l4-2 4 2v-6"/>@break
@case('briefcase')<rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V3h8v4M3 12a22 22 0 0 0 18 0M12 11v4"/>@break
@case('chart')<path d="M4 3v18h17M8 16v-5m5 5V7m5 9v-8"/>@break
@case('pin')<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/>@break
@default<circle cx="12" cy="7" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/>
@endswitch
</svg>
