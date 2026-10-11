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

        foreach ($emprendimientos as $emprendimiento) {
            $productos = $emprendimiento->productos()->get();
            if ($productos->isEmpty()) {
                continue;
            }

            // Pedidos en distintos estados para alimentar las métricas (RN-08).
            foreach (range(1, 4) as $i) {
                $comprador = $compradores->random();
                if ($comprador->id === $emprendimiento->user_id) {
                    continue;
                }

                $estado = match ($i) {
                    1 => Pedido::ESTADO_ENTREGADO,
                    2 => Pedido::ESTADO_ENTREGADO,
                    3 => Pedido::ESTADO_PENDIENTE,
                    default => Pedido::ESTADO_CONFIRMADO,
                };

                $pedido = Pedido::create([
                    'comprador_id' => $comprador->id,
                    'emprendimiento_id' => $emprendimiento->id,
                    'estado' => $estado,
                    'metodo_pago' => $i % 2 === 0 ? Pedido::PAGO_EFECTIVO : Pedido::PAGO_TRANSFERENCIA,
                    'nota' => $i === 3 ? 'Entregar en la biblioteca, por favor.' : null,
                    'total' => 0,
                    'confirmado_at' => $estado !== Pedido::ESTADO_PENDIENTE ? now()->subDays(3) : null,
                    'entregado_at' => $estado === Pedido::ESTADO_ENTREGADO ? now()->subDays(random_int(1, 5)) : null,
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

                // Reseña tras la entrega (RN-07).
                if ($estado === Pedido::ESTADO_ENTREGADO) {
                    Resena::create([
                        'pedido_id' => $pedido->id,
                        'comprador_id' => $comprador->id,
                        'emprendimiento_id' => $emprendimiento->id,
                        'puntuacion' => random_int(4, 5),
                        'comentario' => 'Excelente atención y producto de buena calidad.',
                    ]);
                }
            }

            // Conversaciones de ejemplo con mensajes (RF-29 a RF-34).
            foreach ($compradores->take(2) as $comprador) {
                if ($comprador->id === $emprendimiento->user_id) {
                    continue;
                }
                $conversacion = Conversacion::firstOrCreate([
                    'emprendimiento_id' => $emprendimiento->id,
                    'comprador_id' => $comprador->id,
                ]);
                Mensaje::create([
                    'conversacion_id' => $conversacion->id,
                    'emisor_id' => $comprador->id,
                    'contenido' => '¡Hola! ¿Tienes disponibilidad de tu producto?',
                ]);
                Mensaje::create([
                    'conversacion_id' => $conversacion->id,
                    'emisor_id' => $emprendimiento->user_id,
                    'contenido' => '¡Claro que sí! ¿Para cuándo lo necesitas?',
                ]);
            }

            // Visitas de ejemplo (RN-08).
            foreach (range(1, 25) as $v) {
                Visita::create([
                    'emprendimiento_id' => $emprendimiento->id,
                    'producto_id' => $productos->random()->id,
                    'user_id' => null,
                    'sesion' => hash('sha256', Str::random(12).$v),
                    'fecha' => now()->subDays(random_int(0, 20))->toDateString(),
                ]);
            }
        }

        // Un reporte de ejemplo sobre un producto.
        $producto = Producto::query()->first();
        $reportador = $compradores->first();
        if ($producto && $reportador) {
            Reporte::firstOrCreate(
                [
                    'user_id' => $reportador->id,
                    'reportable_type' => 'producto',
                    'reportable_id' => $producto->id,
                ],
                ['motivo' => 'La descripción no coincide con el producto.', 'estado' => Reporte::ESTADO_PENDIENTE]
            );
        }
    }
}
