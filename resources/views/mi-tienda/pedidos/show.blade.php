@extends('layouts.dashboard')

@section('title', 'Detalle del pedido #'.$pedido->id)

@section('actions')
    <a href="{{ route('mi-tienda.pedidos.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Volver</a>
@endsection

@section('content')
    @include('pedidos.detalle', ['esVendedor' => true])

    <div class="cs-card p-3">
        <h6 class="fw-bold mb-3">Acciones</h4>
        <div class="d-flex flex-wrap gap-2">
            @if ($pedido->estado === 'pendiente')
                <form method="POST" action="{{ route('mi-tienda.pedidos.confirmar', $pedido) }}">
                    @csrf
                    <button class="btn btn-success"><i class="bi bi-check2-circle me-1"></i> Confirmar pedido</button>
                </form>
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modal-rechazar">
                    <i class="bi bi-x-circle me-1"></i> Rechazar
                </button>
            @elseif ($pedido->estado === 'confirmado')
                <form method="POST" action="{{ route('mi-tienda.pedidos.entregar', $pedido) }}">
                    @csrf
                    <button class="btn btn-success"><i class="bi bi-check2-circle me-1"></i> Marcar como entregado</button>
                </form>
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modal-cancelar">
                    <i class="bi bi-x-circle me-1"></i> Cancelar pedido
                </button>
            @else
                <p class="text-secondary mb-0">No hay acciones disponibles para un pedido {{ estadoLabel($pedido->estado) }}.</p>
            @endif
        </div>
    </div>

    {{-- Rechazar (RF-40) --}}
    <div class="modal fade" id="modal-rechazar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('mi-tienda.pedidos.rechazar', $pedido) }}">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Rechazar pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Motivo del rechazo</label>
                    <textarea name="motivo" rows="3" maxlength="300" required class="form-control" placeholder="Ej: sin stock disponible…"></textarea>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rechazar pedido</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Cancelar (RF-44) --}}
    <div class="modal fade" id="modal-cancelar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('mi-tienda.pedidos.cancelar', $pedido) }}">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Cancelar pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-secondary">Al cancelar se devolverá el stock reservado.</p>
                    <label class="form-label">Motivo de la cancelación</label>
                    <textarea name="motivo" rows="3" maxlength="300" required class="form-control" placeholder="Ej: no pudimos cumplir con la entrega…"></textarea>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Volver</button>
                    <button type="submit" class="btn btn-danger">Cancelar pedido</button>
                </div>
            </form>
        </div>
    </div>
@endsection
