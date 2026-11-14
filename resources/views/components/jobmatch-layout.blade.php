@props(['portal' => false, 'title' => 'WCC JobMatchAI'])
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#00285b">
        <meta name="description" content="WCC JobMatchAI — career opportunities, skills, and employer connections for the World Citi Colleges community.">
        <title>{{ $title }} | WCC JobMatchAI</title>
        <link rel="icon" type="image/png" href="{{ asset('images/jobmatch/current/app-icon-192.png') }}">
        <link rel="stylesheet" href="{{ asset('css/jobmatch-current.css') }}">
        <link rel="stylesheet" href="{{ asset('css/jobmatch-stitch.css') }}?v=13">
    </head>
    <body id="page-top" class="{{ $portal ? 'portal-page' : 'public-page' }} stitch-ui">
        <a class="skip-link" href="#main-content">Skip to content</a>
        @include('jobmatch.layout.header')
        <main id="main-content" class="{{ $portal ? 'portal-main' : 'public-main' }}" tabindex="-1">
            @include('jobmatch.layout.feedback')
            {{ $slot }}
        </main>
        @include('jobmatch.layout.footer')
        <script src="{{ asset('js/jobmatch-current.js') }}" defer></script>
    </body>
</html>
