@props(['producto'])

@php
    $img = $producto->imagenes->first()?->ruta;
    $esServicio = $producto->tipo === 'servicio';
    $pedible = $producto->isPedible();
@endphp

<div class="cs-card cs-card-hover product-card d-flex flex-column h-100">
    <a href="{{ route('catalogo.producto', $producto) }}" class="text-decoration-none d-block">
        <div class="product-thumb">
            <img src="{{ $img ? imagen($img) : asset('img/placeholder-producto.svg') }}"
                 alt="{{ $producto->nombre }}" loading="lazy">
        </div>
    </a>

    <div class="p-3 d-flex flex-column flex-grow-1">
        <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="product-brand">{{ $producto->emprendimiento->nombre }}</span>
            <x-cs.status :estado="$producto->estado" :label="$esServicio ? 'Servicio' : 'Producto'" />
        </div>

        <a href="{{ route('catalogo.producto', $producto) }}" class="text-decoration-none mb-2">
            <div class="product-title">{{ $producto->nombre }}</div>
        </a>

        @if ($producto->emprendimiento->resenas_avg_puntuacion)
            <div class="d-flex align-items-center gap-1 small mb-2">
                <x-cs.stars :value="$producto->emprendimiento->resenas_avg_puntuacion" :size="'0.75rem'" />
                <span class="text-secondary">({{ $producto->emprendimiento->resenas_count }})</span>
            </div>
        @endif

        <div class="d-flex align-items-center justify-content-between mt-auto pt-2">
            <span class="price-display">{{ cop($producto->precio) }}</span>
            @if ($pedible)
                <form method="POST" action="{{ route('carrito.agregar', $producto) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn-add" title="{{ $esServicio ? 'Solicitar' : 'Agregar al carrito' }}">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </form>
            @else
                <button class="btn-add" disabled style="background:#CBD5E1;cursor:not-allowed" title="No disponible">
                    <i class="bi bi-plus-lg"></i>
                </button>
            @endif
        </div>
    </div>
</div>