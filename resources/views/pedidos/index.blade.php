@extends('layouts.public')

@section('title', 'Mis pedidos')

@section('content')
    <div class="container py-4">
        <h1 class="h3 fw-bold mb-4"><i class="bi bi-receipt me-2"></i>Mis pedidos</h1>

        @if ($pedidos->isEmpty())
            <x-cs.empty-state icon="bi-receipt" title="Aún no tienes pedidos" description="Cuando hagas un pedido aparecerá aquí.">
                <a href="{{ route('catalogo.index') }}" class="btn btn-primary">Explorar catálogo</a>
            </x-cs.empty-state>
        @else
            <div class="cs-card p-0 overflow-hidden">
                @foreach ($pedidos as $pedido)
                    <a href="{{ route('pedidos.show', $pedido) }}" class="text-decoration-none d-block p-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="fw-bold" style="color:var(--cs-text)">Pedido #{{ $pedido->id }}</span>
                                    <x-cs.status :estado="$pedido->estado" />
                                </div>
                                <div class="small text-secondary">
                                    <i class="bi bi-shop me-1"></i>{{ $pedido->emprendimiento->nombre }}
                                    · {{ $pedido->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="small text-secondary">{{ $pedido->items->count() }} ítem(s)</div>
                            </div>
                            <div class="text-end">
                                <div class="price-display">{{ cop($pedido->total) }}</div>
                                <span class="small text-secondary">{{ ucfirst($pedido->metodo_pago) }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-4">{{ $pedidos->links() }}</div>
        @endif
    </div>
@endsection
