<?php

namespace App\Services;

use App\Models\Emprendimiento;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * PedidoService centraliza el ciclo de vida de los pedidos (RFD-06, RN-06, RNF-16).
 * Las operaciones críticas corren en transacciones con bloqueo de filas (lockForUpdate)
 * para evitar la sobreventa. Los precios siempre se leen de la base de datos,
 * nunca del request.
 */
class PedidoService
{
    /**
     * Crea un pedido «pendiente» para un comprador.
     *
     * @param  int[]  $cantidades  mapa producto_id => cantidad
     */
    public function crear(int $compradorId, int $emprendimientoId, array $cantidades, string $metodoPago, ?string $nota): Pedido
    {
        return DB::transaction(function () use ($compradorId, $emprendimientoId, $cantidades, $metodoPago, $nota) {
            $comprador = User::findOrFail($compradorId);
            $emprendimiento = Emprendimiento::findOrFail($emprendimientoId);

            if ($emprendimiento->user_id === $compradorId) {
                throw new DomainException('No puedes pedir a tu propio emprendimiento.');
            }
            if (! $emprendimiento->isOperativo()) {
                throw new DomainException('El emprendimiento no está disponible para recibir pedidos.');
            }
            if (! in_array($metodoPago, [Pedido::PAGO_EFECTIVO, Pedido::PAGO_TRANSFERENCIA])) {
                throw new DomainException('El método de pago no es válido.');
            }
            if (empty($cantidades)) {
                throw new DomainException('El carrito está vacío.');
            }

            // Bloquea los productos del carrito para validar y congelar datos.
            $productos = Producto::whereIn('id', array_keys($cantidades))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $total = 0;
            $items = [];

            foreach ($cantidades as $productoId => $cantidad) {
                $cantidad = (int) $cantidad;
                $producto = $productos->get($productoId);

                if (! $producto || $producto->emprendimiento_id !== $emprendimientoId) {
                    throw new DomainException('Uno de los productos no pertenece a este emprendimiento.');
                }
                if (! $producto->isPedible()) {
                    throw new DomainException("«{$producto->nombre}» no está disponible para pedir.");
                }
                if ($cantidad < 1) {
                    throw new DomainException('La cantidad debe ser al menos 1.');
                }
                if ($producto->stock !== null && $cantidad > $producto->stock) {
                    throw new DomainException(
                        "Solo hay {$producto->stock} unidades de «{$producto->nombre}»."
                    );
                }

                $total += (float) $producto->precio * $cantidad;
                $items[] = [
                    'producto_id' => $producto->id,
                    'nombre_producto' => $producto->nombre,
                    'precio_unitario' => $producto->precio,
                    'cantidad' => $cantidad,
                ];
            }

            // Límite de pedidos por hora por usuario (RNF-13) hecho en el controlador
            // mediante throttle; aquí se centraliza la creación consistente.
            $pedido = Pedido::create([
                'comprador_id' => $compradorId,
                'emprendimiento_id' => $emprendimientoId,
                'estado' => Pedido::ESTADO_PENDIENTE,
                'metodo_pago' => $metodoPago,
                'nota' => $nota ?: null,
                'total' => $total,
            ]);

            foreach ($items as $item) {
                PedidoItem::create($item + ['pedido_id' => $pedido->id]);
            }

            return $pedido->load('items');
        });
    }

