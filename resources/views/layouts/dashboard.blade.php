<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · CampusShop</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg cs-navbar">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('inicio') }}">
                <span class="brand-mark">CS</span>
                <span class="fw-bold d-none d-sm-inline" style="color: var(--cs-primary)">Campus</span><span class="fw-bold d-none d-sm-inline">Shop</span>
            </a>
            <div class="d-flex align-items-center gap-3 ms-auto">
                <a class="icon-link d-inline-flex" href="{{ route('catalogo.index') }}" title="Ir al catálogo"><i class="bi bi-grid fs-6"></i><span class="d-none d-md-inline ms-1 small">Catálogo</span></a>
                @if (auth()->user()->isAdmin() && isset($pendientes) && $pendientes > 0)
                    <a class="position-relative text-decoration-none" href="{{ route('admin.aprobacion') }}" title="Emprendimientos por revisar">
                        <i class="bi bi-inbox fs-5"></i>
                        <span class="badge-counter">{{ $pendientes }}</span>
                    </a>
                @endif
                <a class="icon-link d-inline-flex" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">@csrf</form>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-lg-4">
        <div class="row">
            <aside class="col-lg-3 col-xl-2 cs-sidebar py-3 d-none d-lg-block">
                <nav class="nav nav-pills flex-column gap-1">
                    <span class="small text-uppercase text-secondary fw-semibold px-3 mb-1">Mi negocio</span>
                    <a class="nav-link {{ request()->routeIs('mi-tienda.*') ? 'active' : '' }}" href="{{ route('mi-tienda.index') }}"><i class="bi bi-shop"></i>Mi tienda</a>
                    <a class="nav-link {{ request()->routeIs('mi-tienda.pedidos.*') ? 'active' : '' }}" href="{{ route('mi-tienda.pedidos.index') }}"><i class="bi bi-inbox"></i>Pedidos recibidos</a>
                    <a class="nav-link {{ request()->routeIs('mi-tienda.metricas') ? 'active' : '' }}" href="{{ route('mi-tienda.metricas') }}"><i class="bi bi-bar-chart"></i>Métricas</a>
                    <a class="nav-link" href="{{ route('emprendimientos.editar', auth()->user()->emprendimiento) }}"><i class="bi bi-gear"></i>Mi emprendimiento</a>

                    @if (auth()->user()->isAdmin())
                        <span class="small text-uppercase text-secondary fw-semibold px-3 mt-3 mb-1">Administración</span>
                        <a class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i>Resumen</a>
                        <a class="nav-link {{ request()->routeIs('admin.aprobacion', 'admin.emprendimientos.*') ? 'active' : '' }}" href="{{ route('admin.aprobacion') }}"><i class="bi bi-patch-check"></i>Aprobar emprendimientos</a>
                        <a class="nav-link {{ request()->routeIs('admin.categorias.*') ? 'active' : '' }}" href="{{ route('admin.categorias.index') }}"><i class="bi bi-tags"></i>Categorías</a>
                        <a class="nav-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}" href="{{ route('admin.reportes.index') }}"><i class="bi bi-flag"></i>Reportes</a>
                        <a class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}" href="{{ route('admin.usuarios.index') }}"><i class="bi bi-people"></i>Cuentas</a>
                    @endif
                </nav>
            </aside>

            <main class="col-lg-9 col-xl-10 py-4">
                @include('components.flash')
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h1 class="h4 mb-0 fw-bold">@yield('title')</h1>
                    <div class="text-end">@yield('actions')</div>
                </div>
                @yield('content')
            </main>
        </div>
    </div>

    <div data-toasts class="toast-container position-fixed bottom-0 end-0 p-3"></div>
    @stack('scripts')
</body>
</html>