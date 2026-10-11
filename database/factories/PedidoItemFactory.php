<?php

namespace Database\Factories;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PedidoItem>
 */
class PedidoItemFactory extends Factory
{
    public function definition(): array
    {
        $producto = Producto::factory();

        return [
            'pedido_id' => Pedido::factory(),
            'producto_id' => $producto,
            'nombre_producto' => ucfirst(fake()->words(2, true)),
            'precio_unitario' => fake()->numberBetween(3000, 80000),
            'cantidad' => fake()->numberBetween(1, 5),
        ];
    }
}
