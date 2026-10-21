@extends('layouts.dashboard')

@section('title', 'Emprendimientos por aprobar')

@section('content')
    @if ($pendientes->isEmpty())
        <x-cs.empty-state icon="bi-patch-check" title="Todo al día" description="No hay emprendimientos pendientes de aprobación." />
    @else
        <div class="row g-3">
            @foreach ($pendientes as $emprendimiento)
                <div class="col-md-6">
                    <div class="cs-card p-3 h-100 d-flex flex-column">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img src="{{ $emprendimiento->logo ? imagen($emprendimiento->logo) : asset('img/logo-placeholder.svg') }}"
                                 alt="" class="rounded-3" style="width:56px;height:56px;object-fit:cover;background:#E7EEFF;">
                            <div>
                                <h6 class="fw-bold mb-0">{{ $emprendimiento->nombre }}</h6>
                                <span class="small text-secondary">{{ $emprendimiento->categoria->nombre }}</span>
                            </div>
                        </div>
                        <p class="small text-secondary">{{ \Illuminate\Support\Str::limit($emprendimiento->descripcion, 140) }}</p>
                        <div class="small text-secondary mb-3">
                            <i class="bi bi-person me-1"></i>{{ $emprendimiento->user->name }} · {{ $emprendimiento->user->email }}<br>
                            <i class="bi bi-calendar3 me-1"></i>Solicitado {{ $emprendimiento->created_at->diffForHumans() }}
                        </div>
                        <div class="d-flex gap-2 mt-auto">
                            <a href="{{ route('emprendimientos.show', $emprendimiento) }}" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="bi bi-eye"></i></a>
                            <form method="POST" action="{{ route('admin.aprobacion.aprobar', $emprendimiento) }}" class="flex-grow-1">
                                @csrf
                                <button class="btn btn-sm btn-success w-100"><i class="bi bi-check2 me-1"></i> Aprobar</button>
                            </form>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rechazar-{{ $emprendimiento->id }}"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="rechazar-{{ $emprendimiento->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form class="modal-content" method="POST" action="{{ route('admin.aprobacion.rechazar', $emprendimiento) }}">
                            @csrf
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold">Rechazar «{{ $emprendimiento->nombre }}»</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <label class="form-label">Motivo del rechazo</label>
                                <textarea name="motivo" rows="3" maxlength="300" required class="form-control" placeholder="Explica al estudiante qué debe corregir…"></textarea>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger">Rechazar</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
