@extends('layouts.public')

@section('title', 'Mi perfil')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="h3 fw-bold mb-4"><i class="bi bi-person-circle me-2"></i>Mi perfil</h1>

                @if (session('status') === 'profile-updated' || session('success'))
                    <div class="alert alert-success">Perfil actualizado correctamente.</div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="cs-card p-4 mb-4">
                    @csrf
                    @method('PATCH')

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img id="preview-avatar"
                             src="{{ $user->avatar ? Storage::url($user->avatar) : asset('img/avatar-placeholder.svg') }}"
                             alt="" class="rounded-circle" style="width:88px;height:88px;object-fit:cover;">
                        <div>
                            <label class="form-label" for="avatar">Foto de perfil</label>
                            <input id="avatar" type="file" name="avatar" accept="image/*" class="form-control @error('avatar') is-invalid @enderror" data-preview="#preview-avatar">
                            @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">jpg, png o webp hasta 2 MB.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="name">Nombre completo</label>
                        <input id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="100" class="form-control @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="program">Programa académico</label>
                        <input id="program" name="program" value="{{ old('program', $user->program) }}" required maxlength="120" class="form-control @error('program') is-invalid @enderror">
                        @error('program')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">Correo institucional</label>
                        <input id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror">
                        <div class="form-text">El correo debe pertenecer al dominio de la universidad.</div>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar cambios</button>
                </form>

                <div class="cs-card p-4 border-danger-subtle">
                    <h5 class="fw-bold text-danger">Desactivar cuenta</h5>
                    <p class="text-secondary small">Al desactivar tu cuenta podrás seguir usando la plataforma, pero perderás acceso como vendedor si tienes emprendimiento. Esta acción requiere tu contraseña.</p>

                    @if ($errors->userDeletion->isNotEmpty())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->userDeletion->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modal-desactivar">Desactivar mi cuenta</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-desactivar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">¿Desactivar tu cuenta?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-secondary">Esta acción desactiva tu cuenta. Ingresa tu contraseña para confirmar.</p>
                    <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Desactivar cuenta</button>
                </div>
            </form>
        </div>
    </div>
@endsection
