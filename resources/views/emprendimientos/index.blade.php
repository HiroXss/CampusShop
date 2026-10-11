@extends('layouts.public')

@section('title', 'Emprendimientos')

@section('content')
    <div class="container py-4">
        <div class="mb-4">
            <h1 class="h3 fw-bold mb-1">Emprendimientos</h1>
            <p class="text-secondary mb-0">Conoce los negocios de estudiantes de la universidad.</p>
        </div>

        <form method="GET" action="{{ route('emprendimientos.index') }}" class="cs-card p-3 mb-4">
            <div class="row g-2 align-items-end">
                <div class="col-md">
                    <label class="form-label">Buscar</label>
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nombre del emprendimiento…">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Categoría</label>
                    <select name="categoria" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->slug }}" @selected(request('categoria') === $categoria->slug)>{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto d-grid">
                    <button class="btn btn-primary"><i class="bi bi-search me-1"></i> Buscar</button>
                </div>
            </form>

        @if ($emprendimientos->isEmpty())
            <x-cs.empty-state icon="bi-shop" title="Sin emprendimientos" description="No encontramos emprendimientos con esos criterios." />
        @else
            <div class="row g-3">
                @foreach ($emprendimientos as $emprendimiento)
                    <div class="col-md-6 col-lg-4 d-flex">
                        <x-cs.emprendimiento-card :emprendimiento="$emprendimiento" />
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $emprendimientos->links() }}
            </div>
        @endif
    </div>
@endsection
