<x-auth-layout>
    @section('title', 'Registro')

    <h4 class="fw-bold mb-1">Crea tu cuenta</h4>
    <p class="text-secondary mb-4" style="font-size:.875rem">
        Solo estudiantes universitarios pueden crear cuenta (RN-01).
    </p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nombre completo</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="form-control @error('name') is-invalid @enderror">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo universitario</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="tucorreo@{{ config('campushop.university_domain') }}">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="program" class="form-label">Programa académico</label>
            <input id="program" type="text" name="program" value="{{ old('program') }}" required maxlength="120"
                   class="form-control @error('program') is-invalid @enderror" placeholder="Ej: Tecnología en Desarrollo de Software">
            @error('program')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row">
            <div class="col-12 col-sm-6 mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-sm-6 mb-3">
                <label for="password_confirmation" class="form-label">Confirmar</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       class="form-control">
            </div>
        </div>

        <div class="form-text mb-3 small">
            Deberás verificar tu correo antes de ingresar (RF-02).
        </div>

        <button type="submit" class="btn btn-primary w-100">Crear cuenta</button>
    </form>

    <p class="text-center mb-0 mt-4 small text-secondary">
        ¿Ya tienes cuenta? <a href="{{ route('login') }}">Ingresa</a>
    </p>
</x-auth-layout>