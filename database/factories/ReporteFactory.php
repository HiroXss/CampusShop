<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Reporte;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reporte>
 */
class ReporteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'reportable_type' => 'producto',
            'reportable_id' => Producto::factory(),
            'motivo' => fake()->sentence(8),
            'estado' => Reporte::ESTADO_PENDIENTE,
        ];
    }
}