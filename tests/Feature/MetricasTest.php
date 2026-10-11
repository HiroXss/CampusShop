<?php

namespace Tests\Feature;

use App\Models\Emprendimiento;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Resena;
use App\Models\User;
use App\Services\MetricasService;
use App\Services\PedidoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 9 — Métricas y reconocimiento (RF-47 a RF-49, RN-08, RN-09).
 * Casos P25 a P27 del plan de pruebas (SRS 10.4).
 */
class MetricasTest extends TestCase
{
    use RefreshDatabase;

    private function emprendimiento(): Emprendimiento
    {
        $vendedor = User::factory()->create();

        return Emprendimiento::factory()->create([
            'user_id' => $vendedor->id,
            'estado' => Emprendimiento::ESTADO_APROBADO,
        ]);
    }

    /** P25 — Ventas y más vendidos solo cuentan pedidos entregados (RN-08). */
    public function test_p25_solo_cuentan_los_pedidos_entregados(): void
    {
        $emp = $this->emprendimiento();
        $servicio = app(PedidoService::class);

        $vendido = Producto::factory()->create([
            'emprendimiento_id' => $emp->id,
            'estado' => Producto::ESTADO_DISPONIBLE,
            'precio' => 10000,
            'stock' => 10,
        ]);
        $soloConfirmado = Producto::factory()->create([
            'emprendimiento_id' => $emp->id,
            'estado' => Producto::ESTADO_DISPONIBLE,
            'precio' => 50000,
            'stock' => 10,
        ]);

        $comprador = User::factory()->create();

        // Pedido entregado: sí cuenta.
        $entregado = $servicio->crear($comprador->id, $emp->id, [$vendido->id => 2], 'efectivo', null);
        $servicio->confirmar($entregado);
        $servicio->entregar($entregado);

        // Pedido solo confirmado: no cuenta como venta.
        $confirmado = $servicio->crear($comprador->id, $emp->id, [$soloConfirmado->id => 1], 'efectivo', null);
        $servicio->confirmar($confirmado);

        $panel = app(MetricasService::class)->panelDelEmprendedor($emp);

        $this->assertSame(20000.0, (float) $panel['ventas']);

        $idsVendidos = $panel['productos_mas_vendidos']->pluck('id')->all();
        $this->assertContains($vendido->id, $idsVendidos);
        $this->assertNotContains($soloConfirmado->id, $idsVendidos);
    }

    /** P26 — El dueño no genera visita en su propio emprendimiento (RN-08). */
    public function test_p26_el_dueno_no_genera_visita(): void
    {
        $emp = $this->emprendimiento();

        $this->actingAs($emp->user)
            ->get('/emprendimientos/'.$emp->slug)
            ->assertOk();
        $this->assertDatabaseCount('visitas', 0);

        $visitante = User::factory()->create();
        $this->actingAs($visitante)
            ->get('/emprendimientos/'.$emp->slug)
            ->assertOk();

        $this->assertDatabaseCount('visitas', 1);
        $this->assertDatabaseHas('visitas', [
            'emprendimiento_id' => $emp->id,
            'user_id' => $visitante->id,
        ]);
    }

    /** P27 — El ranking de mejor calificados exige al menos 3 reseñas (RN-09). */
    public function test_p27_ranking_requiere_minimo_de_resenas(): void
    {
        $empA = $this->emprendimiento();
        $empB = $this->emprendimiento();

        $this->resenas($empA, 3, 5);
        $this->resenas($empB, 2, 5);

        $ranking = app(MetricasService::class)->mejorCalificados();
        $ids = $ranking->pluck('id')->all();

        $this->assertContains($empA->id, $ids);
        $this->assertNotContains($empB->id, $ids);
    }

    private function resenas(Emprendimiento $emp, int $cantidad, int $puntuacion): void
    {
        for ($i = 0; $i < $cantidad; $i++) {
            $comprador = User::factory()->create();
            $pedido = Pedido::factory()->entregado()->create([
                'comprador_id' => $comprador->id,
                'emprendimiento_id' => $emp->id,
            ]);

            Resena::create([
                'pedido_id' => $pedido->id,
                'comprador_id' => $comprador->id,
                'emprendimiento_id' => $emp->id,
                'puntuacion' => $puntuacion,
            ]);
        }
    }
}