    /**
     * Confirma un pedido pendiente: descuenta el stock dentro de la transacción.
     */
    public function confirmar(Pedido $pedido): void
    {
        DB::transaction(function () use ($pedido) {
            $pedido->refresh();
            if ($pedido->estado !== Pedido::ESTADO_PENDIENTE) {
                throw new DomainException('Solo se puede confirmar un pedido pendiente.');
            }

            // Vuelve a validar el stock con bloqueo de filas (RNF-16).
            $productos = Producto::whereIn(
                'id',
                $pedido->items->pluck('producto_id')
            )->lockForUpdate()->get()->keyBy('id');

            foreach ($pedido->items as $item) {
                $producto = $productos->get($item->producto_id);
                if (! $producto) {
                    throw new DomainException("El producto «{$item->nombre_producto}» ya no existe.");
                }
                if ($producto->stock !== null && $producto->stock < $item->cantidad) {
                    throw new DomainException(
                        "Stock insuficiente de «{$item->nombre_producto}» (quedan {$producto->stock})."
                    );
                }
            }

            foreach ($pedido->items as $item) {
                $producto = $productos->get($item->producto_id);
                if ($producto->stock === null) {
                    continue; // servicio sin control de inventario
                }
                $producto->stock -= $item->cantidad;
                // Si el stock llega a 0, pasa automáticamente a «agotado» (RF-21, RN-03).
                if ($producto->stock <= 0) {
                    $producto->stock = 0;
                    $producto->estado = Producto::ESTADO_AGOTADO;
                }
                $producto->save();
            }

            $pedido->estado = Pedido::ESTADO_CONFIRMADO;
            $pedido->confirmado_at = now();
            $pedido->motivo = null;
            $pedido->save();
        });
    }

    /**
     * Rechaza un pedido pendiente con motivo obligatorio (estado final).
     */
    public function rechazar(Pedido $pedido, string $motivo): void
    {
        $pedido->refresh();
        if ($pedido->estado !== Pedido::ESTADO_PENDIENTE) {
            throw new DomainException('Solo se puede rechazar un pedido pendiente.');
        }
        if (trim($motivo) === '') {
            throw new DomainException('Debes indicar un motivo de rechazo.');
        }

        $pedido->estado = Pedido::ESTADO_RECHAZADO;
        $pedido->motivo = trim($motivo);
        $pedido->save();
    }

    /**
     * Marca un pedido confirmado como entregado (estado final, habilita reseña).
     */
    public function entregar(Pedido $pedido): void
    {
        $pedido->refresh();
        if ($pedido->estado !== Pedido::ESTADO_CONFIRMADO) {
            throw new DomainException('Solo se puede entregar un pedido confirmado.');
        }

        $pedido->estado = Pedido::ESTADO_ENTREGADO;
        $pedido->entregado_at = now();
        $pedido->save();
    }

    /**
     * Cancelación del comprador: solo pedidos pendientes, sin efecto en el stock.
     */
    public function cancelarPorComprador(Pedido $pedido): void
    {
        $pedido->refresh();
        if ($pedido->estado !== Pedido::ESTADO_PENDIENTE) {
            throw new DomainException('Solo puedes cancelar un pedido pendiente.');
        }

        $pedido->estado = Pedido::ESTADO_CANCELADO;
        $pedido->motivo = 'Cancelado por el comprador';
        $pedido->save();
    }

    /**
     * Cancelación del emprendedor: solo pedidos confirmados; devuelve el stock.
     */
    public function cancelarPorEmprendedor(Pedido $pedido, string $motivo): void
    {
        DB::transaction(function () use ($pedido, $motivo) {
            $pedido->refresh();
            if ($pedido->estado !== Pedido::ESTADO_CONFIRMADO) {
                throw new DomainException('Solo se puede cancelar un pedido confirmado.');
            }
            if (trim($motivo) === '') {
                throw new DomainException('Debes indicar un motivo de cancelación.');
            }

            // Devuelve el stock descontado (RF-44).
            $productos = Producto::whereIn('id', $pedido->items->pluck('producto_id'))
                ->lockForUpdate()
                ->get();

            foreach ($productos as $producto) {
                $item = $pedido->items->firstWhere('producto_id', $producto->id);
                if (! $item || $producto->stock === null) {
                    continue;
                }
                $producto->stock += $item->cantidad;
                if ($producto->estado === Producto::ESTADO_AGOTADO && $producto->stock > 0) {
                    $producto->estado = Producto::ESTADO_DISPONIBLE;
                }
                $producto->save();
            }

            $pedido->estado = Pedido::ESTADO_CANCELADO;
            $pedido->motivo = trim($motivo);
            $pedido->save();
        });
    }
}
