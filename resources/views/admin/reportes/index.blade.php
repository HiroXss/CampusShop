@extends('layouts.dashboard')

@section('title', 'Reportes')

@section('content')
    <div class="d-flex flex-wrap gap-2 mb-3">
        @php $estados = ['' => 'Todos', 'pendiente' => 'Pendientes', 'descartado' => 'Descartados', 'aceptado' => 'Aceptados']; @endphp
        @foreach ($estados as $valor => $texto)
            <a href="{{ route('admin.reportes.index', $valor ? ['estado' => $valor] : []) }}"
               class="btn btn-sm {{ request('estado', '') === $valor ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $texto }}</a>
        @endforeach
    </div>

    <div class="cs-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Elemento</th>
                        <th>Motivo</th>
                        <th>Reportado por</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reportes as $reporte)
                        <tr>
                            <td>
                                <span class="badge text-bg-light mb-1">{{ $reporte->reportable_type === 'producto' ? 'Producto' : 'Emprendimiento' }}</span>
                                <div class="fw-medium">
                                    @if ($reporte->reportable)
                                        {{ $reporte->reportable->nombre }}
                                    @else
                                        <span class="text-secondary">(eliminado)</span>
                                    @endif
                                </div>
                            </td>
                            <td style="max-width:260px">{{ $reporte->motivo }}</td>
                            <td>
                                <div class="small">{{ $reporte->user->name }}</div>
                                <div class="small text-secondary">{{ $reporte->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td>
                                @if ($reporte->estado === 'pendiente')
                                    <span class="chip status-ambar">Pendiente</span>
                                @elseif ($reporte->estado === 'aceptado')
                                    <span class="chip status-rojo">Aceptado</span>
                                @else
                                    <span class="chip status-gris">Descartado</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if ($reporte->estado === 'pendiente')
                                    <div class="d-inline-flex gap-1">
                                        <form method="POST" action="{{ route('admin.reportes.resolver', $reporte) }}">
                                            @csrf
                                            <input type="hidden" name="decision" value="descartar">
                                            <button class="btn btn-sm btn-outline-secondary">Descartar</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.reportes.resolver', $reporte) }}">
                                            @csrf
                                            <input type="hidden" name="decision" value="aceptar">
                                            <button class="btn btn-sm btn-danger">Aceptar</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="small text-secondary">Resuelto</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-4">No hay reportes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $reportes->links() }}</div>
@endsection
