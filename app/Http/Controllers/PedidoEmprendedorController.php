<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Services\PedidoService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PedidoEmprendedorController extends Controller
{
    public function __construct(private readonly PedidoService $pedidos)
    {
    }

    /**
     * Pedidos recibidos por el emprendimiento (RF-41) con filtro por estado.
     */
    public function index(Request $request): View
    {
        $emprendimiento = $request->user()->emprendimiento;

        $query = $emprendimiento->pedidos()->with('comprador', 'items')->latest();

        if (in_array($request->input('estado'), ['pendiente', 'confirmado', 'entregado', 'rechazado', 'cancelado'])) {
            $query->where('estado', $request->input('estado'));
        }

        $pedidos = $query->paginate(10)->withQueryString();

        // Aviso simple de pedidos nuevos pendientes (no notificación flotante).
        $nuevos = $emprendimiento->pedidos()->where('estado', Pedido::ESTADO_PENDIENTE)->count();

        return view('mi-tienda.pedidos.index', compact('pedidos', 'nuevos'));
    }

    public function show(Request $request, Pedido $pedido): View
    {
        $this->authorize('ver', $pedido);
        $pedido->load('items.producto', 'comprador', 'emprendimiento');

        return view('mi-tienda.pedidos.show', compact('pedido'));
    }

    /**
     * Confirmar pedido: descuenta stock en transacción (RF-39, RN-06, RNF-16).
     */
    public function confirmar(Pedido $pedido): RedirectResponse
    {
        $this->authorize('confirmar', $pedido);

        try {
            $this->pedidos->confirmar($pedido);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Pedido #{$pedido->id} confirmado.");
    }

    /**
     * Rechazar pedido con motivo (RF-40).
     */
    public function rechazar(Request $request, Pedido $pedido): RedirectResponse
    {
        $this->authorize('rechazar', $pedido);

        $data = $request->validate([
            'motivo' => ['required', 'string', 'max:300'],
        ], [
            'motivo.required' => 'Debes indicar un motivo de rechazo.',
        ]);

        try {
            $this->pedidos->rechazar($pedido, $data['motivo']);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Pedido #{$pedido->id} rechazado.");
    }

    /**
     * Marcar como entregado (RF-43).
     */
    public function entregar(Pedido $pedido): RedirectResponse
    {
        $this->authorize('entregar', $pedido);

        try {
            $this->pedidos->entregar($pedido);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Pedido #{$pedido->id} marcado como entregado.");
    }

    /**
     * Cancelar un pedido confirmado y devolver el stock (RF-44, RN-06).
     */
    public function cancelar(Request $request, Pedido $pedido): RedirectResponse
    {
        $this->authorize('cancelarPorEmprendedor', $pedido);

        $data = $request->validate([
            'motivo' => ['required', 'string', 'max:300'],
        ], [
            'motivo.required' => 'Debes indicar un motivo de cancelación.',
        ]);

        try {
            $this->pedidos->cancelarPorEmprendedor($pedido, $data['motivo']);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Pedido #{$pedido->id} cancelado. El stock fue devuelto.");
    }
}