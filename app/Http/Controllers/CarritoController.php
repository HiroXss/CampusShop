<?php

namespace App\Http\Controllers;

use App\Models\Emprendimiento;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarritoController extends Controller
{
    /**
     * Ver el carrito (RF-35). Los precios siempre se leen de la base de datos.
     */
    public function index(): View
    {
        $productos = carritoProductos();
        $cantidades = carrito();

        $emprendimiento = $productos->first()?->emprendimiento;
        $total = $productos->sum(fn ($p) => (float) $p->precio * ($cantidades[$p->id] ?? 0));

        return view('carrito.index', compact('productos', 'cantidades', 'emprendimiento', 'total'));
    }

    /**
     * Agregar un producto/servicio (RF-15 del flujo, RF-35). Un carrito admite
     * productos de un solo emprendimiento (RN-06).
     */
    public function agregar(Request $request, Producto $producto): RedirectResponse
    {
        if (! $producto->isPedible()) {
            return back()->with('error', 'Este producto no está disponible para pedir.');
        }

        $carrito = carrito();

        // RN-06: un pedido solo puede incluir productos de un mismo emprendimiento.
        if (! empty($carrito)) {
            $idsExistentes = array_keys($carrito);
            $emprendimientoId = Producto::whereIn('id', $idsExistentes)->value('emprendimiento_id');
            if ($emprendimientoId && (int) $emprendimientoId !== (int) $producto->emprendimiento_id) {
                return back()->with('error', 'Tu carrito tiene productos de otra tienda. Vacíalo para agregar este producto (RN-06).');
            }
        }

        $cantidad = max(1, (int) $request->input('cantidad', 1));
        $nueva = ($carrito[$producto->id] ?? 0) + $cantidad;

        if ($producto->stock !== null && $nueva > $producto->stock) {
            $nueva = $producto->stock;
            session()->flash('warning', "Solo hay {$producto->stock} unidades de «{$producto->nombre}».");
        }

        if ($nueva < 1) {
            return back()->with('error', 'El producto está agotado.');
        }

        // Si el dueño intenta comprar su propio producto (RN: no pedir a lo propio).
        if (auth()->check() && auth()->user()->id === $producto->emprendimiento->user_id) {
            return back()->with('error', 'No puedes pedir productos de tu propio emprendimiento.');
        }

        $carrito[$producto->id] = $nueva;
        session(['carrito' => $carrito]);

        return back()->with('success', '«'.$producto->nombre.'» se agregó al carrito.');
    }

    /**
     * Actualizar la cantidad de un ítem del carrito (RF-35).
     */
    public function actualizar(Request $request, Producto $producto): RedirectResponse
    {
        $request->validate(['cantidad' => ['required', 'integer', 'min:0']]);
        $cantidad = (int) $request->input('cantidad');

        $carrito = carrito();

        if ($cantidad <= 0) {
            unset($carrito[$producto->id]);
        } else {
            if ($producto->stock !== null && $cantidad > $producto->stock) {
                return back()->with('warning', "Solo hay {$producto->stock} unidades de «{$producto->nombre}».");
            }
            $carrito[$producto->id] = $cantidad;
        }

        session(['carrito' => $carrito]);

        return back();
    }

    /**
     * Quitar un ítem del carrito (RF-35).
     */
    public function quitar(Producto $producto): RedirectResponse
    {
        $carrito = carrito();
        unset($carrito[$producto->id]);
        session(['carrito' => $carrito]);

        return back()->with('success', 'Producto eliminado del carrito.');
    }

    /**
     * Vaciar el carrito por completo (necesario para cambiar de emprendimiento, RN-06).
     */
    public function vaciar(): RedirectResponse
    {
        session()->forget('carrito');

        return back()->with('success', 'Carrito vaciado.');
    }
}
