<?php

namespace Tests\Feature;

use App\Models\Conversacion;
use App\Models\Emprendimiento;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 7 — Chat (RF-29 a RF-34, RN-05).
 * Casos P11 a P13 del plan de pruebas (SRS 10.4).
 */
class ChatTest extends TestCase
{
    use RefreshDatabase;

    private function tienda(): array
    {
        $vendedor = User::factory()->create();
        $emp = Emprendimiento::factory()->create([
            'user_id' => $vendedor->id,
            'estado' => Emprendimiento::ESTADO_APROBADO,
        ]);
        $producto = Producto::factory()->create(['emprendimiento_id' => $emp->id]);

        return [$vendedor, $emp, $producto];
    }

    /** P11 — El dueño no puede iniciar una conversación consigo mismo (RN-05). */
    public function test_p11_no_se_puede_escribir_al_propio_emprendimiento(): void
    {
        [$vendedor, $emp] = $this->tienda();

        $this->actingAs($vendedor)
            ->post('/conversaciones/iniciar/'.$emp->slug)
            ->assertSessionHas('error');

        $this->assertDatabaseCount('conversaciones', 0);
    }

    /** P12 — Iniciar dos veces reutiliza la conversación existente (RN-05). */
    public function test_p12_una_sola_conversacion_por_par(): void
    {
        [, $emp, $producto] = $this->tienda();
        $comprador = User::factory()->create();

        $this->actingAs($comprador)->post('/conversaciones/iniciar/'.$emp->slug, ['producto_id' => $producto->id]);
        $this->actingAs($comprador)->post('/conversaciones/iniciar/'.$emp->slug);

        $this->assertDatabaseCount('conversaciones', 1);
        $conv = Conversacion::first();
        $this->assertSame($emp->id, $conv->emprendimiento_id);
        $this->assertSame($comprador->id, $conv->comprador_id);
    }

    /** P13 — Un tercero no puede abrir la conversación de otros (RN-05). */
    public function test_p13_un_tercero_no_puede_ver_la_conversacion(): void
    {
        [$vendedor, $emp] = $this->tienda();
        $comprador = User::factory()->create();

        $conv = Conversacion::create([
            'emprendimiento_id' => $emp->id,
            'comprador_id' => $comprador->id,
        ]);

        $tercero = User::factory()->create();

        $this->actingAs($tercero)
            ->get('/conversaciones/'.$conv->id)
            ->assertForbidden();

        // Los participantes sí pueden verla.
        $this->actingAs($comprador)->get('/conversaciones/'.$conv->id)->assertOk();
        $this->actingAs($vendedor)->get('/conversaciones/'.$conv->id)->assertOk();
    }
}
