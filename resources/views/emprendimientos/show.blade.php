@extends('layouts.public')

@section('title', $emprendimiento->nombre)

@php
    $esPropietario = auth()->check() && auth()->id() === $emprendimiento->user_id;
@endphp

@section('content')
    <div class="container py-4">
        {{-- Cabecera de la tienda (RF-13, RF-14) --}}
        <div class="cs-card overflow-hidden mb-4">
            <div style="height:150px;background:linear-gradient(135deg,var(--cs-primary),#7B6CFF);"></div>
            <div class="p-4">
                <div class="d-flex flex-column flex-md-row align-items-md-end gap-3" style="margin-top:-64px">
                    <img src="{{ $emprendimiento->logo ? imagen($emprendimiento->logo) : asset('img/logo-placeholder.svg') }}"
                         alt="{{ $emprendimiento->nombre }}"
                         class="rounded-4 border border-4 border-white" style="width:112px;height:112px;object-fit:cover;background:#fff;">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h1 class="h3 fw-bold mb-0">{{ $emprendimiento->nombre }}</h1>
                            @if ($emprendimiento->destacado)
                                <span class="chip status-azul"><i class="bi bi-star-fill"></i>Destacado</span>
                            @endif
                            @unless ($emprendimiento->isVisible())
                                <x-cs.status :estado="$emprendimiento->estado" />
                            @endunless
                        </div>
                        <div class="d-flex align-items-center gap-3 mt-1 flex-wrap">
                            <span class="text-secondary"><i class="bi bi-tag me-1"></i>{{ $emprendimiento->categoria->nombre }}</span>
                            @if ($emprendimiento->resenas_avg_puntuacion)
                                <span class="d-inline-flex align-items-center gap-1">
                                    <x-cs.stars :value="$emprendimiento->resenas_avg_puntuacion" :size="'0.8125rem'" />
                                    <span class="text-secondary small">{{ number_format($emprendimiento->resenas_avg_puntuacion, 1) }} ({{ $emprendimiento->resenas_count }})</span>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        @auth
                            @unless ($esPropietario)
                                <form method="POST" action="{{ route('conversaciones.iniciar', $emprendimiento) }}">
                                    @csrf
                                    <button class="btn btn-primary"><i class="bi bi-chat-dots me-1"></i> Chatear</button>
                                </form>
                            @else
                                <a href="{{ route('mi-tienda.index') }}" class="btn btn-outline-primary"><i class="bi bi-shop me-1"></i> Administrar</a>
                            @endunless
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary"><i class="bi bi-chat-dots me-1"></i> Chatear</a>
                        @endauth
                        @auth
                            @unless ($esPropietario)
                                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modal-reporte-tienda" title="Reportar">
                                    <i class="bi bi-flag"></i>
                                </button>
                            @endunless
                        @endauth
                    </div>
                </div>

                <p class="mt-3 mb-2 text-secondary">{{ $emprendimiento->descripcion }}</p>

                @if ($emprendimiento->whatsapp || $emprendimiento->instagram)
                    <div class="d-flex gap-3 small">
                        @if ($emprendimiento->whatsapp)
                            <a href="https://wa.me/57{{ preg_replace('/\D/', '', $emprendimiento->whatsapp) }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-1"></i>WhatsApp</a>
                        @endif
                        @if ($emprendimiento->instagram)
                            <span class="text-secondary"><i class="bi bi-instagram me-1"></i>{{ $emprendimiento->instagram }}</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="row g-4">
            {{-- Productos --}}
            <div class="col-lg-8">
                <h2 class="h5 fw-bold mb-3">Productos y servicios ({{ $productos->total() }})</h2>
                @if ($productos->isEmpty())
                    <x-cs.empty-state icon="bi-box-seam" title="Sin productos" description="Este emprendimiento todavía no ha publicado productos." />
                @else
                    <div class="row g-3">
                        @foreach ($productos as $producto)
                            <div class="col-6 col-md-4 d-flex">
                                <x-cs.product-card :producto="$producto" />
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">{{ $productos->links() }}</div>
                @endif
            </div>

            {{-- Reseñas --}}
            <div class="col-lg-4">
                <h2 class="h5 fw-bold mb-3">Reseñas recientes</h2>
                <div class="cs-card p-3">
                    @forelse ($resenas as $resena)
                        <div class="{{ ! $loop->last ? 'border-bottom pb-3 mb-3' : '' }}">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $resena->comprador->avatar ? imagen($resena->comprador->avatar) : asset('img/avatar-placeholder.svg') }}"
                                     alt="" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                                <div>
                                    <strong class="d-block small">{{ $resena->comprador->name }}</strong>
                                    <x-cs.stars :value="$resena->puntuacion" :size="'0.75rem'" />
                                </div>
                            </div>
                            <p class="mb-0 mt-2 small text-secondary">{{ $resena->comentario ?: 'Sin comentario.' }}</p>
                        </div>
                    @empty
                        <p class="text-secondary mb-0 small">Aún no hay reseñas de este emprendimiento.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @auth
        @unless ($esPropietario)
            <div class="modal fade" id="modal-reporte-tienda" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form class="modal-content" method="POST" action="{{ route('reportes.store') }}">
                        @csrf
                        <input type="hidden" name="tipo" value="emprendimiento">
                        <input type="hidden" name="id" value="{{ $emprendimiento->id }}">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">Reportar emprendimiento</h5>
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
        @endunless
    @endauth
@endsection
