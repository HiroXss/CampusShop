@extends('layouts.public')

@section('title', 'Inicio')

@section('content')
    {{-- Hero / buscador (RF-23) --}}
    <section class="cs-hero py-5">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="chip status-azul mb-3"><i class="bi bi-mortarboard"></i> Hecho por estudiantes</span>
                    <h1 class="display-5 fw-bold mb-3">
                        Compra y vende en <span style="color: var(--cs-primary)">CampusShop</span>
                    </h1>
                    <p class="lead text-secondary mb-4">
                        Los productos y servicios de los emprendimientos de tu universidad, en un solo lugar.
                    </p>
                    <form action="{{ route('catalogo.index') }}" method="GET" class="row g-2">
                        <div class="col-sm">
                            <input type="search" name="q" class="form-control form-control-lg" placeholder="¿Qué estás buscando?">
                        </div>
                        <div class="col-sm-auto">
                            <button class="btn btn-primary btn-lg w-100"><i class="bi bi-search me-1"></i> Buscar</button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <div class="cs-card p-4">
                        <i class="bi bi-shop-window" style="font-size:5rem;color:var(--cs-primary)"></i>
                        <p class="mb-0 mt-3 text-secondary">Descubre emprendimientos universitarios</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container pb-5">
        {{-- Categorías (RF-26) --}}
        <section class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="h4 fw-bold mb-0">Explora por categoría</h2>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($categorias as $categoria)
                    <a href="{{ route('catalogo.index', ['categoria' => $categoria->slug]) }}" class="category-pill">
                        <i class="bi bi-tag"></i> {{ $categoria->nombre }}
                        <span class="text-secondary small">({{ $categoria->productos_count }})</span>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Destacados (RF-27) --}}
        @if ($destacados->isNotEmpty())
            <section class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="h4 fw-bold mb-0"><i class="bi bi-star-fill text-warning me-1"></i> Emprendimientos destacados</h2>
                    <a href="{{ route('emprendimientos.index') }}" class="btn btn-sm btn-outline-secondary">Ver todos</a>
                </div>
                <div class="row g-3">
                    @foreach ($destacados as $emprendimiento)
                        <div class="col-md-6 col-lg-4 d-flex">
                            <x-cs.emprendimiento-card :emprendimiento="$emprendimiento" />
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Novedades (RF-27) --}}
        @if ($novedades->isNotEmpty())
            <section class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="h4 fw-bold mb-0">Recién publicados</h2>
                    <a href="{{ route('catalogo.index', ['orden' => 'recientes']) }}" class="btn btn-sm btn-outline-secondary">Ver más</a>
                </div>
                <div class="row g-3">
                    @foreach ($novedades as $producto)
                        <div class="col-6 col-lg-3 d-flex">
                            <x-cs.product-card :producto="$producto" />
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Más vendidos (RF-27, RN-08) --}}
        @if ($masVendidos->isNotEmpty())
            <section class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="h4 fw-bold mb-0"><i class="bi bi-fire text-danger me-1"></i> Más vendidos</h2>
                    <a href="{{ route('catalogo.index', ['orden' => 'mas_vendidos']) }}" class="btn btn-sm btn-outline-secondary">Ver más</a>
                </div>
                <div class="row g-3">
                    @foreach ($masVendidos as $producto)
                        <div class="col-6 col-lg-3 d-flex">
                            <x-cs.product-card :producto="$producto" />
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Mejor calificados (RF-27, RN-08) --}}
        @if ($mejorCalificados->isNotEmpty())
            <section class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="h4 fw-bold mb-0"><i class="bi bi-award me-1"></i> Mejor calificados</h2>
                    <a href="{{ route('emprendimientos.index') }}" class="btn btn-sm btn-outline-secondary">Ver más</a>
                </div>
                <div class="row g-3">
                    @foreach ($mejorCalificados as $emprendimiento)
                        <div class="col-md-6 col-lg-3 d-flex">
                            <x-cs.emprendimiento-card :emprendimiento="$emprendimiento" />
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- CTA vender (RF-09) --}}
        @guest
            <section class="cs-card p-4 p-md-5 text-center" style="background: linear-gradient(135deg,#EEF0FF,#F7F8FC);">
                <h2 class="h3 fw-bold mb-2">¿Tienes un emprendimiento?</h2>
                <p class="text-secondary mb-4">Activa tu tienda, publica tus productos y llega a toda la universidad.</p>
                <a href="{{ route('register') }}" class="btn btn-accent btn-lg">Vender en CampusShop</a>
            </div>
        @endauth
        </section>
    </div>
@endsection
