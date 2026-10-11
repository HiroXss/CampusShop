<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Emprendimiento;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Reporte;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Estadísticas globales de la plataforma (RF-57).
     */
    public function index(): View
    {
        $ventas = (float) Pedido::where('estado', Pedido::ESTADO_ENTREGADO)->sum('total');

        return view('admin.dashboard', [
            'usuarios' => User::count(),
            'emprendimientos' => Emprendimiento::count(),
            'emprendimientosPendientes' => Emprendimiento::where('estado', Emprendimiento::ESTADO_PENDIENTE)->count(),
            'productos' => Producto::count(),
            'pedidos' => Pedido::count(),
            'pedidosPendientes' => Pedido::where('estado', Pedido::ESTADO_PENDIENTE)->count(),
            'ventas' => $ventas,
            'reportesPendientes' => Reporte::pendientes()->count(),
            'categorias' => Categoria::withCount('productos')->orderBy('nombre')->get(),
            'pedidosPorEstado' => Pedido::select('estado', DB::raw('count(*) as total'))
                ->groupBy('estado')->pluck('total', 'estado')->toArray(),
        ]);
    }
}