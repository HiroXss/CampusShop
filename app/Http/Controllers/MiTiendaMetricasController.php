<?php

namespace App\Http\Controllers;

use App\Services\MetricasService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MiTiendaMetricasController extends Controller
{
    public function __construct(private readonly MetricasService $metricas)
    {
    }

    /**
     * Dashboard de métricas del emprendedor (RF-47 a RF-49).
     */
    public function index(Request $request): View
    {
        $emprendimiento = $request->user()->emprendimiento;

        $periodo = in_array($request->input('periodo'), ['7', '30', '90', 'total'])
            ? $request->input('periodo')
            : '30';

        $metricas = $this->metricas->panelDelEmprendedor($emprendimiento, $periodo);

        return view('mi-tienda.metricas', [
            'emprendimiento' => $emprendimiento,
            'metricas' => $metricas,
            'periodo' => $periodo,
        ]);
    }
}