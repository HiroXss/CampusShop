@extends('layouts.public')

@section('title', 'Pedido #'.$pedido->id)

@section('content')
    <div class="container py-4">
        <a href="{{ route('mis-pedidos.index') }}" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left me-1"></i> Mis pedidos</a>

        @include('pedidos.detalle', ['pedido' => $pedido, 'esVendedor' => false])

        @if ($pedido->estado === 'pendiente')
            <div class="d-flex justify-content-end">
                <form method="POST" action="{{ route('pedidos.cancelar', $pedido) }}">
                    @csrf
                    <button class="btn btn-outline-danger"><i class="bi bi-x-circle me-1"></i> Cancelar pedido</button>
                </form>
            </div>
        @endif

        @if ($pedido->estado === 'entregado')
            <div class="cs-card p-4">
                @if ($pedido->resena)
                    <h5 class="fw-bold mb-3">Tu reseña</h6>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <x-cs.stars :value="$pedido->resena->puntuacion" />
                        <span class="text-secondary small">{{ $pedido->resena->created_at->format('d/m/Y') }}</span>
                    </div>
                    <p class="mb-0 text-secondary">{{ $pedido->resena->comentario ?: 'Sin comentario.' }}</p>
                @else
                    <h5 class="fw-bold mb-1">Califica este pedido</h5>
                    <p class="text-secondary small">Tu opinión ayuda a otros estudiantes a decidir.</p>
                    <form method="POST" action="{{ route('resenas.store', $pedido) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Puntuación</label>
                            <select name="puntuacion" class="form-select @error('puntuacion') is-invalid @enderror" required style="max-width:200px">
                                <option value="">Selecciona…</option>
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" @selected(old('puntuacion') == $i)>{{ $i }} estrella{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                            @error('puntuacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Comentario (opcional)</label>
                            <textarea name="comentario" rows="3" maxlength="500" class="form-control">{{ old('comentario') }}</textarea>
                        </div>
                        <button class="btn btn-primary">Enviar reseña</button>
                    </form>
                @endif
            </div>
        @endif
    </div>
@endsection
