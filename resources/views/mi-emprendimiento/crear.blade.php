@extends('layouts.public')

@section('title', 'Activar mi tienda')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-4">
                    <span class="chip status-azul mb-3"><i class="bi bi-shop"></i> Vende en CampusShop</span>
                    <h1 class="h3 fw-bold">Activa tu emprendimiento</h1>
                    <p class="text-secondary">Completa la información de tu tienda. Un administrador la revisará antes de publicarla.</p>
                </div>

                <form method="POST" action="{{ route('emprendimientos.store') }}" enctype="multipart/form-data" class="cs-card p-4">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" for="nombre">Nombre del emprendimiento</label>
                        <input id="nombre" name="nombre" value="{{ old('nombre') }}" required maxlength="100"
                               class="form-control @error('nombre') is-invalid @enderror">
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="categoria_id">Categoría</label>
                        <select id="categoria_id" name="categoria_id" required class="form-select @error('categoria_id') is-invalid @enderror">
                            <option value="">Selecciona una categoría…</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}" @selected(old('categoria_id') == $categoria->id)>{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                        @error('categoria_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="4" required maxlength="500"
                                  class="form-control @error('descripcion') is-invalid @enderror"
                                  placeholder="¿Qué vendes y qué te hace especial?">{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="whatsapp">WhatsApp (opcional)</label>
                            <input id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}" maxlength="20"
                                   class="form-control @error('whatsapp') is-invalid @enderror" placeholder="3001234567">
                            @error('whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="instagram">Instagram (opcional)</label>
                            <input id="instagram" name="instagram" value="{{ old('instagram') }}" maxlength="60"
                                   class="form-control @error('instagram') is-invalid @enderror" placeholder="@mitienda">
                            @error('instagram')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="logo">Logo (obligatorio)</label>
                            <input id="logo" type="file" name="logo" accept="image/*" required
                                   class="form-control @error('logo') is-invalid @enderror" data-preview="#preview-logo">
                            @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <img id="preview-logo" alt="" class="mt-2 rounded-3 d-none" style="width:80px;height:80px;object-fit:cover;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="banner">Banner (opcional)</label>
                            <input id="banner" type="file" name="banner" accept="image/*"
                                   class="form-control @error('banner') is-invalid @enderror" data-preview="#preview-banner">
                            @error('banner')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <img id="preview-banner" alt="" class="mt-2 rounded-3 d-none" style="width:80px;height:80px;object-fit:cover;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <a href="{{ route('inicio') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button class="btn btn-primary px-4"><i class="bi bi-send me-1"></i> Enviar solicitud</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
