<?php

namespace Database\Seeders;

use App\Models\Conversacion;
use App\Models\Emprendimiento;
use App\Models\Mensaje;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use App\Models\Reporte;
use App\Models\Resena;
use App\Models\User;
use App\Models\Visita;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Datos de demostración transversales: pedidos en todos los estados, reseñas,
 * conversaciones con mensajes, visitas y reportes. Alimenta el panel del
 * emprendedor, las métricas y el panel de moderación del administrador.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $emprendimientos = Emprendimiento::where('estado', Emprendimiento::ESTADO_APROBADO)->get();
        $compradores = User::where('role', User::ROLE_USER)
            ->whereDoesntHave('emprendimiento')
            ->get();

        if ($compradores->isEmpty()) {
            $compradores = User::factory()->count(5)->create();
        }

        // El estudiante demo debe tener pedidos propios (comprador principal).
        $dominio = config('campushop.university_domain');
        $estudiante = User::where('email', "estudiante@{$dominio}")->first() ?? $compradores->first();

        $estados = [
            Pedido::ESTADO_ENTREGADO,
            Pedido::ESTADO_CONFIRMADO,
            Pedido::ESTADO_PENDIENTE,
            Pedido::ESTADO_RECHAZADO,
            Pedido::ESTADO_CANCELADO,
        ];

        foreach ($emprendimientos as $indice => $emprendimiento) {
            $productos = $emprendimiento->productos()->get();
            if ($productos->isEmpty()) {
                continue;
            }

            // 5 pedidos por tienda, uno por cada estado posible (RF-41 a RF-44).
            foreach ($estados as $i => $estado) {
                // El primer pedido (entregado) es del estudiante demo para su historial.
                $comprador = ($indice === 0 && $i === 0) ? $estudiante : $compradores->random();
                if ($comprador->id === $emprendimiento->user_id) {
                    $comprador = $compradores->firstWhere('id', '!=', $emprendimiento->user_id) ?? $comprador;
                }

                $creado = now()->subDays(20 - $i * 3);

                $pedido = Pedido::create([
                    'comprador_id' => $comprador->id,
                    'emprendimiento_id' => $emprendimiento->id,
                    'estado' => $estado,
                    'metodo_pago' => $i % 2 === 0 ? Pedido::PAGO_EFECTIVO : Pedido::PAGO_TRANSFERENCIA,
                    'nota' => in_array($estado, [Pedido::ESTADO_PENDIENTE, Pedido::ESTADO_CONFIRMADO], true)
                        ? 'Entregar en la biblioteca, por favor.'
                        : null,
                    'motivo' => in_array($estado, [Pedido::ESTADO_RECHAZADO, Pedido::ESTADO_CANCELADO], true)
                        ? ($estado === Pedido::ESTADO_RECHAZADO ? 'No hay stock suficiente para la fecha.' : 'El comprador no confirmó a tiempo.')
                        : null,
                    'total' => 0,
                    'created_at' => $creado,
                    'updated_at' => $creado,
                    'confirmado_at' => in_array($estado, [Pedido::ESTADO_CONFIRMADO, Pedido::ESTADO_ENTREGADO], true) ? $creado->copy()->addHours(2) : null,
                    'entregado_at' => $estado === Pedido::ESTADO_ENTREGADO ? $creado->copy()->addDays(2) : null,
                ]);

                $total = 0;
                foreach ($productos->random(min(2, $productos->count())) as $producto) {
                    $cantidad = random_int(1, 3);
                    $total += (float) $producto->precio * $cantidad;

                    PedidoItem::create([
                        'pedido_id' => $pedido->id,
                        'producto_id' => $producto->id,
                        'nombre_producto' => $producto->nombre,
                        'precio_unitario' => $producto->precio,
                        'cantidad' => $cantidad,
                    ]);
                }
                $pedido->update(['total' => $total]);

                // Reseña tras la entrega (RN-07), una vez por pedido.
                if ($estado === Pedido::ESTADO_ENTREGADO) {
                    $resena = Resena::create([
                        'pedido_id' => $pedido->id,
                        'comprador_id' => $comprador->id,
                        'emprendimiento_id' => $emprendimiento->id,
                        'puntuacion' => random_int(4, 5),
                        'comentario' => collect([
                            'Excelente atención y producto de buena calidad.',
                            'Muy cumplido con la entrega, volveré a comprar.',
                            'El producto superó mis expectativas. ¡Recomendado!',
                            'Buena relación precio-calidad, atención muy amable.',
                        ])->random(),
                    ]);
                    // La tabla solo conserva created_at (sin updated_at).
                    \Illuminate\Support\Facades\DB::table('resenas')
                        ->where('id', $resena->id)
                        ->update(['created_at' => $creado->copy()->addDays(3)]);
                }
            }

            // Conversaciones de ejemplo con varios mensajes (RF-29 a RF-34).
            foreach ($compradores->take(3) as $comprador) {
                if ($comprador->id === $emprendimiento->user_id) {
                    continue;
                }
                $conversacion = Conversacion::firstOrCreate([
                    'emprendimiento_id' => $emprendimiento->id,
                    'comprador_id' => $comprador->id,
                ]);

                if ($conversacion->mensajes()->exists()) {
                    continue;
                }

                $producto = $productos->random();
                Mensaje::create([
                    'conversacion_id' => $conversacion->id,
                    'emisor_id' => $comprador->id,
                    'contenido' => "¡Hola! Vi \"{$producto->nombre}\" y me interesa. ¿Tienes disponibilidad?",
                ]);
                Mensaje::create([
                    'conversacion_id' => $conversacion->id,
                    'emisor_id' => $emprendimiento->user_id,
                    'contenido' => '¡Claro que sí! ¿Para qué fecha lo necesitas?',
                ]);
                Mensaje::create([
                    'conversacion_id' => $conversacion->id,
                    'emisor_id' => $comprador->id,
                    'contenido' => 'Para la próxima semana, si es posible.',
                ]);
                Mensaje::create([
                    'conversacion_id' => $conversacion->id,
                    'emisor_id' => $emprendimiento->user_id,
                    'contenido' => 'Perfecto, te lo tengo listo. ¡Gracias por escribir!',
                ]);
            }

            // Visitas de ejemplo para las métricas (RN-08).
            foreach (range(1, 60) as $v) {
                Visita::create([
                    'emprendimiento_id' => $emprendimiento->id,
                    'producto_id' => $productos->random()->id,
                    'user_id' => null,
                    'sesion' => hash('sha256', Str::random(12).$v),
                    'fecha' => now()->subDays(random_int(0, 25))->toDateString(),
                ]);
            }
        }

        // Reportes de ejemplo para el panel de moderación del admin (RF-50, RF-51).
        $productos = Producto::visibles()->get();
        $motivos = [
            'La descripción no coincide con el producto que recibí.',
            'El precio publicado parece incorrecto.',
            'La imagen no corresponde al producto real.',
            'Lenguaje inapropiado en la descripción.',
        ];

        foreach ($productos->take(4) as $i => $producto) {
            Reporte::firstOrCreate(
                [
                    'user_id' => $compradores[$i % $compradores->count()]->id,
                    'reportable_type' => 'producto',
                    'reportable_id' => $producto->id,
                ],
                [
                    'motivo' => $motivos[$i % count($motivos)],
                    'estado' => Reporte::ESTADO_PENDIENTE,
                ]
            );
        }

        // Un reporte sobre un emprendimiento.
        $primerEmprendimiento = $emprendimientos->first();
        if ($primerEmprendimiento) {
            Reporte::firstOrCreate(
                [
                    'user_id' => $compradores->last()->id,
                    'reportable_type' => 'emprendimiento',
                    'reportable_id' => $primerEmprendimiento->id,
                ],
                [
                    'motivo' => 'La tienda publica productos que no ofrece realmente.',
                    'estado' => Reporte::ESTADO_PENDIENTE,
                ]
            );
        }
    }
}
