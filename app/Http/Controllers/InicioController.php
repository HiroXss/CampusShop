<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Services\MetricasService;
use Illuminate\View\View;

class InicioController extends Controller
{
    public function __construct(private readonly MetricasService $metricas)
    {
    }

    /**
     * Página de inicio (RF-27, RF-28): destacados, más vendidos, mejor
     * calificados, novedades y categorías.
     */
    public function index(): View
    {
        return view('inicio', [
            'destacados' => $this->metricas->destacados(),
            'masVendidos' => $this->metricas->masVendidos(),
            'mejorCalificados' => $this->metricas->mejorCalificados(),
            'novedades' => $this->metricas->novedades(),
            'categorias' => Categoria::withCount('productos')->orderBy('nombre')->get(),
        ]);
    }
}