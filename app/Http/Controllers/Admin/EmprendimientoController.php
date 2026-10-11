<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Emprendimiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmprendimientoController extends Controller
{
    /**
     * Gestión de emprendimientos: destacar, suspender y reactivar (RF-53 a RF-56).
     */
    public function index(Request $request): View
    {
        $query = Emprendimiento::with('user', 'categoria', 'resenas');

        if (in_array($request->input('estado'), ['pendiente', 'aprobado', 'rechazado', 'pausado', 'suspendido'])) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->input('q')) {
            $query->where('nombre', 'like', '%'.$request->input('q').'%');
        }

        $emprendimientos = $query->latest()->paginate(15)->withQueryString();

        return view('admin.emprendimientos', compact('emprendimientos'));
    }

    /**
     * Destacar o quitar de destacados un emprendimiento (RF-54, RN-09).
     */
    public function destacar(Emprendimiento $emprendimiento): RedirectResponse
    {
        if ($emprendimiento->estado !== Emprendimiento::ESTADO_APROBADO) {
            return back()->with('error', 'Solo se pueden destacar emprendimientos aprobados.');
        }

        $emprendimiento->update(['destacado' => ! $emprendimiento->destacado]);

        return back()->with('success', $emprendimiento->destacado
            ? "«{$emprendimiento->nombre}» ahora es destacado."
            : "«{$emprendimiento->nombre}» ya no es destacado.");
    }

    /**
     * Suspender un emprendimiento con motivo (RF-55, RN-11).
     */
    public function suspender(Request $request, Emprendimiento $emprendimiento): RedirectResponse
    {
        $data = $request->validate([
            'motivo' => ['required', 'string', 'max:300'],
        ], [
            'motivo.required' => 'Indica el motivo de la suspensión.',
        ]);

        $emprendimiento->update([
            'estado' => Emprendimiento::ESTADO_SUSPENDIDO,
            'motivo_rechazo' => $data['motivo'],
            'destacado' => false,
            'revisado_por' => auth()->id(),
            'revisado_at' => now(),
        ]);

        return back()->with('success', "Emprendimiento «{$emprendimiento->nombre}» suspendido.");
    }

    /**
     * Reactivar un emprendimiento suspendido (RF-56).
     */
    public function reactivar(Emprendimiento $emprendimiento): RedirectResponse
    {
        $emprendimiento->update([
            'estado' => Emprendimiento::ESTADO_APROBADO,
            'motivo_rechazo' => null,
            'revisado_por' => auth()->id(),
            'revisado_at' => now(),
        ]);

        return back()->with('success', "Emprendimiento «{$emprendimiento->nombre}» reactivado.");
    }
}