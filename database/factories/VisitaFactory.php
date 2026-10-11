<?php

namespace Database\Factories;

use App\Models\Emprendimiento;
use App\Models\Visita;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visita>
 */
class VisitaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'emprendimiento_id' => Emprendimiento::factory(),
            'producto_id' => null,
            'user_id' => null,
            'sesion' => hash('sha256', Str::random(16)),
            'fecha' => now()->toDateString(),
        ];
    }
}