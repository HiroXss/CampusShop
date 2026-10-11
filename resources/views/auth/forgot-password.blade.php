<x-auth-layout>
    @section('title', 'Recuperar contraseña')

    @if (session('status'))
        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
    @endif

    <h4 class="fw-bold mb-1">¿Olvidaste tu contraseña?</h4>
    <p class="text-secondary mb-4" style="font-size:.875rem">
        Te enviaremos un enlace para restablecerla (RF-04).
    </p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Correo universitario</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="form-control @error('email') is-invalid @enderror">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary w-100">Enviar enlace</button>
    </form>

    <p class="text-center mb-0 mt-4 small">
        <a href="{{ route('login') }}">Volver a ingresar</a>
    </p>
</x-auth-layout>