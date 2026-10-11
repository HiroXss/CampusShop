<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Categoria>
 */
class CategoriaFactory extends Factory
{
    public function definition(): array
    {
        $nombre = fake()->unique()->words(2, true);

        return [
            'nombre' => Str::title($nombre),
            'slug' => Str::slug($nombre),
            'descripcion' => fake()->sentence(8),
        ];
    }
}
