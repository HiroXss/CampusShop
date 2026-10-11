<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Emprendimiento;
use App\Models\Reporte;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReporteController extends Controller
{
    /**
     * Listado de reportes para moderación (RF-51).
     */
    public function index(Request $request): View
    {
        $query = Reporte::with('user', 'reportable');

        if (in_array($request->input('estado'), ['pendiente', 'descartado', 'aceptado'])) {
            $query->where('estado', $request->input('estado'));
        }

        $reportes = $query->latest()->paginate(15)->withQueryString();

        return view('admin.reportes.index', compact('reportes'));
    }

    /**
     * Resolver un reporte: descartar, reactivar o eliminar/suspender (RF-51).
     *
     * @param  string  $decision  descartar|aceptar
     */
    public function resolver(Request $request, Reporte $reporte): RedirectResponse
    {
        $data = $request->validate([
            'decision' => ['required', 'in:descartar,aceptar'],
        ]);

        if ($data['decision'] === 'descartar') {
            $reporte->update([
                'estado' => Reporte::ESTADO_DESCARTADO,
                'revisado_por' => auth()->id(),
                'revisado_at' => now(),
            ]);

            return back()->with('success', 'Reporte descartado.');
        }

        // Aceptado: se actúa según el tipo de elemento reportado.
        $reportable = $reporte->reportable;

        if ($reporte->reportable_type === 'producto' && $reportable) {
            $reporte->reportable->delete(); // eliminación lógica del producto
        } elseif ($reporte instanceof Reporte && $reporte->reportable_type === 'emprendimiento' && $reporte->reportable) {
            $reporte->reportable->update([
                'estado' => Emprendimiento::ESTADO_SUSPENDIDO,
                'motivo_rechazo' => 'Suspendido tras revisión de reportes.',
            ]);
        }

        // Marca como aceptados los reportes pendientes del mismo elemento.
        Reporte::where('reportable_type', $reporte->reportable_type)
            ->where('reportable_id', $reporte->reportable_id)
            ->where('estado', Reporte::ESTADO_PENDIENTE)
            ->update([
                'estado' => Reporte::ESTADO_ACEPTADO,
                'revisado_por' => auth()->id(),
                'revisado_at' => now(),
            ]);

        return back()->with('success', 'Reporte aceptado y elemento moderado.');
    }
}
