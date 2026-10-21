@extends('layouts.public')

@section('title', $producto->nombre)

@php
    $img = $producto->imagenes->first()?->ruta;
    $esServicio = $producto->tipo === 'servicio';
    $esPropietario = auth()->check() && auth()->id() === $emprendimiento->user_id;
    $pedible = $producto->isPedible();
@endphp

@section('content')
    <div class="container py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('catalogo.index') }}">Catálogo</a></li>
                <li class="breadcrumb-item"><a href="{{ route('catalogo.index', ['categoria' => $producto->categoria->slug]) }}">{{ $producto->categoria->nombre }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($producto->nombre, 40) }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            {{-- Galería (RF-19) --}}
            <div class="col-lg-6">
                <div class="cs-card p-2">
                    <img id="galeria-principal"
                         src="{{ $img ? imagen($img) : asset('img/placeholder-producto.svg') }}"
                         alt="{{ $producto->nombre }}"
                         class="w-100 rounded-3" style="aspect-ratio:1/1;object-fit:cover;background:#E7EEFF;">
                </div>
                @if ($producto->imagenes->count() > 1)
                    <div class="d-flex gap-2 mt-2 flex-wrap">
                        @foreach ($producto->imagenes as $imagen)
                            <button type="button" class="btn p-0 border rounded-3 galeria-miniatura"
                                    data-src="{{ imagen($imagen->ruta) }}" style="width:72px;height:72px;overflow:hidden;">
                                <img src="{{ imagen($imagen->ruta) }}" alt="" class="w-100 h-100" style="object-fit:cover;">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Información --}}
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="chip status-azul">{{ $esServicio ? 'Servicio' : 'Producto' }}</span>
                    <x-cs.status :estado="$producto->estado" />
                </div>
                <h1 class="h3 fw-bold mb-2">{{ $producto->nombre }}</h1>

                <a href="{{ route('emprendimientos.show', $emprendimiento) }}" class="text-decoration-none d-inline-flex align-items-center gap-2 mb-3">
                    <img src="{{ $emprendimiento->logo ? imagen($emprendimiento->logo) : asset('img/logo-placeholder.svg') }}"
                         alt="{{ $emprendimiento->nombre }}" class="rounded-3" style="width:32px;height:32px;object-fit:cover;">
                    <span class="fw-semibold" style="color:var(--cs-text)">{{ $emprendimiento->nombre }}</span>
                </a>

                @if ($emprendimiento->resenas_avg_puntuacion)
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <x-cs.stars :value="$emprendimiento->resenas_avg_puntuacion" />
                        <span class="text-secondary small">{{ number_format($emprendimiento->resenas_avg_puntuacion, 1) }} ({{ $emprendimiento->resenas_count }} reseñas)</span>
                    </div>
                @endif

                <div class="price-display mb-3">{{ cop($producto->precio) }}</div>

                @if ($producto->stock !== null)
                    <p class="small mb-3">
                        @if ($producto->stock > 0)
                            <span class="text-success"><i class="bi bi-check-circle me-1"></i>Disponibles: {{ $producto->stock }}</span>
                        @else
                            <span class="text-danger"><i class="bi bi-x-circle me-1"></i>Agotado</span>
                        @endif
                    </div>
                @endif

                <p class="text-secondary">{{ $producto->descripcion }}</p>

                @auth
                    @if ($esPropietario)
                        <div class="alert alert-info py-2">Este producto es de tu emprendimiento. <a href="{{ route('mi-tienda.productos.editar', $producto) }}">Edítalo aquí</a>.</div>
                    @elseif ($pedible)
                        <form method="POST" action="{{ route('carrito.agregar', $producto) }}" class="d-flex align-items-center gap-2 mb-2">
                            @csrf
                            <div class="input-group" style="width:130px">
                                <button type="button" class="btn btn-outline-secondary" data-qty-menos>−</button>
                                <input type="number" id="cantidad" name="cantidad" value="1" min="1"
                                       max="{{ $producto->stock ?? '' }}" class="form-control text-center" data-qty-input>
                                <button type="button" class="btn btn-outline-secondary" data-qty-mas>+</button>
                            </div>
                            <button class="btn btn-accent flex-grow-1"><i class="bi bi-bag-plus me-1"></i> Agregar al carrito</button>
                        </form>
                    @else
                        <button class="btn btn-secondary w-100 mb-2" disabled>Producto agotado</button>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">Ingresa para comprar</a>
                @endauth

                @auth
                    @if (! $esPropietario)
                        <div class="d-flex gap-2">
                            <form method="POST" action="{{ route('conversaciones.iniciar', $emprendimiento) }}" class="flex-grow-1">
                                @csrf
                                <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                <button class="btn btn-outline-primary w-100"><i class="bi bi-chat-dots me-1"></i> Chatear con la tienda</button>
                            </form>
                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modal-reporte" title="Reportar">
                                <i class="bi bi-flag"></i>
                            </button>
                        </div>
                    @endif
                @endauth

                @if ($emprendimiento->whatsapp || $emprendimiento->instagram)
                    <hr>
                    <div class="small text-secondary">
                        @if ($emprendimiento->whatsapp)
                            <div><i class="bi bi-whatsapp me-1"></i> <a href="https://wa.me/57{{ preg_replace('/\D/', '', $emprendimiento->whatsapp) }}" target="_blank" rel="noopener">WhatsApp</a></div>
                        @endif
                        @if ($emprendimiento->instagram)
                            <div><i class="bi bi-instagram me-1"></i> {{ $emprendimiento->instagram }}</div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Descripción y reseñas --}}
        <ul class="nav nav-tabs mt-5" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-desc" type="button">Descripción</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-res" type="button">Reseñas ({{ $emprendimiento->resenas_count }})</button></li>
        </ul>
        <div class="tab-content cs-card p-4 border-top-0 rounded-top-0">
            <div class="tab-pane fade show active" id="tab-desc">
                <p class="mb-0" style="white-space: pre-line;">{{ $producto->descripcion }}</p>
            </div>
            <div class="tab-pane fade" id="tab-res">
                @forelse ($resenas as $resena)
                    <div class="d-flex gap-3 {{ ! $loop->last ? 'border-bottom pb-3 mb-3' : '' }}">
                        <img src="{{ $resena->comprador->avatar ? imagen($resena->comprador->avatar) : asset('img/avatar-placeholder.svg') }}"
                             alt="" class="rounded-circle" style="width:42px;height:42px;object-fit:cover;">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <strong>{{ $resena->comprador->name }}</strong>
                                <x-cs.stars :value="$resena->puntuacion" :size="'0.75rem'" />
                            </div>
                            <p class="mb-0 text-secondary small">{{ $resena->comentario ?: 'Sin comentario.' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-secondary mb-0">Este emprendimiento aún no tiene reseñas.</p>
                @endforelse
            </div>
        </div>

        {{-- Relacionados --}}
        @if ($relacionados->isNotEmpty())
            <section class="mt-5">
                <h2 class="h4 fw-bold mb-3">También te puede interesar</h2>
                <div class="row g-3">
                    @foreach ($relacionados as $rel)
                        <div class="col-6 col-lg-3 d-flex">
                            <x-cs.product-card :producto="$rel" />
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    {{-- Modal de reporte (RF-50) --}}
    @auth
        @if (! $esPropietario)
            <div class="modal fade" id="modal-reporte" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form class="modal-content" method="POST" action="{{ route('reportes.store') }}">
                        @csrf
                        <input type="hidden" name="tipo" value="producto">
                        <input type="hidden" name="id" value="{{ $producto->id }}">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">Reportar producto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label">Motivo</label>
                            <textarea name="motivo" rows="3" maxlength="300" required class="form-control" placeholder="Describe el problema…"></textarea>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Enviar reporte</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endauth
@endsection
