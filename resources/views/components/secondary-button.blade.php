@props(['type' => 'button'])

<button {{ $attributes->merge(['type' => $type, 'class' => 'btn btn-outline-secondary']) }}>
    {{ $slot }}
</button>