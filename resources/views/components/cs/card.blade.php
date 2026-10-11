@props(['title' => '', 'subtitle' => null, 'icon' => null, 'hover' => false])

<div class="cs-card {{ $hover ? 'cs-card-hover' : '' }} {{ $attributes->get('class') }}" {{ $attributes->except('class') }}>
    @if ($title || $icon)
        <div class="d-flex align-items-start gap-2 p-3 pb-0">
            @if ($icon)<i class="bi {{ $icon }} fs-5" style="color: var(--cs-primary)"></i>@endif
            <div>
                @if ($title)<h6 class="fw-bold mb-0">{{ $title }}</h6>@endif
                @if ($subtitle)<small class="text-secondary">{{ $subtitle }}</small>@endif
            </div>
        </div>
    @endif
    <div class="p-3 {{ $title || $icon ? 'pt-2' : '' }}">
        {{ $slot }}
    </div>
</div>