<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Resena;
use App\Services\MetricasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CatalogoController extends Controller
{
    public function __construct(private readonly MetricasService $metricas)
    {
    }

    /**
     * Catálogo con búsqueda y filtros (RF-23 a RF-26): texto, categoría, tipo,
     * rango de precio y orden, paginado a PAGE_SIZE.
     */
    public function index(): View
    {
        $orden = in_array(request('orden'), ['recientes', 'precio_asc', 'precio_desc', 'mejor_calificados', 'mas_vendidos'], true)
            ? request('orden')
            : 'recientes';

        $query = \App\Models\Producto::visibles()
            ->with('emprendimiento', 'imagenes');

        if ($q = trim((string) request('q'))) {
            $query->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")
                    ->orWhere('descripcion', 'like', "%{$q}%")
                    ->orWhereHas('emprendimiento', fn ($e) => $e->where('nombre', 'like', "%{$q}%"));
            });
        }

        if ($categoria = request('categoria')) {
            $query->whereHas('categoria', fn ($c) => $c->where('slug', $categoria)->orWhere('id', $categoria));
        }

        if (in_array(request('tipo'), ['producto', 'servicio'])) {
            $query->where('tipo', request('tipo'));
        }

        if (is_numeric(request('precio_min'))) {
            $query->where('precio', '>=', (float) request('precio_min'));
        }
        if (is_numeric(request('precio_max'))) {
            $query->where('precio', '<=', (float) request('precio_max'));
        }

        match ($orden) {
            'precio_asc' => $query->orderBy('precio'),
            'precio_desc' => $query->orderByDesc('precio'),
            'mejor_calificados' => $query->orderByDesc(
                \App\Models\Resena::selectRaw('avg(puntuacion)')
                    ->whereColumn('emprendimiento_id', 'productos.emprendimiento_id')
            )->orderByDesc('id'),
            'mas_vendidos' => $query->withCount(['pedidoItems as vendidos' => fn ($i) => $i->whereHas('pedido', fn ($p) => $p->where('estado', \App\Models\Pedido::ESTADO_ENTREGADO))])->orderByDesc('vendidos'),
            default => $query->latest(),
        };

        return view('catalogo.index', [
            'productos' => $query->paginate((int) config('campushop.page_size'))->withQueryString(),
            'categorias' => Categoria::orderBy('nombre')->get(),
            'filtros' => [
                'q' => request('q'),
                'categoria' => request('categoria'),
                'tipo' => request('tipo'),
                'precio_min' => request('precio_min'),
                'precio_max' => request('precio_max'),
                'orden' => $orden,
            ],
        ]);
    }

    /**
     * Detalle de producto (RF-25): galería, emprendimiento, reseñas y relacionados.
     */
    public function show(\App\Models\Producto $producto): View
    {
        // Solo productos visibles (RN-12) para el público.
        if (! $producto->isVisible()) {
            $esPropietario = auth()->check() && auth()->user()->id === $producto->emprendimiento->user_id;
            abort_unless($esPropietario, 404);
        }

        $this->metricas->registrarVisita(
            $producto->emprendimiento,
            $producto,
            auth()->user(),
            session()->getId()
        );

        $emprendimiento = $producto->emprendimiento->loadCount('resenas')->loadAvg('resenas', 'puntuacion');
        $emprendimiento->load('categoria', 'user');

        $resenas = $emprendimiento->resenas()
            ->with('comprador')
            ->latest()
            ->limit(6)
            ->get();

        $relacionados = \App\Models\Producto::visibles()
            ->where('id', '!=', $producto->id)
            ->where(function ($w) use ($producto) {
                $w->where('emprendimiento_id', $producto->emprendimiento_id)
                    ->orWhere('categoria_id', $producto->categoria_id);
            })
            ->with('emprendimiento', 'imagenes')
            ->limit(4)
            ->get();

        return view('catalogo.producto', [
            'producto' => $producto->load('imagenes', 'categoria'),
            'emprendimiento' => $emprendimiento,
            'resenas' => $resenas,
            'relacionados' => $relacionados,
        ]);
    }
}