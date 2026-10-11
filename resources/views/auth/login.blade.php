<x-auth-layout>
    @section('title', 'Ingresar')

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <h4 class="fw-bold mb-1">¡Hola de nuevo!</h4>
    <p class="text-secondary mb-4" style="font-size:.875rem">Ingresa con tu correo universitario.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Correo universitario</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="form-control @error('email') is-invalid @enderror" placeholder="tucorreo@{{ config('campushop.university_domain') }}">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <label for="password" class="form-label mb-0">Contraseña</label>
                @if (Route::has('password.request'))
                    <a class="small" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="form-control mt-1 @error('password') is-invalid @enderror">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label small" for="remember">Recordarme</label>
        </div>

        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
    </form>

    <p class="text-center mb-0 mt-4 small text-secondary">
        ¿Aún no tienes cuenta? <a href="{{ route('register') }}">Regístrate</a>
    </p>
</x-auth-layout>