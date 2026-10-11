@php
    $mensajes = [
        'success' => session('success'),
        'error'   => session('error'),
        'warning' => session('warning'),
        'info'    => session('info'),
    ];
    $mapa = ['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'];
@endphp

@foreach ($mensajes as $tipo => $msj)
    @if ($msj)
        <div class="container mt-3">
            <div class="alert alert-{{ $mapa[$tipo] }} alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi {{ $tipo === 'success' ? 'bi-check-circle' : ($tipo === 'error' ? 'bi-x-circle' : ($tipo === 'warning' ? 'bi-exclamation-triangle' : 'bi-info-circle')) }}"></i>
                    <div>{{ $msj }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            </div>
        </div>
    @endif
@endforeach

@if ($errors->any())
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-exclamation-octagon"></i>
                <div>
                    <strong>Revisa los siguientes campos:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        </div>
    </div>
@endif