<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Emprendimiento;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Fase 4 — Productos y servicios (RF-15 a RF-22, RN-03, RN-10).
 * Casos P07 y P08 del plan de pruebas (SRS 10.4).
 */
class ProductoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function vendedorAprobado(): array
    {
        $user = User::factory()->create();
        $emp = Emprendimiento::factory()->create([
            'user_id' => $user->id,
            'estado' => Emprendimiento::ESTADO_APROBADO,
        ]);

        return [$user, $emp];
    }

    private function imagenValida(string $nombre = 'foto.png'): UploadedFile
    {
        return new UploadedFile(base_path('tests/Fixtures/imagen.png'), $nombre, 'image/png', null, true);
    }

    /** P07 — Imagen de 3 MB o en formato pdf se rechaza (RN-10). */
    public function test_p07_imagen_demasiado_grande_o_pdf_se_rechaza(): void
    {
        [$user] = $this->vendedorAprobado();
        $categoria = Categoria::factory()->create();

        $grande = $this->actingAs($user)->post('/mi-tienda/productos', [
            'nombre' => 'Con imagen grande',
            'descripcion' => 'Pesa demasiado.',
            'tipo' => 'producto',
            'categoria_id' => $categoria->id,
            'precio' => 5000,
            'stock' => 3,
            'imagenes' => [UploadedFile::fake()->create('grande.jpg', 3000, 'image/jpeg')],
        ]);
        $grande->assertSessionHasErrors('imagenes.0');

        $pdf = $this->actingAs($user)->post('/mi-tienda/productos', [
            'nombre' => 'Con pdf',
            'descripcion' => 'Formato inválido.',
            'tipo' => 'producto',
            'categoria_id' => $categoria->id,
            'precio' => 5000,
            'stock' => 3,
            'imagenes' => [UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf')],
        ]);
        $pdf->assertSessionHasErrors('imagenes.0');

        $this->assertDatabaseCount('productos', 0);
    }

    /** Crear un producto válido publica y asocia la imagen (RF-15 a RF-19). */
    public function test_crear_producto_valido_publica_con_imagen(): void
    {
        [$user, $emp] = $this->vendedorAprobado();
        $categoria = Categoria::factory()->create();

        $response = $this->actingAs($user)->post('/mi-tienda/productos', [
            'nombre' => 'Producto válido',
            'descripcion' => 'Descripción del producto.',
            'tipo' => 'producto',
            'categoria_id' => $categoria->id,
            'precio' => 12000,
            'stock' => 4,
            'imagenes' => [$this->imagenValida('foto.png')],
        ]);

        $response->assertRedirect(route('mi-tienda.index'));
        $this->assertDatabaseHas('productos', [
            'emprendimiento_id' => $emp->id,
            'nombre' => 'Producto válido',
            'estado' => Producto::ESTADO_DISPONIBLE,
        ]);
        $this->assertDatabaseCount('producto_imagenes', 1);
    }

    /** P08 — Editar el producto de otro emprendimiento responde 403 (RN-04). */
    public function test_p08_no_se_puede_editar_el_producto_de_otro(): void
    {
        [, $empA] = $this->vendedorAprobado();
        $producto = Producto::factory()->create(['emprendimiento_id' => $empA->id]);

        [$userB] = $this->vendedorAprobado();

        $this->actingAs($userB)
            ->get('/mi-tienda/productos/'.$producto->id.'/editar')
            ->assertForbidden();

        $this->actingAs($userB)
            ->delete('/mi-tienda/productos/'.$producto->id)
            ->assertForbidden();
    }
}
