<?php

namespace App\Policies;

use App\Models\Emprendimiento;
use App\Models\User;

class EmprendimientoPolicy
{
    /**
     * Solo el dueño gestiona su emprendimiento (RN-04).
     */
    public function update(User $user, Emprendimiento $emprendimiento): bool
    {
        return $user->id === $emprendimiento->user_id;
    }

    /**
     * Pausar/reactivar solo el propio (RF-12).
     */
    public function pausar(User $user, Emprendimiento $emprendimiento): bool
    {
        return $user->id === $emprendimiento->user_id;
    }
}