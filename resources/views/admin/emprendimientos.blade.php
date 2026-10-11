@extends('layouts.dashboard')

@section('title', 'Gestión de emprendimientos')

@section('content')
    <form method="GET" class="cs-card p-3 mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-sm">
                <label class="form-label">Buscar por nombre</label>
                <input type="search" name="q" value="{{ request('q') }}" class="form-control">
            </div>
            <div class="col-sm-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    @foreach (['pendiente', 'aprobado', 'rechazado', 'pausado', 'suspendido'] as $estado)
                        <option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ estadoLabel($estado) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-auto">
                <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Filtrar</button>
            </div>
        </div>
    </form>

    <div class="cs-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Emprendimiento</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th class="text-center">Destacado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($emprendimientos as $emprendimiento)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $emprendimiento->logo ? Storage::url($emprendimiento->logo) : asset('img/logo-placeholder.svg') }}"
                                         alt="" class="rounded-2" style="width:40px;height:40px;object-fit:cover;background:#E7EEFF;">
                                    <div>
                                        <a href="{{ route('emprendimientos.show', $emprendimiento) }}" class="fw-medium text-decoration-none" style="color:var(--cs-text)">{{ $emprendimiento->nombre }}</a>
                                        <div class="small text-secondary">{{ $emprendimiento->user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $emprendimiento->categoria->nombre }}</td>
                            <td><x-cs.status :estado="$emprendimiento->estado" /></td>
                            <td class="text-center">
                                @if ($emprendimiento->destacado)
                                    <i class="bi bi-star-fill text-warning"></i>
                                @else
                                    <i class="bi bi-star text-secondary"></i>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    @if ($emprendimiento->estado === 'aprobado')
                                        <form method="POST" action="{{ route('admin.emprendimientos.destacar', $emprendimiento) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-secondary" title="{{ $emprendimiento->destacado ? 'Quitar destacado' : 'Destacar' }}">
                                                <i class="bi {{ $emprendimiento->destacado ? 'bi-star' : 'bi-star-fill' }}"></i>
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#suspender-{{ $emprendimiento->id }}" title="Suspender"><i class="bi bi-slash-circle"></i></button>
                                    @elseif ($emprendimiento->estado === 'suspendido')
                                        <form method="POST" action="{{ route('admin.emprendimientos.reactivar', $emprendimiento) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success"><i class="bi bi-arrow-counterclockwise me-1"></i>Reactivar</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('emprendimientos.show', $emprendimiento) }}" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="bi bi-box-arrow-up-right"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-4">No se encontraron emprendimientos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $emprendimientos->links() }}</div>

    @foreach ($emprendimientos as $emprendimiento)
        <div class="modal fade" id="suspender-{{ $emprendimiento->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('admin.emprendimientos.suspender', $emprendimiento) }}">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Suspender «{{ $emprendimiento->nombre }}»</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Motivo de la suspensión</label>
                        <textarea name="motivo" rows="3" maxlength="300" required class="form-control"></textarea>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Suspender</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
