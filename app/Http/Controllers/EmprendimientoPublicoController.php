<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Emprendimiento;
use App\Models\Producto;
use App\Services\MetricasService;
use Illuminate\View\View;

class EmprendimientoPublicoController extends Controller
{
    public function __construct(private readonly MetricasService $metricas) {}

    /**
     * Listado público de emprendimientos con búsqueda y filtro por categoría (RF-14).
     */
    public function index(): View
    {
        $query = Emprendimiento::visibles()
            ->with('categoria', 'user')
            ->withCount('resenas')
            ->withAvg('resenas', 'puntuacion');

        if ($q = trim((string) request('q'))) {
            $query->where(fn ($w) => $w->where('nombre', 'like', "%{$q}%")
                ->orWhere('descripcion', 'like', "%{$q}%"));
        }

        if ($categoria = request('categoria')) {
            $query->whereHas('categoria', fn ($c) => $c->where('slug', $categoria)->orWhere('id', $categoria));
        }

        return view('emprendimientos.index', [
            'emprendimientos' => $query->orderByDesc('destacado')->latest()
                ->paginate((int) config('campushop.page_size'))->withQueryString(),
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }

    /**
     * Página pública del emprendimiento (RF-13).
     */
    public function show(Emprendimiento $emprendimiento): View
    {
        if (! $emprendimiento->isVisible()) {
            $esPropietario = auth()->check() && auth()->user()->id === $emprendimiento->user_id;
            abort_unless($esPropietario, 404);
        }

        $this->metricas->registrarVisita($emprendimiento, null, auth()->user(), session()->getId());

        $emprendimiento->loadCount('resenas')->loadAvg('resenas', 'puntuacion');
        $emprendimiento->load('categoria', 'user');

        $productos = $emprendimiento->productos()
            ->whereIn('estado', [Producto::ESTADO_DISPONIBLE, Producto::ESTADO_AGOTADO])
            ->where('oculto', false)
            ->with('imagenes')
            ->latest()
            ->paginate((int) config('campushop.page_size'));

        $resenas = $emprendimiento->resenas()->with('comprador')->latest()->limit(6)->get();

        return view('emprendimientos.show', [
            'emprendimiento' => $emprendimiento,
            'productos' => $productos,
            'resenas' => $resenas,
        ]);
    }
}
