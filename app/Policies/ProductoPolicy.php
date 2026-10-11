<?php

namespace App\Policies;

use App\Models\Producto;
use App\Models\User;

class ProductoPolicy
{
    /**
     * Solo el dueño del emprendimiento gestiona sus productos (RN-04).
     */
    private function esDueno(User $user, Producto $producto): bool
    {
        return $user->id === $producto->emprendimiento->user_id;
    }

    public function update(User $user, Producto $producto): bool
    {
        return $this->esDueno($user, $producto);
    }

    public function delete(User $user, Producto $producto): bool
    {
        return $this->esDueno($user, $producto);
    }

    public function cambiarEstado(User $user, Producto $producto): bool
    {
        return $this->esDueno($user, $producto);
    }
}