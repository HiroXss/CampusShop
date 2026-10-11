<?php

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;

class PedidoPolicy
{
    /**
     * El pedido solo lo ve su comprador o el dueño del emprendimiento (RN-04).
     */
    public function ver(User $user, Pedido $pedido): bool
    {
        return $user->id === $pedido->comprador_id
            || $user->id === $pedido->emprendimiento->user_id;
    }

    public function cancelar(User $user, Pedido $pedido): bool
    {
        // Solo el comprador cancela sus pedidos pendientes (RF-39).
        return $user->id === $pedido->comprador_id
            && $pedido->estado === Pedido::ESTADO_PENDIENTE;
    }

    public function confirmar(User $user, Pedido $pedido): bool
    {
        return $user->id === $pedido->emprendimiento->user_id;
    }

    public function rechazar(User $user, Pedido $pedido): bool
    {
        return $user->id === $pedido->emprendimiento->user_id;
    }

    public function entregar(User $user, Pedido $pedido): bool
    {
        return $user->id === $pedido->emprendimiento->user_id;
    }

    public function cancelarPorEmprendedor(User $user, Pedido $pedido): bool
    {
        return $user->id === $pedido->emprendimiento->user_id;
    }
}