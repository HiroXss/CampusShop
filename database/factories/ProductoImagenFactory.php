<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\ProductoImagen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductoImagen>
 */
class ProductoImagenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'producto_id' => Producto::factory(),
            'ruta' => 'productos/placeholder.svg',
            'orden' => 1,
        ];
    }
}
