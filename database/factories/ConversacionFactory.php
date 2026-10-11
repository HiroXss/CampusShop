<?php

namespace Database\Factories;

use App\Models\Conversacion;
use App\Models\Emprendimiento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversacion>
 */
class ConversacionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'emprendimiento_id' => Emprendimiento::factory(),
            'comprador_id' => User::factory(),
            'producto_id' => null,
        ];
    }
}
