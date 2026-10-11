<?php

namespace App\Services;

use App\Models\Emprendimiento;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use App\Models\Visita;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * MetricasService: visitas, pedidos por estado, ventas y rankings
 * (RFD-08, RN-08, RN-09). Todo se calcula al consultar para evitar desincronización.
 */
class MetricasService
{
    /**
     * Registra una visita si corresponde (RN-08): máximo una por sesión,
     * página y día; el dueño no genera visita.
     */
    public function registrarVisita(Emprendimiento $emprendimiento, ?Producto $producto, ?User $usuario, ?string $sesion): void
    {
        if ($usuario && $usuario->id === $emprendimiento->user_id) {
            return; // el dueño no se cuenta a sí mismo
        }

        $sesionId = hash('sha256', (string) ($sesion ?: 'anon'));

        $existe = Visita::where('emprendimiento_id', $emprendimiento->id)
            ->where('producto_id', $producto?->id)
            ->where('sesion', $sesionId)
            ->whereDate('fecha', today())
            ->exists();

        if ($existe) {
            return;
        }

        Visita::create([
            'emprendimiento_id' => $emprendimiento->id,
            'producto_id' => $producto?->id,
            'user_id' => $usuario?->id,
            'sesion' => $sesionId,
            'fecha' => today(),
        ]);
    }

    /**
     * Panel de métricas del emprendedor (RF-48, RF-49).
     *
     * @return array{visitas:int, pedidos_por_estado:array, ventas:float, calificacion:?float, productos_mas_vendidos:Collection}
     */
    public function panelDelEmprendedor(Emprendimiento $emprendimiento, string $periodo = 'total'): array
    {
        $desde = match ($periodo) {
            '7' => now()->subDays(7),
            '30' => now()->subDays(30),
            '90' => now()->subDays(90),
            default => null,
        };

        // Visitas según fecha de creación (RN-08).
        $visitas = Visita::where('emprendimiento_id', $emprendimiento->id)
            ->when($desde, fn ($q) => $q->whereDate('fecha', '>=', $desde->toDateString()))
            ->count();

        // Pedidos por estado según created_at (RN-08).
        $pedidosPorEstado = Pedido::where('emprendimiento_id', $emprendimiento->id)
            ->when($desde, fn ($q) => $q->where('created_at', '>=', $desde))
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        // Ventas (pedidos entregados) según entregado_at (RN-08).
        $ventas = (float) Pedido::where('emprendimiento_id', $emprendimiento->id)
            ->where('estado', Pedido::ESTADO_ENTREGADO)
            ->when($desde, fn ($q) => $q->where('entregado_at', '>=', $desde))
            ->join('pedido_items', 'pedidos.id', '=', 'pedido_items.pedido_id')
            ->select(DB::raw('COALESCE(SUM(pedido_items.precio_unitario * pedido_items.cantidad), 0) as total'))
            ->value('total');

        // Calificación promedio con su cantidad.
        $calificacion = $emprendimiento->resenas()
            ->when($desde, fn ($q) => $q->where('created_at', '>=', $desde))
            ->selectRaw('ROUND(AVG(puntuacion), 1) as promedio, COUNT(*) as total')
            ->first();

        // Productos más vendidos (unidades en pedidos entregados).
        $productos = Producto::where('productos.emprendimiento_id', $emprendimiento->id)
            ->withTrashed()
            ->join('pedido_items', 'productos.id', '=', 'pedido_items.producto_id')
            ->join('pedidos', 'pedidos.id', '=', 'pedido_items.pedido_id')
            ->where('pedidos.estado', Pedido::ESTADO_ENTREGADO)
            ->when($desde, fn ($q) => $q->where('pedidos.entregado_at', '>=', $desde))
            ->select('productos.id', 'productos.nombre', 'productos.precio', DB::raw('SUM(pedido_items.cantidad) as unidades'))
            ->groupBy('productos.id', 'productos.nombre', 'productos.precio')
            ->orderByDesc('unidades')
            ->limit(10)
            ->get();

        return [
            'visitas' => $visitas,
            'pedidos_por_estado' => $pedidosPorEstado,
            'ventas' => $ventas,
            'calificacion' => $calificacion ? (float) $calificacion->promedio : null,
            'calificacion_total' => $calificacion ? (int) $calificacion->total : 0,
            'productos_mas_vendidos' => $productos,
        ];
    }

    /* Rankings del inicio (RN-09) */

    public function destacados(): Collection
    {
        return Emprendimiento::visibles()
            ->where('destacado', true)
            ->with('user')
            ->withCount('resenas')
            ->withAvg('resenas', 'puntuacion')
            ->orderByDesc('destacado')
            ->limit((int) config('campushop.featured_limit'))
            ->get();
    }

    public function masVendidos(): Collection
    {
        $dias = (int) config('campushop.top_selling_days');

        return Producto::visibles()
            ->with('emprendimiento')
            ->with('imagenes')
            ->join('pedido_items', 'productos.id', '=', 'pedido_items.producto_id')
            ->join('pedidos', 'pedidos.id', '=', 'pedido_items.pedido_id')
            ->where('pedidos.estado', Pedido::ESTADO_ENTREGADO)
            ->where('pedidos.entregado_at', '>=', now()->subDays($dias))
            ->select('productos.*', DB::raw('SUM(pedido_items.cantidad) as unidades_vendidas'))
            ->groupBy('productos.id')
            ->orderByDesc('unidades_vendidas')
            ->limit(8)
            ->get();
    }

    public function mejorCalificados(): Collection
    {
        $min = (int) config('campushop.min_reviews_for_ranking');

        return Emprendimiento::visibles()
            ->with('user', 'categoria')
            ->withCount('resenas')
            ->withAvg('resenas', 'puntuacion')
            ->having('resenas_count', '>=', $min)
            ->orderByDesc('resenas_avg_puntuacion')
            ->orderByDesc('resenas_count')
            ->limit(8)
            ->get();
    }

    public function novedades(): Collection
    {
        return Producto::visibles()
            ->with('emprendimiento', 'imagenes')
            ->latest()
            ->limit(8)
            ->get();
    }
}
