@props([
    'estado' => '',
    'label' => null,
    'icon' => null,
])

@php
    $label = $label ?: estadoLabel($estado);
@endphp

<span class="chip {{ estadoChip($estado) }}">
    @if ($icon)<i class="bi {{ $icon }}"></i>@endif
    {{ $label }}
</span>