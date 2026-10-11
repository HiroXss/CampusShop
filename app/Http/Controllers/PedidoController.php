<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePedidoRequest;
use App\Models\Pedido;
use App\Models\Producto;
use App\Services\PedidoService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PedidoController extends Controller
{
    public function __construct(private readonly PedidoService $pedidos) {}

    /**
     * Listado de pedidos del comprador (RF-42).
     */
    public function index(): View
    {
        $pedidos = Pedido::where('comprador_id', Auth::id())
            ->with('emprendimiento', 'items')
            ->latest()
            ->paginate(10);

        return view('pedidos.index', compact('pedidos'));
    }

    /**
     * Detalle de un pedido del comprador o del emprendedor (RF-43).
     */
    public function show(Pedido $pedido): View
    {
        $this->authorize('ver', $pedido);

        $pedido->load('items.producto', 'emprendimiento.user', 'comprador', 'resena');

        return view('pedidos.show', compact('pedido'));
    }

    /**
     * Crear el pedido desde el carrito en sesión (RF-36, RF-37, RF-38).
     */
    public function store(StorePedidoRequest $request): RedirectResponse
    {
        $carrito = carrito();

        if (empty($carrito)) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        $emprendimientoId = (int) Producto::whereIn('id', array_keys($carrito))->value('emprendimiento_id');

        try {
            $pedido = $this->pedidos->crear(
                Auth::id(),
                $emprendimientoId,
                $carrito,
                $request->string('metodo_pago')->toString(),
                $request->input('nota')
            );
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        session()->forget('carrito');

        return redirect()->route('pedidos.show', $pedido)
            ->with('success', '¡Pedido enviado! El emprendimiento lo revisará pronto.');
    }

    /**
     * Cancelar un pedido pendiente (RF-44, comprador).
     */
    public function cancelar(Pedido $pedido): RedirectResponse
    {
        $this->authorize('cancelar', $pedido);

        try {
            $this->pedidos->cancelarPorComprador($pedido);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pedido cancelado.');
    }
}
