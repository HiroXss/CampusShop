@extends('layouts.dashboard')

@section('title', 'Mi tienda')

@section('actions')
    <a href="{{ route('mi-tienda.productos.crear') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nuevo producto
    </a>
@endsection

@section('content')
    @if (! $emprendimiento->isVisible())
        <div class="alert alert-warning d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle"></i>
            <div>Tu tienda está <strong>{{ estadoLabel($emprendimiento->estado) }}</strong> y no es visible al público.
                @if ($emprendimiento->estado === 'pausado')
                    <a href="{{ route('emprendimientos.editar') }}">Reactívala aquí</a>.
                @else
                    <a href="{{ route('emprendimientos.editar') }}">Ver estado</a>.
                @endif
            </div>
        </div>
    @endif

    {{-- Resumen de la tienda --}}
    <div class="cs-card p-3 mb-4">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <img src="{{ $emprendimiento->logo ? Storage::url($emprendimiento->logo) : asset('img/logo-placeholder.svg') }}"
                 alt="" class="rounded-3" style="width:64px;height:64px;object-fit:cover;">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold mb-0">{{ $emprendimiento->nombre }}</h5>
                    <x-cs.status :estado="$emprendimiento->estado" />
                    @if ($emprendimiento->destacado)<span class="chip status-azul"><i class="bi bi-star-fill"></i>Destacado</span>@endif
                </div>
                <div class="small text-secondary">{{ $emprendimiento->categoria->nombre }} · {{ $productos->total() }} productos</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('emprendimientos.show', $emprendimiento) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye me-1"></i>Ver tienda</a>
                <a href="{{ route('emprendimientos.editar') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-gear me-1"></i>Editar</a>
            </div>
        </div>
    </div>

    {{-- Tabla de productos (RF-19 a RF-22) --}}
    <div class="cs-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th class="text-end">Precio</th>
                        <th class="text-center">Stock</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productos as $producto)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $producto->imagenes->first()?->ruta ? Storage::url($producto->imagenes->first()->ruta) : asset('img/placeholder-producto.svg') }}"
                                         alt="" class="rounded-2" style="width:44px;height:44px;object-fit:cover;background:#E7EEFF;">
                                    <div>
                                        <div class="fw-medium">{{ $producto->nombre }}</div>
                                        <div class="small text-secondary">{{ $producto->categoria->nombre }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge text-bg-light">{{ $producto->tipo === 'servicio' ? 'Servicio' : 'Producto' }}</span></td>
                            <td class="text-end">{{ cop($producto->precio) }}</td>
                            <td class="text-center">{{ $producto->stock ?? '—' }}</td>
                            <td><x-cs.status :estado="$producto->estado" /></td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <form method="POST" action="{{ route('mi-tienda.productos.estado', $producto) }}" class="d-flex gap-1">
                                        @csrf
                                        @method('PATCH')
                                        <select name="estado" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                                            <option value="disponible" @selected($producto->estado === 'disponible')>Disponible</option>
                                            <option value="agotado" @selected($producto->estado === 'agotado')>Agotado</option>
                                            <option value="inactivo" @selected($producto->estado === 'inactivo')>Inactivo</option>
                                        </select>
                                    </form>
                                    <a href="{{ route('mi-tienda.productos.editar', $producto) }}" class="btn btn-sm btn-outline-secondary" title="Editar"><i class="bi bi-pencil"></i></a>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            data-confirm
                                            data-action="{{ route('mi-tienda.productos.destroy', $producto) }}"
                                            data-mensaje="¿Eliminar «{{ $producto->nombre }}»? Esta acción no se puede deshacer."
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-cs.empty-state icon="bi-box-seam" title="Sin productos todavía" description="Publica tu primer producto o servicio para empezar a vender.">
                                    <a href="{{ route('mi-tienda.productos.crear') }}" class="btn btn-primary">Publicar producto</a>
                                </x-cs.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $productos->links() }}</div>

    <x-modal-confirm title="Eliminar producto" />
@endsection
