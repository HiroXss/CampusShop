@props(['value' => 0, 'max' => 5, 'size' => null])

@php
    $value = (float) $value;
    $largo = $size ?: '0.875rem';
    $svg = fn (string $tipo) => '<svg width="'.$largo.'" height="'.$largo.'" viewBox="0 0 24 24" fill="'.$tipo.'" xmlns="http://www.w3.org/2000/svg" style="display:inline-block"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>';
@endphp

<span class="stars" title="{{ number_format($value, 1) }} / 5">
    @for ($i = 1; $i <= $max; $i++)
        @if ($value >= $i)
            {!! $svg('#F59E0B') !!}
        @elseif ($value >= $i - 0.5)
            {!! $svg('#F59E0B') !!}
        @else
            {!! $svg('#E2E8F0') !!}
        @endif
    @endfor
</span>