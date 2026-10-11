@extends('layouts.dashboard')

@section('title', 'Cuentas')

@section('content')
    <form method="GET" class="cs-card p-3 mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Buscar</label>
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nombre o correo…">
            </div>
            <div class="col-md-4">
                <label class="form-label">Rol</label>
                <select name="role" class="form-select">
                    <option value="">Todos</option>
                    <option value="user" @selected(request('role') === 'user')>Estudiantes</option>
                    <option value="admin" @selected(request('role') === 'admin')>Administradores</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Filtrar</button>
            </div>
        </div>
    </form>

    <div class="cs-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Programa</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $usuario->avatar ? Storage::url($usuario->avatar) : asset('img/avatar-placeholder.svg') }}"
                                         alt="" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                                    <div>
                                        <div class="fw-medium">{{ $usuario->name }}</div>
                                        <div class="small text-secondary">{{ $usuario->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="small">{{ $usuario->program }}</td>
                            <td>
                                @if ($usuario->role === 'admin')
                                    <span class="chip status-azul">Administrador</span>
                                @else
                                    <span class="badge text-bg-light">Estudiante</span>
                                @endif
                            </td>
                            <td>
                                @if ($usuario->is_active)
                                    <span class="chip status-verde">Activo</span>
                                @else
                                    <span class="chip status-rojo">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <form method="POST" action="{{ route('admin.usuarios.toggle', $usuario) }}">
                                        @csrf
                                        <button class="btn btn-sm {{ $usuario->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                            {{ $usuario->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                    @if ($usuario->id !== auth()->id())
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                data-confirm
                                                data-action="{{ route('admin.usuarios.destroy', $usuario) }}"
                                                data-mensaje="¿Eliminar la cuenta de {{ $usuario->name }}?"><i class="bi bi-trash"></i></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-4">No se encontraron cuentas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $usuarios->links() }}</div>

    <x-modal-confirm />
@endsection
