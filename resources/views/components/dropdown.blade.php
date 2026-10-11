@props(['align' => 'right', 'contentClasses' => ''])

@php
    $posicion = $align === 'left' ? 'start-0' : 'end-0';
@endphp

<div class="position-relative" x-data>
    <div @click="open = ! open">{{ $trigger ?? $slot }}</div>
    <div x-show="open" x-on:click.outside="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="dropdown-menu dropdown-menu-{{ $align }} {{ $posicion }} show mt-2 shadow {{ $contentClasses }}" style="display:block"
         {{ $attributes }}>
        {{ $slot }}
    </div>
</div>