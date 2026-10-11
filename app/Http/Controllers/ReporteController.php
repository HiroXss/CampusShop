<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReporteRequest;
use App\Models\Emprendimiento;
use App\Models\Producto;
use App\Models\Reporte;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReporteController extends Controller
{
    /**
     * Reportar un producto o emprendimiento (RF-50, RF-51, RN-11).
     * Al alcanzar el umbral, el elemento se oculta automáticamente.
     */
    public function store(StoreReporteRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $reportable = $data['tipo'] === 'producto'
            ? Producto::find($data['id'])
            : Emprendimiento::find($data['id']);

        if (! $reportable) {
            return back()->with('error', 'El elemento que intentas reportar no existe.');
        }

        // No se puede reportar lo propio (validación RN-11).
        $duenoId = $data['tipo'] === 'producto'
            ? $reportable->emprendimiento->user_id
            : $reportable->user_id;

        if ($duenoId === Auth::id()) {
            return back()->with('error', 'No puedes reportar tu propio contenido.');
        }

        // Un reporte por usuario y elemento.
        $yaExiste = Reporte::where('user_id', Auth::id())
            ->where('reportable_type', $data['tipo'])
            ->where('reportable_id', $reportable->id)
            ->exists();

        if ($yaExiste) {
            return back()->with('error', 'Ya reportaste este elemento. Un administrador lo revisará.');
        }

        Reporte::create([
            'user_id' => Auth::id(),
            'reportable_type' => $data['tipo'],
            'reportable_id' => $reportable->id,
            'motivo' => $data['motivo'],
            'estado' => Reporte::ESTADO_PENDIENTE,
        ]);

        $this->ocultarSiSuperaUmbral($data['tipo'], $reportable);

        return back()->with('success', 'Gracias. Tu reporte quedó registrado para revisión.');
    }

    /**
     * Oculta el elemento cuando los reportes pendientes alcanzan el umbral (RN-11).
     */
    private function ocultarSiSuperaUmbral(string $tipo, $reportable): void
    {
        $pendientes = Reporte::where('reportable_type', $tipo)
            ->where('reportable_id', $reportable->id)
            ->where('estado', Reporte::ESTADO_PENDIENTE)
            ->count();

        if ($pendientes >= (int) config('campushop.reports_threshold')) {
            if ($tipo === 'producto') {
                $reportable->oculto = true;
            } else {
                $reportable->oculta = true;
            }
            $reportable->save();
        }
    }
}
