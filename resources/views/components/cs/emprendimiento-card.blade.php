@props(['emprendimiento'])

@php
    $logo = $emprendimiento->logo;
    $promedio = $emprendimiento->resenas_avg_puntuacion;
    $cuenta = $emprendimiento->resenas_count;
@endphp

<a href="{{ route('emprendimientos.show', $emprendimiento) }}" class="text-decoration-none d-block h-100">
    <div class="cs-card cs-card-hover p-3 d-flex flex-column h-100">
        <div class="d-flex align-items-center gap-3 mb-2">
            <img src="{{ $logo ? imagen($logo) : asset('img/logo-placeholder.svg') }}"
                 alt="{{ $emprendimiento->nombre }}"
                 class="rounded-3" style="width:56px;height:56px;object-fit:cover;background:#E7EEFF;">
            <div class="min-w-0">
                <h6 class="fw-bold mb-0 text-truncate" style="color: var(--cs-text)">{{ $emprendimiento->nombre }}</h6>
                <span class="small text-secondary">{{ $emprendimiento->categoria->nombre }}</span>
            </div>
        </div>

        <p class="small text-secondary mb-2" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
            {{ $emprendimiento->descripcion }}
        </p>

        <div class="d-flex align-items-center gap-2 mt-auto pt-1">
            @if ($promedio)
                <x-cs.stars :value="$promedio" :size="'0.8125rem'" />
                <span class="small text-secondary">{{ number_format($promedio, 1) }} ({{ $cuenta }})</span>
            @else
                <span class="small text-secondary">Sin calificaciones todavía</span>
            @endif
            @if ($emprendimiento->destacado)
                <span class="chip status-azul ms-auto"><i class="bi bi-star-fill"></i>Destacado</span>
            @endif
        </div>
    </div>
</a>