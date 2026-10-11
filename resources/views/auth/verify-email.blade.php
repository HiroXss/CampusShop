<x-auth-layout>
    @section('title', 'Verifica tu correo')

    <div class="text-center">
        <div class="empty-icon mb-3 mx-auto" style="width:64px;height:64px;border-radius:50%;background:#EEF0FF;color:var(--cs-primary);display:flex;align-items:center;justify-content:center;font-size:1.75rem;">
            <i class="bi bi-envelope-check"></i>
        </div>
        <h4 class="fw-bold mb-2">Verifica tu correo</h4>
        <p class="text-secondary mb-4" style="font-size:.875rem">
            Te enviamos un enlace de verificación a <strong>{{ auth()->user()->email }}</strong>.
            Revisa tu bandeja (RF-02, RF-03).
        </p>

        @if (session('status') === 'verification-link-sent')
            <div class="alert alert-success" role="alert">
                Se envió un nuevo enlace de verificación a tu correo.
            </div>
        @endif

        <div class="d-grid gap-2">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary w-100">Reenviar enlace</button>
            </form>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary w-100">Cerrar sesión</button>
            </form>
        </div>
    </div>
</x-auth-layout>