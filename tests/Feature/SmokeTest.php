<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Conversacion;
use App\Models\Emprendimiento;
use App\Models\Mensaje;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Prueba de humo: todas las páginas responden sin errores de servidor.
 */
class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_paginas_publicas(): void
    {
        $categoria = Categoria::factory()->create();
        $emp = Emprendimiento::factory()->destacado()->create(['categoria_id' => $categoria->id]);
        $producto = Producto::factory()->create([
            'emprendimiento_id' => $emp->id,
            'categoria_id' => $categoria->id,
        ]);

        $this->get('/')->assertOk();
        $this->get('/productos')->assertOk();
        $this->get('/productos/'.$producto->id)->assertOk();
        $this->get('/emprendimientos')->assertOk();
        $this->get('/emprendimientos/'.$emp->slug)->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_paginas_comprador(): void
    {
        $comprador = User::factory()->create();
        $vendedor = User::factory()->create();
        $emp = Emprendimiento::factory()->create(['user_id' => $vendedor->id]);
        $producto = Producto::factory()->create(['emprendimiento_id' => $emp->id]);

        $pedido = Pedido::factory()->entregado()->create([
            'comprador_id' => $comprador->id,
            'emprendimiento_id' => $emp->id,
            'total' => 10000,
        ]);
        PedidoItem::create([
            'pedido_id' => $pedido->id,
            'producto_id' => $producto->id,
            'nombre_producto' => $producto->nombre,
            'precio_unitario' => 5000,
            'cantidad' => 2,
        ]);

        $conv = Conversacion::create([
            'emprendimiento_id' => $emp->id,
            'comprador_id' => $comprador->id,
            'producto_id' => $producto->id,
        ]);
        Mensaje::create([
            'conversacion_id' => $conv->id,
            'emisor_id' => $vendedor->id,
            'contenido' => 'Hola, ¿sigue disponible?',
        ]);

        $this->actingAs($comprador)->get('/dashboard')->assertRedirect();
        $this->actingAs($comprador)->get('/profile')->assertOk();
        $this->actingAs($comprador)->get('/carrito')->assertOk();
        $this->actingAs($comprador)->get('/mis-pedidos')->assertOk();
        $this->actingAs($comprador)->get('/pedidos/'.$pedido->id)->assertOk();
        $this->actingAs($comprador)->get('/conversaciones')->assertOk();
        $this->actingAs($comprador)->get('/conversaciones/'.$conv->id)->assertOk();
        $this->actingAs($comprador)->get('/vender')->assertOk();
        $this->actingAs($comprador)->get('/mi-emprendimiento')->assertNotFound();
    }

    public function test_paginas_emprendedor(): void
    {
        $vendedor = User::factory()->create();
        $emp = Emprendimiento::factory()->create(['user_id' => $vendedor->id]);
        $producto = Producto::factory()->create(['emprendimiento_id' => $emp->id]);
        $comprador = User::factory()->create();

        $pedido = Pedido::factory()->create([
            'comprador_id' => $comprador->id,
            'emprendimiento_id' => $emp->id,
            'total' => 5000,
        ]);
        PedidoItem::create([
            'pedido_id' => $pedido->id,
            'producto_id' => $producto->id,
            'nombre_producto' => $producto->nombre,
            'precio_unitario' => 5000,
            'cantidad' => 1,
        ]);

        $this->actingAs($vendedor)->get('/mi-emprendimiento')->assertOk();
        $this->actingAs($vendedor)->get('/mi-tienda')->assertOk();
        $this->actingAs($vendedor)->get('/mi-tienda/productos/crear')->assertOk();
        $this->actingAs($vendedor)->get('/mi-tienda/productos/'.$producto->id.'/editar')->assertOk();
        $this->actingAs($vendedor)->get('/mi-tienda/metricas')->assertOk();
        $this->actingAs($vendedor)->get('/mi-tienda/pedidos')->assertOk();
        $this->actingAs($vendedor)->get('/mi-tienda/pedidos/'.$pedido->id)->assertOk();
    }

    public function test_paginas_admin(): void
    {
        $admin = User::factory()->admin()->create();
        Emprendimiento::factory()->pendiente()->create();
        Categoria::factory()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/aprobacion')->assertOk();
        $this->actingAs($admin)->get('/admin/emprendimientos')->assertOk();
        $this->actingAs($admin)->get('/admin/categorias')->assertOk();
        $this->actingAs($admin)->get('/admin/reportes')->assertOk();
        $this->actingAs($admin)->get('/admin/usuarios')->assertOk();
    }
}
