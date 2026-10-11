@props(['active' => false])

@php
    $clases = $active ? 'nav-link active fw-semibold' : 'nav-link fw-medium';
@endphp

<a {{ $attributes->merge(['class' => $clases]) }}>{{ $slot }}</a>