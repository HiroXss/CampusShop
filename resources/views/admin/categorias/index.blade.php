@extends('layouts.dashboard')

@section('title', 'Categorías')

@section('content')
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="cs-card p-3">
                <h6 class="fw-bold mb-3">Nueva categoría</h6>
                <form method="POST" action="{{ route('admin.categorias.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="nombre">Nombre</label>
                        <input id="nombre" name="nombre" value="{{ old('nombre') }}" required maxlength="80" class="form-control @error('nombre') is-invalid @enderror">
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="descripcion">Descripción (opcional)</label>
                        <textarea id="descripcion" name="descripcion" rows="2" maxlength="255" class="form-control">{{ old('descripcion') }}</textarea>
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i> Crear categoría</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="cs-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th class="text-center">Productos</th>
                                <th class="text-center">Tiendas</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categorias as $categoria)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $categoria->nombre }}</div>
                                        <div class="small text-secondary">{{ $categoria->descripcion }}</div>
                                    </td>
                                    <td class="text-center">{{ $categoria->productos_count }}</td>
                                    <td class="text-center">{{ $categoria->emprendimientos_count }}</td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editar-{{ $categoria->id }}"><i class="bi bi-pencil"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-confirm
                                                    data-action="{{ route('admin.categorias.destroy', $categoria) }}"
                                                    data-mensaje="¿Eliminar la categoría «{{ $categoria->nombre }}»?"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-secondary py-4">Aún no hay categorías.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @foreach ($categorias as $categoria)
        <div class="modal fade" id="editar-{{ $categoria->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('admin.categorias.update', $categoria) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Editar categoría</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input name="nombre" value="{{ $categoria->nombre }}" required maxlength="80" class="form-control">
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Descripción</label>
                            <input name="descripcion" value="{{ $categoria->descripcion }}" maxlength="255" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <x-modal-confirm title="Eliminar categoría" text="¿Confirmas la eliminación de esta categoría?" />
@endsection
