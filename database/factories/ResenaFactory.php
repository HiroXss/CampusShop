<?php

namespace Database\Factories;

use App\Models\Emprendimiento;
use App\Models\Pedido;
use App\Models\Resena;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resena>
 */
class ResenaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pedido_id' => Pedido::factory()->entregado(),
            'comprador_id' => fn (array $attrs) => Pedido::find($attrs['pedido_id'])->comprador_id,
            'emprendimiento_id' => Emprendimiento::factory(),
            'puntuacion' => fake()->numberBetween(3, 5),
            'comentario' => fake()->optional()->sentence(12),
        ];
    }
}