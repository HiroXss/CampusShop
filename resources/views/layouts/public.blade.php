<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'CampusShop')) · CampusShop</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg cs-navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('inicio') }}">
                <span class="brand-mark">CS</span>
                <span class="fw-bold" style="color: var(--cs-primary)">Campus</span><span class="fw-bold">Shop</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navPrincipal" aria-controls="navPrincipal" aria-expanded="false" aria-label="Menú">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navPrincipal">
                <form class="d-lg-flex mx-lg-auto my-2 my-lg-0" action="{{ route('catalogo.index') }}" method="GET" role="search">
                    <div class="cs-search d-flex align-items-center gap-2">
                        <i class="bi bi-search text-secondary"></i>
                        <input name="q" value="{{ request('q') }}" placeholder="Busca productos y servicios…" aria-label="Buscar">
                    </div>
                </form>

                <ul class="navbar-nav align-items-lg-center gap-lg-2 mt-2 mt-lg-0">
                    <li class="nav-item"><a class="nav-link fw-medium" href="{{ route('catalogo.index') }}">Catálogo</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="{{ route('emprendimientos.index') }}">Emprendimientos</a></li>

                    @auth
                        <li class="nav-item">
                            <a class="icon-link d-inline-flex" href="{{ route('conversaciones.index') }}" title="Mensajes">
                                <i class="bi bi-chat-dots"></i>
                                <span id="mensajes-no-leidos" class="badge-counter d-none" data-count="0">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="icon-link d-inline-flex" href="{{ route('carrito.index') }}" title="Carrito">
                                <i class="bi bi-bag"></i>
                                @if (carritoCount() > 0)
                                    <span class="badge-counter">{{ carritoCount() }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item dropdown ms-lg-2">
                            <a class="nav-link dropdown-toggle p-0" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ auth()->user()->avatar ? imagen(auth()->user()->avatar) : asset('img/avatar-placeholder.svg') }}"
                                     alt="{{ auth()->user()->name }}" class="avatar">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><span class="dropdown-item-text fw-semibold">{{ auth()->user()->name }}</span></li>
                                <li><span class="dropdown-item-text small text-secondary">{{ auth()->user()->email }}</span></li>
                                @if (auth()->user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-check me-2"></i>Panel de administración</a></li>
                                @endif
                                @if (auth()->user()->emprendimiento)
                                    <li><a class="dropdown-item" href="{{ route('mi-tienda.index') }}"><i class="bi bi-shop me-2"></i>Mi tienda</a></li>
                                    <li><a class="dropdown-item" href="{{ route('mi-tienda.metricas') }}"><i class="bi bi-bar-chart me-2"></i>Métricas</a></li>
                                @else
                                    <li><a class="dropdown-item" href="{{ route('emprendimientos.crear-form') }}"><i class="bi bi-plus-circle me-2"></i>Vende en CampusShop</a></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('mis-pedidos.index') }}"><i class="bi bi-receipt me-2"></i>Mis pedidos</a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Mi perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item d-flex gap-2">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('login') }}">Ingresar</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Regístrate</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        @include('components.flash')
        @yield('content')
    </main>

    <footer class="cs-footer mt-4 pt-5 pb-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="brand-mark" style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,var(--cs-primary),#7B6CFF);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:700;">CS</span>
                        <span class="fw-bold text-dark">CampusShop</span>
                    </div>
                    <p class="small mb-0">La tienda virtual de los emprendimientos de estudiantes de la universidad.</p>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="fw-semibold text-dark">Tienda</h6>
                    <ul class="list-unstyled small mb-0">
                        <li><a href="{{ route('catalogo.index') }}">Catálogo</a></li>
                        <li><a href="{{ route('emprendimientos.index') }}">Emprendimientos</a></li>
                        <li><a href="{{ route('carrito.index') }}">Carrito</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="fw-semibold text-dark">Cuenta</h6>
                    <ul class="list-unstyled small mb-0">
                        @auth
                            <li><a href="{{ route('mis-pedidos.index') }}">Mis pedidos</a></li>
                            <li><a href="{{ route('profile.edit') }}">Mi perfil</a></li>
                        @else
                            <li><a href="{{ route('login') }}">Ingresar</a></li>
                            <li><a href="{{ route('register') }}">Registrarme</a></li>
                        @endauth
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="fw-semibold text-dark">¿Tienes emprendimiento?</h6>
                    <p class="small">Únete a la comunidad y vende tus productos o servicios a toda la universidad.</p>
                    <a href="{{ route('emprendimientos.crear-form') }}" class="btn btn-accent btn-sm">Vender en CampusShop</a>
                </div>
            </div>
            <hr class="my-4 border-secondary-subtle">
            <div class="text-center small">© {{ date('Y') }} CampusShop · UniMisiónPaz</div>
        </div>
    </footer>

    <div data-toasts class="toast-container position-fixed bottom-0 end-0 p-3"></div>
    @stack('scripts')
</body>
</html>