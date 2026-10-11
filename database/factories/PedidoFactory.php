<?php

namespace Database\Factories;

use App\Models\Emprendimiento;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pedido>
 */
class PedidoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'comprador_id' => User::factory(),
            'emprendimiento_id' => Emprendimiento::factory(),
            'estado' => Pedido::ESTADO_PENDIENTE,
            'metodo_pago' => fake()->randomElement([Pedido::PAGO_EFECTIVO, Pedido::PAGO_TRANSFERENCIA]),
            'nota' => fake()->optional()->sentence(),
            'total' => 0,
        ];
    }

    public function confirmado(): static
    {
        return $this->state(fn () => ['estado' => Pedido::ESTADO_CONFIRMADO, 'confirmado_at' => now()]);
    }

    public function entregado(): static
    {
        return $this->state(fn () => [
            'estado' => Pedido::ESTADO_ENTREGADO,
            'confirmado_at' => now()->subDays(2),
            'entregado_at' => now()->subDay(),
        ]);
    }

    public function cancelado(): static
    {
        return $this->state(fn () => ['estado' => Pedido::ESTADO_CANCELADO, 'motivo' => 'Cancelado']);
    }

    public function rechazado(): static
    {
        return $this->state(fn () => ['estado' => Pedido::ESTADO_RECHAZADO, 'motivo' => 'Sin stock']);
    }
}
