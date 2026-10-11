<?php

namespace Tests\Feature;

use App\Models\Conversacion;
use App\Models\Emprendimiento;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 10 — Moderación y administración (RF-50 a RF-57, RN-11, RNF-01, RNF-13).
 * Casos P28 a P30 del plan de pruebas (SRS 10.4).
 */
class ModeracionTest extends TestCase
{
    use RefreshDatabase;

    /** P28 — Tres reportes pendientes ocultan el producto del catálogo (RN-11). */
    public function test_p28_tres_reportes_ocultan_el_producto(): void
    {
        $emp = Emprendimiento::factory()->create(['estado' => Emprendimiento::ESTADO_APROBADO]);
        $producto = Producto::factory()->create([
            'emprendimiento_id' => $emp->id,
            'estado' => Producto::ESTADO_DISPONIBLE,
        ]);

        $reporteros = User::factory()->count(3)->create();

        foreach ($reporteros as $indice => $usuario) {
            $this->actingAs($usuario)
                ->post('/reportes', [
                    'tipo' => 'producto',
                    'id' => $producto->id,
                    'motivo' => 'Contenido inapropiado',
                ])
                ->assertSessionHas('success');

            if ($indice < 2) {
                $this->assertFalse((bool) $producto->fresh()->oculto, 'No debería ocultarse antes del umbral.');
            }
        }

        $this->assertTrue((bool) $producto->fresh()->oculto);

        $idsCatalogo = $this->get('/productos')->viewData('productos')->pluck('id')->all();
        $this->assertNotContains($producto->id, $idsCatalogo);
    }

    /** P29 — Un estudiante no accede al panel de administración (RNF-01). */
    public function test_p29_estudiante_no_accede_a_admin(): void
    {
        $estudiante = User::factory()->create();

        $this->actingAs($estudiante)->get('/admin/reportes')->assertForbidden();
        $this->actingAs($estudiante)->get('/admin')->assertForbidden();
    }

    /** P30 — El mensaje 31 en un minuto se rechaza por límite (RNF-13). */
    public function test_p30_limite_de_mensajes_por_minuto(): void
    {
        $vendedor = User::factory()->create();
        $emp = Emprendimiento::factory()->create([
            'user_id' => $vendedor->id,
            'estado' => Emprendimiento::ESTADO_APROBADO,
        ]);
        $comprador = User::factory()->create();

        $conv = Conversacion::create([
            'emprendimiento_id' => $emp->id,
            'comprador_id' => $comprador->id,
        ]);

        $this->actingAs($comprador);

        $limite = (int) config('campushop.max_messages_per_minute');

        for ($i = 1; $i <= $limite; $i++) {
            $this->post('/conversaciones/'.$conv->id.'/mensajes', ['contenido' => "Mensaje {$i}"])
                ->assertRedirect();
        }

        $this->post('/conversaciones/'.$conv->id.'/mensajes', ['contenido' => 'Mensaje de más'])
            ->assertStatus(429);
    }
}
