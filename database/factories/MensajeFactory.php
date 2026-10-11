<?php

namespace Database\Factories;

use App\Models\Conversacion;
use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mensaje>
 */
class MensajeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'conversacion_id' => Conversacion::factory(),
            'emisor_id' => User::factory(),
            'contenido' => fake()->sentence(10),
            'leido_at' => null,
        ];
    }
}