<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Emprendimiento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Fase 3 — Emprendimientos (RF-09 a RF-14, RN-02).
 * Casos P05 y P06 del plan de pruebas (SRS 10.4).
 */
class EmprendimientoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function imagenValida(string $nombre = 'logo.png'): UploadedFile
    {
        return new UploadedFile(base_path('tests/Fixtures/imagen.png'), $nombre, 'image/png', null, true);
    }

    /** P05 — Un estudiante no puede crear un segundo emprendimiento (RN-02). */
    public function test_p05_no_se_puede_crear_un_segundo_emprendimiento(): void
    {
        $user = User::factory()->create();
        $existente = Emprendimiento::factory()->create([
            'user_id' => $user->id,
            'estado' => Emprendimiento::ESTADO_PENDIENTE,
        ]);
        $categoria = Categoria::factory()->create();

        $response = $this->actingAs($user)->post('/vender', [
            'nombre' => 'Segundo Emprendimiento',
            'descripcion' => 'No debería crearse.',
            'categoria_id' => $categoria->id,
            'logo' => $this->imagenValida('logo.png'),
        ]);

        $response->assertRedirect(route('mi-tienda.index'));
        $this->assertSame(1, Emprendimiento::count());
        $this->assertDatabaseHas('emprendimientos', ['id' => $existente->id]);

        // El formulario de creación también redirige a la tienda existente.
        $this->actingAs($user)->get('/vender')->assertRedirect(route('mi-tienda.index'));
    }

    /** P06 — Un emprendimiento pendiente no puede publicar productos (RN-02). */
    public function test_p06_emprendimiento_pendiente_no_publica_productos(): void
    {
        $user = User::factory()->create();
        Emprendimiento::factory()->pendiente()->create(['user_id' => $user->id]);
        $categoria = Categoria::factory()->create();

        $response = $this->actingAs($user)->post('/mi-tienda/productos', [
            'nombre' => 'Producto no autorizado',
            'descripcion' => 'No debe publicarse.',
            'tipo' => 'producto',
            'categoria_id' => $categoria->id,
            'precio' => 10000,
            'stock' => 5,
            'imagenes' => [$this->imagenValida('foto.png')],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('productos', ['nombre' => 'Producto no autorizado']);
    }
}
