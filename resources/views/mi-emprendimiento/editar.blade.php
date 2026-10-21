@extends('layouts.public')

@section('title', 'Mi emprendimiento')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 fw-bold mb-0">Mi emprendimiento</h1>
                    <x-cs.status :estado="$emprendimiento->estado" />
                </div>

                {{-- Avisos de estado (RF-10, RF-11, RF-12) --}}
                @if ($emprendimiento->estado === 'pendiente')
                    <div class="alert alert-warning"><i class="bi bi-hourglass-split me-1"></i> Tu emprendimiento está pendiente de aprobación. Puedes editar la información mientras esperas.</div>
                @elseif ($emprendimiento->estado === 'rechazado')
                    <div class="alert alert-danger">
                        <strong>Tu solicitud fue rechazada.</strong>
                        @if ($emprendimiento->motivo_rechazo)<div class="mt-1">Motivo: {{ $emprendimiento->motivo_rechazo }}</div>@endif
                        <div class="mt-1">Corrige la información y guarda para reenviarla a revisión.</div>
                    </div>
                @elseif ($emprendimiento->estado === 'pausado')
                    <div class="alert alert-warning">Tu tienda está pausada: no aparece en el catálogo ni recibe pedidos.</div>
                @elseif ($emprendimiento->estado === 'suspendido')
                    <div class="alert alert-danger">
                        <strong>Tu emprendimiento está suspendido por un administrador.</strong>
                        @if ($emprendimiento->motivo_rechazo)<div class="mt-1">Motivo: {{ $emprendimiento->motivo_rechazo }}</div>@endif
                    </div>
                @endif

                <form method="POST" action="{{ route('emprendimientos.update') }}" enctype="multipart/form-data" class="cs-card p-4">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" for="nombre">Nombre del emprendimiento</label>
                        <input id="nombre" name="nombre" value="{{ old('nombre', $emprendimiento->nombre) }}" required maxlength="100"
                               class="form-control @error('nombre') is-invalid @enderror" @disabled($emprendimiento->estado === 'suspendido')>
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="categoria_id">Categoría</label>
                        <select id="categoria_id" name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror">
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}" @selected(old('categoria_id', $emprendimiento->categoria_id) == $categoria->id)>{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                        @error('categoria_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3" maxlength="500" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $emprendimiento->descripcion) }}</textarea>
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="whatsapp">WhatsApp</label>
                            <input id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $emprendimiento->whatsapp) }}" maxlength="20" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="instagram">Instagram</label>
                            <input id="instagram" name="instagram" value="{{ old('instagram', $emprendimiento->instagram) }}" maxlength="60" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="logo">Logo</label>
                            <input id="logo" type="file" name="logo" accept="image/*" class="form-control" data-preview="#preview-logo">
                            <img id="preview-logo" src="{{ $emprendimiento->logo ? imagen($emprendimiento->logo) : '' }}" alt=""
                                 class="mt-2 rounded-3 {{ $emprendimiento->logo ? '' : 'd-none' }}" style="width:80px;height:80px;object-fit:cover;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="banner">Banner</label>
                            <input id="banner" type="file" name="banner" accept="image/*" class="form-control" data-preview="#preview-banner">
                            <img id="preview-banner" src="{{ $emprendimiento->banner ? imagen($emprendimiento->banner) : '' }}" alt=""
                                 class="mt-2 rounded-3 {{ $emprendimiento->banner ? '' : 'd-none' }}" style="width:80px;height:80px;object-fit:cover;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex gap-2">
                            @if ($emprendimiento->estado === 'aprobado')
                                <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modal-pausar">
                                    <i class="bi bi-pause-circle me-1"></i> Pausar tienda
                                </button>
                            @elseif ($emprendimiento->estado === 'pausado')
                                <button type="submit" form="form-reactivar" class="btn btn-outline-success">
                                    <i class="bi bi-play-circle me-1"></i> Reactivar tienda
                                </button>
                            @endif
                        </div>
                        @unless ($emprendimiento->estado === 'suspendido')
                            <button class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i>
                                {{ $emprendimiento->estado === 'rechazado' ? 'Guardar y reenviar' : 'Guardar cambios' }}
                            </button>
                        @endunless
                    </div>
                </form>

                @if ($emprendimiento->estado === 'pausado')
                    <form id="form-reactivar" method="POST" action="{{ route('emprendimientos.reactivar') }}">@csrf</form>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-pausar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('emprendimientos.pausar') }}">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Pausar tienda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">Tu tienda dejará de mostrarse en el catálogo y no recibirá pedidos hasta que la reactives. ¿Deseas continuar?</div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Sí, pausar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
