<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Emprendimiento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Emprendimiento>
 */
class EmprendimientoFactory extends Factory
{
    public function definition(): array
    {
        $nombre = fake()->unique()->company();

        return [
            'user_id' => User::factory(),
            'categoria_id' => Categoria::factory(),
            'nombre' => $nombre,
            'slug' => Str::slug($nombre).'-'.Str::random(4),
            'descripcion' => fake()->paragraph(3),
            'logo' => 'emprendimientos/placeholder.svg',
            'banner' => null,
            'whatsapp' => fake()->numerify('3#########'),
            'instagram' => '@'.Str::slug($nombre, ''),
            'estado' => Emprendimiento::ESTADO_APROBADO,
            'destacado' => false,
            'oculta' => false,
        ];
    }

    public function pendiente(): static
    {
        return $this->state(fn () => ['estado' => Emprendimiento::ESTADO_PENDIENTE]);
    }

    public function rechazado(): static
    {
        return $this->state(fn () => [
            'estado' => Emprendimiento::ESTADO_RECHAZADO,
            'motivo_rechazo' => 'Información incompleta del emprendimiento.',
        ]);
    }

    public function pausado(): static
    {
        return $this->state(fn () => ['estado' => Emprendimiento::ESTADO_PAUSADO]);
    }

    public function destacado(): static
    {
        return $this->state(fn () => ['destacado' => true]);
    }
}