<?php

namespace Tests\Feature;

use App\Models\Emprendimiento;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 8 — Reseñas (RF-45, RF-46, RN-07).
 * Casos P22 a P24 del plan de pruebas (SRS 10.4).
 */
class ResenaTest extends TestCase
{
    use RefreshDatabase;

    /** P22 — No se puede reseñar un pedido que no está entregado (RN-07). */
    public function test_p22_no_se_resena_un_pedido_no_entregado(): void
    {
        $comprador = User::factory()->create();
        $emp = Emprendimiento::factory()->create(['estado' => Emprendimiento::ESTADO_APROBADO]);
        $pedido = Pedido::factory()->confirmado()->create([
            'comprador_id' => $comprador->id,
            'emprendimiento_id' => $emp->id,
        ]);

        $this->actingAs($comprador)
            ->post('/pedidos/'.$pedido->id.'/resena', ['puntuacion' => 5])
            ->assertForbidden();

        $this->assertDatabaseCount('resenas', 0);
    }

    /** P23 — No se puede reseñar dos veces el mismo pedido (RN-07). */
    public function test_p23_no_se_resena_dos_veces(): void
    {
        $comprador = User::factory()->create();
        $emp = Emprendimiento::factory()->create(['estado' => Emprendimiento::ESTADO_APROBADO]);
        $pedido = Pedido::factory()->entregado()->create([
            'comprador_id' => $comprador->id,
            'emprendimiento_id' => $emp->id,
        ]);

        $this->actingAs($comprador)
            ->post('/pedidos/'.$pedido->id.'/resena', ['puntuacion' => 5, 'comentario' => 'Excelente'])
            ->assertSessionHas('success');

        $this->assertDatabaseCount('resenas', 1);

        $this->actingAs($comprador)
            ->post('/pedidos/'.$pedido->id.'/resena', ['puntuacion' => 4])
            ->assertForbidden();

        $this->assertDatabaseCount('resenas', 1);
    }

    /** P24 — Un usuario no puede reseñar el pedido de otro (RN-07). */
    public function test_p24_no_se_resena_el_pedido_ajeno(): void
    {
        $comprador = User::factory()->create();
        $emp = Emprendimiento::factory()->create(['estado' => Emprendimiento::ESTADO_APROBADO]);
        $pedido = Pedido::factory()->entregado()->create([
            'comprador_id' => $comprador->id,
            'emprendimiento_id' => $emp->id,
        ]);

        $otro = User::factory()->create();

        $this->actingAs($otro)
            ->post('/pedidos/'.$pedido->id.'/resena', ['puntuacion' => 5])
            ->assertForbidden();

        $this->assertDatabaseCount('resenas', 0);
    }
}
