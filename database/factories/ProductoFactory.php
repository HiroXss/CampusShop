<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Emprendimiento;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'emprendimiento_id' => Emprendimiento::factory(),
            'categoria_id' => Categoria::factory(),
            'nombre' => ucfirst(fake()->words(3, true)),
            'descripcion' => fake()->paragraph(3),
            'tipo' => fake()->randomElement([Producto::TIPO_PRODUCTO, Producto::TIPO_SERVICIO]),
            'precio' => fake()->numberBetween(3000, 120000),
            'stock' => fake()->numberBetween(1, 40),
            'estado' => Producto::ESTADO_DISPONIBLE,
            'oculto' => false,
        ];
    }

    public function servicio(): static
    {
        return $this->state(fn () => [
            'tipo' => Producto::TIPO_SERVICIO,
            'stock' => null,
        ]);
    }

    public function agotado(): static
    {
        return $this->state(fn () => [
            'estado' => Producto::ESTADO_AGOTADO,
            'stock' => 0,
        ]);
    }
}