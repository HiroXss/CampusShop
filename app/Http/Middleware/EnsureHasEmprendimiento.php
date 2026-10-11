<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware "emprendedor": exige una sesión autenticada con correo verificado
 * y un emprendimiento aprobado o pausado (RNF-01, RN-02).
 */
class EnsureHasEmprendimiento
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if (! $user->emprendimientoGestionable()) {
            abort(403, 'Necesitas un emprendimiento aprobado o pausado para acceder a esta sección.');
        }

        return $next($request);
    }
}
