@extends('layouts.dashboard')

@section('title', 'Pedidos recibidos')

@section('content')
    @if ($nuevos > 0)
        <div class="alert alert-info d-flex align-items-center gap-2">
            <i class="bi bi-bell"></i>
            <div>Tienes <strong>{{ $nuevos }}</strong> pedido(s) pendiente(s) por revisar.</div>
        </div>
    @endif

    <div class="d-flex flex-wrap gap-2 mb-3">
        @php
            $estados = ['' => 'Todos', 'pendiente' => 'Pendientes', 'confirmado' => 'Confirmados', 'entregado' => 'Entregados', 'rechazado' => 'Rechazados', 'cancelado' => 'Cancelados'];
        @endphp
        @foreach ($estados as $valor => $texto)
            <a href="{{ route('mi-tienda.pedidos.index', $valor ? ['estado' => $valor] : []) }}"
               class="btn btn-sm {{ request('estado', '') === $valor ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $texto }}</a>
        @endforeach
    </div>

    <div class="cs-card p-0 overflow-hidden">
        @forelse ($pedidos as $pedido)
            <div class="p-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <a href="{{ route('mi-tienda.pedidos.show', $pedido) }}" class="fw-bold text-decoration-none" style="color:var(--cs-text)">Pedido #{{ $pedido->id }}</a>
                            <x-cs.status :estado="$pedido->estado" />
                        </div>
                        <div class="small text-secondary">
                            <i class="bi bi-person me-1"></i>{{ $pedido->comprador->name }}
                            · {{ $pedido->created_at->format('d/m/Y H:i') }}
                            · {{ $pedido->items->count() }} ítem(s)
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="price-display me-2">{{ cop($pedido->total) }}</span>
                        @if ($pedido->estado === 'pendiente')
                            <form method="POST" action="{{ route('mi-tienda.pedidos.confirmar', $pedido) }}">
                                @csrf
                                <button class="btn btn-sm btn-success"><i class="bi bi-check2 me-1"></i>Confirmar</button>
                            </form>
                        @elseif ($pedido->estado === 'confirmado')
                            <form method="POST" action="{{ route('mi-tienda.pedidos.entregar', $pedido) }}">
                                @csrf
                                <button class="btn btn-sm btn-success"><i class="bi bi-bag-check me-1"></i>Entregar</button>
                            </form>
                        @endif
                        <a href="{{ route('mi-tienda.pedidos.show', $pedido) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-3">
                <x-cs.empty-state icon="bi-inbox" title="Sin pedidos" description="Cuando recibas pedidos aparecerán aquí." />
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $pedidos->links() }}</div>
@endsection
