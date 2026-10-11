<x-auth-layout>
    @section('title', 'Confirmar contraseña')

    <h4 class="fw-bold mb-1">Confirma tu contraseña</h4>
    <p class="text-secondary mb-4" style="font-size:.875rem">
        Por seguridad, confirma tu contraseña para continuar.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="form-control @error('password') is-invalid @enderror">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary w-100">Confirmar</button>
    </form>
</x-auth-layout>