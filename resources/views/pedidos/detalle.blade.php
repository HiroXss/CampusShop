@php
    $esVendedor = $esVendedor ?? false;
@endphp

<div class="cs-card p-4 mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h1 class="h4 fw-bold mb-1">Pedido #{{ $pedido->id }}</h1>
            <div class="small text-secondary">Realizado el {{ $pedido->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <x-cs.status :estado="$pedido->estado" />
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <h6 class="fw-semibold mb-2">{{ $esVendedor ? 'Comprador' : 'Vendedor' }}</h6>
            @if ($esVendedor)
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ $pedido->comprador->avatar ? Storage::url($pedido->comprador->avatar) : asset('img/avatar-placeholder.svg') }}"
                         alt="" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                    <div>
                        <div class="fw-medium">{{ $pedido->comprador->name }}</div>
                        <div class="small text-secondary">{{ $pedido->comprador->program }}</div>
                    </div>
                </div>
                <a href="{{ route('conversaciones.iniciar', $pedido->emprendimiento) }}" class="btn btn-sm btn-outline-primary mt-2">
                    <i class="bi bi-chat-dots me-1"></i> Contactar
                </a>
            @else
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ $pedido->emprendimiento->logo ? Storage::url($pedido->emprendimiento->logo) : asset('img/logo-placeholder.svg') }}"
                         alt="" class="rounded-3" style="width:40px;height:40px;object-fit:cover;">
                    <div>
                        <div class="fw-medium">{{ $pedido->emprendimiento->nombre }}</div>
                        <a href="{{ route('emprendimientos.show', $pedido->emprendimiento) }}" class="small">Ver tienda</a>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-md-6">
            <h6 class="fw-semibold mb-2">Pago</h6>
            <div class="small">
                <div><span class="text-secondary">Método:</span> {{ ucfirst($pedido->metodo_pago) }}</div>
                @if ($pedido->nota)
                    <div class="mt-1"><span class="text-secondary">Nota:</span> {{ $pedido->nota }}</div>
                @endif
                @if ($pedido->motivo)
                    <div class="mt-1 text-danger"><span class="text-secondary">Motivo:</span> {{ $pedido->motivo }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="cs-card p-0 overflow-hidden mb-3">
    <table class="table mb-0 align-middle">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-end">Precio</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pedido->items as $item)
                <tr>
                    <td>
                        @if ($item->producto && ! $item->producto->trashed())
                            <a href="{{ route('catalogo.producto', $item->producto) }}" class="text-decoration-none">{{ $item->nombre_producto }}</a>
                        @else
                            {{ $item->nombre_producto }}
                        @endif
                    </td>
                    <td class="text-end">{{ cop($item->precio_unitario) }}</td>
                    <td class="text-center">{{ $item->cantidad }}</td>
                    <td class="text-end fw-medium">{{ cop($item->subtotal()) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end fw-semibold">Total</td>
                <td class="text-end fw-bold price-display">{{ cop($pedido->total) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
