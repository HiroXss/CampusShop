@extends('layouts.dashboard')

@section('title', 'Publicar producto o servicio')

@section('actions')
    <a href="{{ route('mi-tienda.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Volver</a>
@endsection

@section('content')
    <form method="POST" action="{{ route('mi-tienda.productos.store') }}" enctype="multipart/form-data" class="cs-card p-4">
        @csrf

        @include('mi-tienda.productos._formulario', ['producto' => null])

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('mi-tienda.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button class="btn btn-primary px-4"><i class="bi bi-upload me-1"></i> Publicar</button>
        </div>
    </form>
@endsection
