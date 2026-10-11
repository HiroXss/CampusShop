<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Emprendimiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AprobacionController extends Controller
{
    /**
     * Emprendimientos pendientes de aprobación (RF-10 de admin, RF-13).
     */
    public function index(): View
    {
        $pendientes = Emprendimiento::where('estado', Emprendimiento::ESTADO_PENDIENTE)
            ->with('user', 'categoria')
            ->oldest()
            ->get();

        return view('admin.aprobacion', compact('pendientes'));
    }

    /**
     * Aprobar un emprendimiento (RF-13).
     */
    public function aprobar(Emprendimiento $emprendimiento): RedirectResponse
    {
        $emprendimiento->update([
            'estado' => Emprendimiento::ESTADO_APROBADO,
            'motivo_rechazo' => null,
            'revisado_por' => auth()->id(),
            'revisado_at' => now(),
        ]);

        return back()->with('success', "Emprendimiento «{$emprendimiento->nombre}» aprobado.");
    }

    /**
     * Rechazar un emprendimiento con motivo obligatorio (RF-13, RF-11).
     */
    public function rechazar(Request $request, Emprendimiento $emprendimiento): RedirectResponse
    {
        $data = $request->validate([
            'motivo' => ['required', 'string', 'max:300'],
        ], [
            'motivo.required' => 'Indica el motivo del rechazo.',
        ]);

        $emprendimiento->update([
            'estado' => Emprendimiento::ESTADO_RECHAZADO,
            'motivo_rechazo' => $data['motivo'],
            'revisado_por' => auth()->id(),
            'revisado_at' => now(),
        ]);

        return back()->with('success', "Emprendimiento «{$emprendimiento->nombre}» rechazado.");
    }
}
