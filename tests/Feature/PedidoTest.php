<?php

namespace Tests\Feature;

use App\Models\Emprendimiento;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use App\Services\PedidoService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 6 — Pedidos (RF-35 a RF-44, RN-06, RNF-16).
 * Casos P14 a P21 del plan de pruebas (SRS 10.4).
 */
class PedidoTest extends TestCase
{
    use RefreshDatabase;

    private function escenario(int $stock): array
    {
        $vendedor = User::factory()->create();
        $emp = Emprendimiento::factory()->create([
            'user_id' => $vendedor->id,
            'estado' => Emprendimiento::ESTADO_APROBADO,
        ]);
        $producto = Producto::factory()->create([
            'emprendimiento_id' => $emp->id,
            'estado' => Producto::ESTADO_DISPONIBLE,
            'precio' => 10000,
            'stock' => $stock,
        ]);

        return [$vendedor, $emp, $producto];
    }

    /** P14 — Pedir un producto del propio emprendimiento se impide (RN-06). */
    public function test_p14_no_se_puede_pedir_al_propio_emprendimiento(): void
    {
        [$vendedor, $emp, $producto] = $this->escenario(5);

        // Vía carrito (HTTP).
        $this->actingAs($vendedor)
            ->post('/carrito/agregar/'.$producto->id, ['cantidad' => 1])
            ->assertSessionHas('error');
        $this->assertEmpty(session('carrito'));

        // Vía servicio (servidor).
        try {
            app(PedidoService::class)->crear($vendedor->id, $emp->id, [$producto->id => 1], 'efectivo', null);
            $this->fail('Se esperaba DomainException al pedir al propio emprendimiento.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('propio', $e->getMessage());
        }

        $this->assertDatabaseCount('pedidos', 0);
    }

    /** P15 — Pedir más unidades que el stock se rechaza con mensaje claro (RN-06). */
    public function test_p15_no_se_puede_pedir_mas_que_el_stock(): void
    {
        [, $emp, $producto] = $this->escenario(3);
        $comprador = User::factory()->create();

        try {
            app(PedidoService::class)->crear($comprador->id, $emp->id, [$producto->id => 999], 'efectivo', null);
            $this->fail('Se esperaba DomainException por stock insuficiente.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('Solo hay 3', $e->getMessage());
        }

        $this->assertDatabaseCount('pedidos', 0);
    }

    /** P16 — No se mezclan productos de dos emprendimientos en un carrito (RN-06). */
    public function test_p16_carrito_de_un_solo_emprendimiento(): void
    {
        $comprador = User::factory()->create();
        [, $empA, $productoA] = $this->escenario(5);
        [, $empB, $productoB] = $this->escenario(5);

        // El carrito ya tiene un producto del emprendimiento A.
        $this->actingAs($comprador)
            ->withSession(['carrito' => [$productoA->id => 1]])
            ->post('/carrito/agregar/'.$productoB->id, ['cantidad' => 1])
            ->assertSessionHas('error');

        $this->assertSame([$productoA->id => 1], session('carrito'));
    }

    /** P17 — Manipular el precio en el formulario se ignora (RNF-16). */
    public function test_p17_el_precio_se_toma_de_la_base_de_datos(): void
    {
        $comprador = User::factory()->create();
        [, $emp, $producto] = $this->escenario(10);

        $response = $this->actingAs($comprador)
            ->withSession(['carrito' => [$producto->id => 2]])
            ->post('/pedidos', [
                'metodo_pago' => 'efectivo',
                'precio' => 1,
                'total' => 1,
                'items' => [['producto_id' => $producto->id, 'precio_unitario' => 1, 'cantidad' => 2]],
            ]);

        $response->assertRedirect();

        $pedido = Pedido::latest('id')->first();
        $this->assertNotNull($pedido);
        $this->assertSame(20000.0, (float) $pedido->total);
        $this->assertSame(10000.0, (float) $pedido->items->first()->precio_unitario);
    }

    /** P18 — Dos compradores piden la última unidad; el segundo no se puede confirmar (RNF-16). */
    public function test_p18_sobreventa_de_la_ultima_unidad(): void
    {
        [, $emp, $producto] = $this->escenario(1);
        $servicio = app(PedidoService::class);

        $comprador1 = User::factory()->create();
        $comprador2 = User::factory()->create();

        $pedido1 = $servicio->crear($comprador1->id, $emp->id, [$producto->id => 1], 'efectivo', null);
        $pedido2 = $servicio->crear($comprador2->id, $emp->id, [$producto->id => 1], 'efectivo', null);

        $servicio->confirmar($pedido1);
        $this->assertSame(0, $producto->fresh()->stock);

        $this->expectException(DomainException::class);
        $servicio->confirmar($pedido2);
    }

    /** P19 — Confirmar descuenta stock y pasa a agotado al llegar a 0 (RF-21). */
    public function test_p19_confirmar_descuenta_stock_y_marca_agotado(): void
    {
        [, $emp, $producto] = $this->escenario(2);
        $servicio = app(PedidoService::class);

        $pedido = $servicio->crear(User::factory()->create()->id, $emp->id, [$producto->id => 2], 'efectivo', null);
        $servicio->confirmar($pedido);

        $this->assertSame(Pedido::ESTADO_CONFIRMADO, $pedido->fresh()->estado);
        $fresco = $producto->fresh();
        $this->assertSame(0, $fresco->stock);
        $this->assertSame(Producto::ESTADO_AGOTADO, $fresco->estado);
    }

    /** P20 — Cancelar un pedido confirmado devuelve el stock (RF-44). */
    public function test_p20_cancelar_confirmado_devuelve_stock(): void
    {
        [, $emp, $producto] = $this->escenario(5);
        $servicio = app(PedidoService::class);

        $pedido = $servicio->crear(User::factory()->create()->id, $emp->id, [$producto->id => 2], 'efectivo', null);
        $servicio->confirmar($pedido);
        $this->assertSame(3, $producto->fresh()->stock);

        $servicio->cancelarPorEmprendedor($pedido, 'No está disponible');

        $this->assertSame(Pedido::ESTADO_CANCELADO, $pedido->fresh()->estado);
        $this->assertSame(5, $producto->fresh()->stock);
        $this->assertSame(Producto::ESTADO_DISPONIBLE, $producto->fresh()->estado);
    }

    /** P21 — El pedido conserva el precio original aunque cambie el producto (RN-06). */
    public function test_p21_el_pedido_conserva_el_precio_original(): void
    {
        [, $emp, $producto] = $this->escenario(5);
        $servicio = app(PedidoService::class);

        $pedido = $servicio->crear(User::factory()->create()->id, $emp->id, [$producto->id => 1], 'efectivo', null);
        $this->assertSame(10000.0, (float) $pedido->total);

        $producto->update(['precio' => 25000]);

        $this->assertSame(10000.0, (float) $pedido->fresh()->total);
        $this->assertSame(10000.0, (float) $pedido->items->first()->precio_unitario);
    }
}
