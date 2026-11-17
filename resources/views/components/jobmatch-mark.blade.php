@props(['src' => null, 'name' => 'WCC', 'symbol' => null])
@php
    $safeImage = is_string($src) && (str_starts_with($src, '/images/jobmatch/current/') || in_array(parse_url($src, PHP_URL_SCHEME), ['http', 'https'], true));
@endphp
<div @class(['company-monogram', 'has-image' => $safeImage]) aria-hidden="true">@if($safeImage)<img src="{{ $src }}" alt="" loading="lazy" data-image-fallback>@endif<span>{{ $symbol ?? mb_substr($name,0,1) }}</span></div>
