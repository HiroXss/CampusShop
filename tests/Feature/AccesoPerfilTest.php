<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 1 — Acceso y perfil (RF-01 a RF-08).
 * Casos P01 a P04 del plan de pruebas (SRS 10.4).
 */
class AccesoPerfilTest extends TestCase
{
    use RefreshDatabase;

    /** P01 — Registro con correo de otro dominio se rechaza (RN-01). */
    public function test_p01_registro_con_correo_de_otro_dominio_se_rechaza(): void
    {
        $response = $this->post('/register', [
            'name' => 'Ajeno',
            'email' => 'ajeno@gmail.com',
            'program' => 'Ingeniería de Sistemas',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'ajeno@gmail.com']);
    }

    /** P02 — Registro con correo ya existente se rechaza. */
    public function test_p02_registro_con_correo_duplicado_se_rechaza(): void
    {
        User::factory()->create(['email' => 'duplicado@unimisionpaz.edu.co']);

        $response = $this->post('/register', [
            'name' => 'Duplicado',
            'email' => 'duplicado@unimisionpaz.edu.co',
            'program' => 'Ingeniería de Sistemas',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** P03 — Operar sin verificar el correo redirige a la verificación (RNF-01). */
    public function test_p03_sin_verificar_correo_se_redirige_a_verificacion(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/vender')
            ->assertRedirect(route('verification.notice'));

        $this->actingAs($user)
            ->get('/mi-tienda')
            ->assertRedirect(route('verification.notice'));
    }

    /** P04 — Inicio de sesión con cuenta desactivada se rechaza (RF-05). */
    public function test_p04_inicio_de_sesion_con_cuenta_desactivada_se_rechaza(): void
    {
        $user = User::factory()->inactivo()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
