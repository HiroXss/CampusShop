@props(['type' => 'text', 'name' => null])

@php
    $clase = 'form-control';
    if ($name && $errors->has($name)) {
        $clase .= ' is-invalid';
    }
@endphp

<input {{ $attributes->merge(['type' => $type, 'class' => $clase]) }}>