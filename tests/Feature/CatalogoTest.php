<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Emprendimiento;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 5 — Catálogo y home (RF-23 a RF-28, RN-03, RN-12).
 * Casos P09 y P10 del plan de pruebas (SRS 10.4).
 */
class CatalogoTest extends TestCase
{
    use RefreshDatabase;

    /** P09 — Búsqueda combinada por texto, categoría, tipo y rango de precio. */
    public function test_p09_busqueda_combinada_devuelve_solo_coincidencias(): void
    {
        $catA = Categoria::factory()->create(['nombre' => 'Ropa']);
        $catB = Categoria::factory()->create(['nombre' => 'Diseño']);
        $emp = Emprendimiento::factory()->create(['estado' => Emprendimiento::ESTADO_APROBADO]);

        $camiseta = Producto::factory()->create([
            'emprendimiento_id' => $emp->id,
            'categoria_id' => $catA->id,
            'nombre' => 'Camiseta universitaria',
            'tipo' => Producto::TIPO_PRODUCTO,
            'precio' => 30000,
            'estado' => Producto::ESTADO_DISPONIBLE,
        ]);
        $logo = Producto::factory()->create([
            'emprendimiento_id' => $emp->id,
            'categoria_id' => $catB->id,
            'nombre' => 'Diseño de logo',
            'tipo' => Producto::TIPO_SERVICIO,
            'precio' => 80000,
            'estado' => Producto::ESTADO_DISPONIBLE,
        ]);

        $response = $this->get('/productos?q=Camiseta&categoria='.$catA->id.'&tipo=producto&precio_min=10000&precio_max=40000');

        $response->assertOk();
        $nombres = $response->viewData('productos')->pluck('nombre')->all();
        $this->assertContains($camiseta->nombre, $nombres);
        $this->assertNotContains($logo->nombre, $nombres);

        // El filtro por tipo servicio deja fuera la camiseta (tipo producto).
        $soloServicio = $this->get('/productos?q=Camiseta&tipo=servicio');
        $this->assertSame([], $soloServicio->viewData('productos')->pluck('nombre')->all());
    }

    /** P10 — El inactivo no aparece y el agotado aparece sin botón de pedido (RN-03). */
    public function test_p10_inactivo_no_aparece_y_agotado_sin_boton(): void
    {
        $emp = Emprendimiento::factory()->create(['estado' => Emprendimiento::ESTADO_APROBADO]);

        $disponible = Producto::factory()->create([
            'emprendimiento_id' => $emp->id,
            'nombre' => 'Producto disponible',
            'estado' => Producto::ESTADO_DISPONIBLE,
            'stock' => 10,
        ]);
        $agotado = Producto::factory()->agotado()->create([
            'emprendimiento_id' => $emp->id,
            'nombre' => 'Producto agotado',
        ]);
        $inactivo = Producto::factory()->create([
            'emprendimiento_id' => $emp->id,
            'nombre' => 'Producto inactivo',
            'estado' => Producto::ESTADO_INACTIVO,
        ]);

        $catalogo = $this->get('/productos');
        $ids = $catalogo->viewData('productos')->pluck('id')->all();

        $this->assertContains($disponible->id, $ids);
        $this->assertContains($agotado->id, $ids);
        $this->assertNotContains($inactivo->id, $ids);

        // El detalle del agotado no ofrece pedirlo: muestra «Producto agotado» y
        // no incluye el formulario de agregar al carrito de ESTE producto (RN-03).
        $this->get('/productos/'.$agotado->id)
            ->assertOk()
            ->assertSee('Producto agotado')
            ->assertDontSee(route('carrito.agregar', $agotado));

        // El detalle del inactivo no es visible para el público.
        $this->get('/productos/'.$inactivo->id)->assertNotFound();
    }
}
