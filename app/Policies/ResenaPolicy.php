<?php

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;

class ResenaPolicy
{
    /**
     * Solo el comprador de un pedido entregado puede reseñar, una vez por pedido (RN-07).
     */
    public function create(User $user, Pedido $pedido): bool
    {
        return $pedido->comprador_id === $user->id
            && $pedido->estado === Pedido::ESTADO_ENTREGADO
            && $pedido->resena()->doesntExist();
    }
}