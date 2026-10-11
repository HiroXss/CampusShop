@extends('layouts.dashboard')

@section('title', 'Métricas de mi tienda')

@section('actions')
    <form method="GET" class="d-flex align-items-center gap-2">
        <label class="small text-secondary mb-0">Periodo</label>
        <select name="periodo" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="7" @selected($periodo === '7')>Últimos 7 días</option>
            <option value="30" @selected($periodo === '30')>Últimos 30 días</option>
            <option value="90" @selected($periodo === '90')>Últimos 90 días</option>
            <option value="total" @selected($periodo === 'total')>Histórico</option>
        </select>
    </form>
@endsection

@section('content')
    @php
        $totalPedidos = array_sum($metricas['pedidos_por_estado']);
        $estados = ['pendiente' => 'Pendiente', 'confirmado' => 'Confirmado', 'entregado' => 'Entregado', 'rechazado' => 'Rechazado', 'cancelado' => 'Cancelado'];
        $labels = [];
        $valores = [];
        $colores = [];
        $paleta = ['#F59E0B', '#3B82F6', '#10B981', '#EF4444', '#94A3B8'];
        $i = 0;
        foreach ($metricas['pedidos_por_estado'] as $estado => $total) {
            $labels[] = $estados[$estado] ?? ucfirst($estado);
            $valores[] = $total;
            $colores[] = $paleta[$i++ % count($paleta)];
        }
        $chart = [
            'type' => 'doughnut',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'data' => $valores,
                    'backgroundColor' => $colores,
                    'borderWidth' => 0,
                ]],
            ],
            'options' => [
                'plugins' => ['legend' => ['position' => 'bottom']],
                'cutout' => '62%',
            ],
        ];
    @endphp

    {{-- Tarjetas de métricas (RF-48) --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-cs.metric-card :valor="cop($metricas['ventas'])" label="Ventas (pedidos entregados)" icon="bi-cash-coin" color="verde" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-cs.metric-card :valor="$totalPedidos" label="Pedidos recibidos" icon="bi-receipt" color="indigo" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-cs.metric-card :valor="$metricas['visitas']" label="Visitas a tu tienda" icon="bi-eye" color="ambar" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-cs.metric-card :valor="$metricas['calificacion'] ? number_format($metricas['calificacion'], 1) : '—'" label="Calificación promedio ({{ $metricas['calificacion_total'] }})" icon="bi-star" color="rojo" />
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="cs-card p-3 h-100">
                <h6 class="fw-bold mb-3">Pedidos por estado</h6>
                @if ($totalPedidos === 0)
                    <p class="text-secondary mb-0">Sin pedidos en este periodo.</p>
                @else
                    <canvas data-chart="{{ json_encode($chart) }}"></canvas>
                @endif
            </div>
        </div>

        {{-- Productos más vendidos (RF-49, RN-09) --}}
        <div class="col-lg-8">
            <div class="cs-card p-0 overflow-hidden h-100">
                <div class="p-3 pb-0"><h6 class="fw-bold mb-0">Productos más vendidos</h6></div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th class="text-end">Precio</th>
                                <th class="text-center">Unidades</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($metricas['productos_mas_vendidos'] as $producto)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $producto->nombre }}</td>
                                    <td class="text-end">{{ cop($producto->precio) }}</td>
                                    <td class="text-center"><span class="badge text-bg-primary">{{ $producto->unidades }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-secondary py-4">Aún no tienes ventas entregadas en este periodo.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
