<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Acceso') · CampusShop</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100" style="background: linear-gradient(180deg,#EEF0FF 0%, #F7F8FC 100%);">
    <div class="container my-auto py-5">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-9 col-md-7 col-lg-5 col-xl-4">
                <div class="text-center mb-4">
                    <a href="{{ route('inicio') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none">
                        <span class="brand-mark" style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,var(--cs-primary),#7B6CFF);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:700;">CS</span>
                        <span class="fs-4 fw-bold text-dark">Campus<span style="color: var(--cs-primary)">Shop</span></span>
                    </a>
                </div>
                <div class="cs-card p-4 p-md-5">
                    @yield('content')
                </div>
                <p class="text-center small text-secondary mt-4 mb-0">Todos los derechos reservados · CampuShop 2026</p>
            </div>
        </div>
    </div>
    <div data-toasts class="toast-container position-fixed bottom-0 end-0 p-3"></div>
</body>
</html>