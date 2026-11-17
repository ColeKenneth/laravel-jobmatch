@props(['param', 'value', 'label'])
<a @class(['filter-chip', 'selected' => (string) request($param) === (string) $value]) href="{{ request()->fullUrlWithQuery([$param => (string) request($param) === (string) $value ? null : $value, 'page' => null]) }}" aria-current="{{ (string) request($param) === (string) $value ? 'true' : 'false' }}">{{ $label }}</a>
