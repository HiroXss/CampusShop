<?php

namespace App\Policies;

use App\Models\Conversacion;
use App\Models\User;

class ConversacionPolicy
{
    /**
     * Solo el comprador o el dueño del emprendimiento ven la conversación (RN-05).
     */
    public function ver(User $user, Conversacion $conversacion): bool
    {
        return $user->id === $conversacion->comprador_id
            || $user->id === $conversacion->emprendimiento->user_id;
    }

    public function escribir(User $user, Conversacion $conversacion): bool
    {
        return $this->ver($user, $conversacion);
    }
}