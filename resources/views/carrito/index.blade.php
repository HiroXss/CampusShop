@extends('layouts.public')

@section('title', 'Carrito')

@section('content')
    <div class="container py-4">
        <h1 class="h3 fw-bold mb-4"><i class="bi bi-bag me-2"></i>Mi carrito</h1>

        @if ($productos->isEmpty())
            <x-cs.empty-state icon="bi-bag" title="Tu carrito está vacío" description="Explora el catálogo y agrega productos o servicios.">
                <a href="{{ route('catalogo.index') }}" class="btn btn-primary">Ir al catálogo</a>
            </x-cs.empty-state>
        @else
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="cs-card p-0 overflow-hidden">
                        @foreach ($productos as $producto)
                            @php $cantidad = $cantidades[$producto->id] ?? 0; @endphp
                            <div class="d-flex gap-3 p-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
                                <img src="{{ $producto->imagenes->first()?->ruta ? imagen($producto->imagenes->first()->ruta) : asset('img/placeholder-producto.svg') }}"
                                     alt="{{ $producto->nombre }}" class="rounded-3" style="width:84px;height:84px;object-fit:cover;background:#E7EEFF;">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between gap-2">
                                        <div>
                                            <a href="{{ route('catalogo.producto', $producto) }}" class="fw-semibold text-decoration-none" style="color:var(--cs-text)">{{ $producto->nombre }}</a>
                                            <div class="small text-secondary">{{ $producto->emprendimiento->nombre }}</div>
                                            @if ($producto->stock !== null)
                                                <div class="small text-secondary">Disponibles: {{ $producto->stock }}</div>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold">{{ cop((float) $producto->precio * $cantidad) }}</div>
                                            <div class="small text-secondary">{{ cop($producto->precio) }} c/u</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <form method="POST" action="{{ route('carrito.actualizar', $producto) }}" class="d-flex align-items-center gap-1">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="cantidad" value="{{ $cantidad }}" min="0" max="{{ $producto->stock ?? '' }}"
                                                   class="form-control form-control-sm text-center" style="width:74px" onchange="this.form.submit()">
                                        </form>
                                        <form method="POST" action="{{ route('carrito.quitar', $producto) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('catalogo.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Seguir comprando</a>
                        <form method="POST" action="{{ route('carrito.vaciar') }}">
                            @csrf
                            <button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i> Vaciar carrito</button>
                        </form>
                    </div>
                </div>

                {{-- Resumen y checkout (RF-36, RF-37) --}}
                <div class="col-lg-4">
                    <div class="cs-card p-3">
                        <h5 class="fw-bold mb-3">Resumen del pedido</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Productos</span>
                            <span>{{ array_sum($cantidades) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary">Vendedor</span>
                            <span class="fw-medium">{{ $emprendimiento?->nombre }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">Total</span>
                            <span class="price-display">{{ cop($total) }}</span>
                        </div>

                        <form method="POST" action="{{ route('pedidos.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Método de pago</label>
                                <select name="metodo_pago" class="form-select @error('metodo_pago') is-invalid @enderror" required>
                                    <option value="">Selecciona…</option>
                                    <option value="efectivo" @selected(old('metodo_pago') === 'efectivo')>Efectivo (al recibir)</option>
                                    <option value="transferencia" @selected(old('metodo_pago') === 'transferencia')>Transferencia</option>
                                </select>
                                @error('metodo_pago')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nota para el vendedor (opcional)</label>
                                <textarea name="nota" rows="2" maxlength="300" class="form-control" placeholder="Punto de encuentro, horario…">{{ old('nota') }}</textarea>
                            </div>
                            <button class="btn btn-accent w-100"><i class="bi bi-check2-circle me-1"></i> Confirmar pedido</button>
                            <p class="small text-secondary text-center mt-2 mb-0">
                                El pago se acuerda directamente con el emprendimiento.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
