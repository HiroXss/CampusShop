@props([
    'valor' => '—',
    'label' => '',
    'icon' => 'bi-bag',
    'color' => 'indigo',
    'sufijo' => null,
])

<div class="cs-card metric-card p-3 h-100">
    <div class="metric-icon {{ $color }}">
        <i class="bi {{ $icon }}"></i>
    </div>
    <div>
        <div class="metric-value">
            {{ $valor }}
            @if ($sufijo)<span class="fs-6 text-secondary fw-normal">{{ $sufijo }}</span>@endif
        </div>
        <div class="metric-label">{{ $label }}</div>
    </div>
</div>