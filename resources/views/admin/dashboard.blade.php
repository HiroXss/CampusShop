@extends('layouts.dashboard')

@section('title', 'Panel de administración')

@section('content')
    @php
        $estados = ['pendiente' => 'Pendiente', 'confirmado' => 'Confirmado', 'entregado' => 'Entregado', 'rechazado' => 'Rechazado', 'cancelado' => 'Cancelado'];
        $labels = [];
        $valores = [];
        $colores = [];
        $paleta = ['#F59E0B', '#3B82F6', '#10B981', '#EF4444', '#94A3B8'];
        $i = 0;
        $totalPedidos = array_sum($pedidosPorEstado);
        foreach ($pedidosPorEstado as $estado => $total) {
            $labels[] = $estados[$estado] ?? ucfirst($estado);
            $valores[] = $total;
            $colores[] = $paleta[$i++ % count($paleta)];
        }
        $chart = ['type' => 'doughnut', 'data' => ['labels' => $labels, 'datasets' => [['data' => $valores, 'backgroundColor' => $colores, 'borderWidth' => 0]]], 'options' => ['plugins' => ['legend' => ['position' => 'bottom']], 'cutout' => '62%']];
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-cs.metric-card :valor="cop($ventas)" label="Ventas totales (entregadas)" icon="bi-cash-coin" color="verde" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-cs.metric-card :valor="$usuarios" label="Cuentas registradas" icon="bi-people" color="indigo" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-cs.metric-card :valor="$emprendimientos" label="Emprendimientos" icon="bi-shop" color="ambar" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-cs.metric-card :valor="$pedidos" label="Pedidos totales" icon="bi-receipt" color="rojo" />
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('admin.aprobacion') }}" class="text-decoration-none">
                <div class="cs-card cs-card-hover p-3 d-flex align-items-center gap-3">
                    <div class="metric-icon indigo"><i class="bi bi-patch-check"></i></div>
                    <div>
                        <div class="metric-value">{{ $emprendimientosPendientes }}</div>
                        <div class="metric-label">Emprendimientos por aprobar</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.reportes.index', ['estado' => 'pendiente']) }}" class="text-decoration-none">
                <div class="cs-card cs-card-hover p-3 d-flex align-items-center gap-3">
                    <div class="metric-icon ambar"><i class="bi bi-flag"></i></div>
                    <div>
                        <div class="metric-value">{{ $reportesPendientes }}</div>
                        <div class="metric-label">Reportes pendientes</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.emprendimientos.index') }}" class="text-decoration-none">
                <div class="cs-card cs-card-hover p-3 d-flex align-items-center gap-3">
                    <div class="metric-icon verde"><i class="bi bi-box-seam"></i></div>
                    <div>
                        <div class="metric-value">{{ $productos }}</div>
                        <div class="metric-label">Productos publicados</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="cs-card p-3 h-100">
                <h6 class="fw-bold mb-3">Pedidos por estado</h6>
                @if ($totalPedidos === 0)
                    <p class="text-secondary mb-0">Sin pedidos registrados.</p>
                @else
                    <canvas data-chart="{{ json_encode($chart) }}"></canvas>
                @endif
            </div>
        </div>
        <div class="col-lg-7">
            <div class="cs-card p-0 overflow-hidden h-100">
                <div class="p-3 pb-0"><h6 class="fw-bold mb-0">Productos por categoría</h6></div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr><th>Categoría</th><th class="text-end">Productos</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($categorias as $categoria)
                                <tr>
                                    <td>{{ $categoria->nombre }}</td>
                                    <td class="text-end">{{ $categoria->productos_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
