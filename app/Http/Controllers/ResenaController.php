<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResenaRequest;
use App\Models\Pedido;
use App\Models\Resena;
use Illuminate\Http\RedirectResponse;

class ResenaController extends Controller
{
    /**
     * Crear una reseña para un pedido entregado (RF-45, RF-46, RN-07).
     * Solo una por pedido y solo tras la entrega.
     */
    public function store(StoreResenaRequest $request, Pedido $pedido): RedirectResponse
    {
        $this->authorize('create', [Resena::class, $pedido]);

        $data = $request->validated();

        Resena::create([
            'pedido_id' => $pedido->id,
            'comprador_id' => $pedido->comprador_id,
            'emprendimiento_id' => $pedido->emprendimiento_id,
            'puntuacion' => $data['puntuacion'],
            'comentario' => $data['comentario'] ?? null,
        ]);

        return back()->with('success', '¡Gracias por tu calificación!');
    }
}
