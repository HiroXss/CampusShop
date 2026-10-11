@extends('layouts.public')

@section('title', 'Catálogo')

@section('content')
    <div class="container py-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="h3 fw-bold mb-1">Catálogo</h1>
                <p class="text-secondary mb-0">{{ $productos->total() }} resultados</p>
            </div>
        </div>

        <div class="row g-4">
            {{-- Filtros (RF-24 a RF-26) --}}
            <aside class="col-lg-3">
                <form method="GET" action="{{ route('catalogo.index') }}" class="cs-card p-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-1"></i> Filtros</h6>

                    <div class="mb-3">
                        <label class="form-label">Buscar</label>
                        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nombre, tienda…">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="categoria" class="form-select">
                            <option value="">Todas</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->slug }}" @selected(request('categoria') === $categoria->slug)>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="">Todos</option>
                            <option value="producto" @selected(request('tipo') === 'producto')>Productos</option>
                            <option value="servicio" @selected(request('tipo') === 'servicio')>Servicios</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rango de precio (COP)</label>
                        <div class="d-flex gap-2">
                            <input type="number" min="0" step="1000" name="precio_min" value="{{ request('precio_min') }}" class="form-control" placeholder="Mín">
                            <input type="number" min="0" step="1000" name="precio_max" value="{{ request('precio_max') }}" class="form-control" placeholder="Máx">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ordenar por</label>
                        <select name="orden" class="form-select">
                            <option value="recientes" @selected(request('orden', 'recientes') === 'recientes')>Más recientes</option>
                            <option value="precio_asc" @selected(request('orden') === 'precio_asc')>Precio: menor a mayor</option>
                            <option value="precio_desc" @selected(request('orden') === 'precio_desc')>Precio: mayor a menor</option>
                            <option value="mejor_calificados" @selected(request('orden') === 'mejor_calificados')>Mejor calificados</option>
                            <option value="mas_vendidos" @selected(request('orden') === 'mas_vendidos')>Más vendidos</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary">Aplicar filtros</button>
                        <a href="{{ route('catalogo.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                    </div>
                </form>
            </aside>

            <div class="col-lg-9">
                @if ($productos->isEmpty())
                    <x-cs.empty-state icon="bi-search" title="Sin resultados" description="Prueba con otros filtros o términos de búsqueda." />
                @else
                    <div class="row g-3">
                        @foreach ($productos as $producto)
                            <div class="col-6 col-md-4 d-flex">
                                <x-cs.product-card :producto="$producto" />
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $productos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
