<?php

use App\Models\Emprendimiento;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

if (! function_exists('imagen')) {
    /**
     * URL pública de una imagen guardada en el disco público.
     *
     * Si el archivo también existe en `public/` (como las imágenes del catálogo,
     * logos y avatares que viven en `public/img`), se sirve directamente desde
     * ahí. Esto permite que la app funcione en hostings compartidos donde
     * `symlink()` está deshabilitado y `php artisan storage:link` no es posible.
     *
     * Si no está en `public/` (subidas de usuarios), se usa el disco público
     * normal (`storage/app/public`).
     */
    function imagen(?string $ruta): string
    {
        if (! $ruta) {
            return '';
        }

        // Las imágenes del catálogo viven en public/img/{artes,logos,avatares},
        // por lo que la ruta guardada (p. ej. "artes/x.jpg") ya es válida allí.
        if (file_exists(public_path($ruta))) {
            return asset($ruta);
        }

        return Storage::url($ruta);
    }
}

if (! function_exists('cop')) {
    /**
     * Formatea un monto en pesos colombianos: $12.000 (RNF-17).
     */
    function cop(float|int|string|null $amount): string
    {
        return '$'.number_format((float) ($amount ?: 0), 0, ',', '.');
    }
}

if (! function_exists('carrito')) {
    /**
     * Contenido del carrito en sesión: [producto_id => cantidad] (RFD-06).
     */
    function carrito(): array
    {
        return Session::get('carrito', []);
    }
}

if (! function_exists('carritoCount')) {
    /**
     * Cantidad total de unidades del carrito (para el contador del navbar).
     */
    function carritoCount(): int
    {
        return array_sum(carrito());
    }
}

if (! function_exists('carritoVacio')) {
    function carritoVacio(): bool
    {
        return carritoCount() === 0;
    }
}

if (! function_exists('carritoProductos')) {
    /**
     * Productos del carrito con los datos vigentes de la base de datos (RN-16).
     */
    function carritoProductos(): Collection
    {
        $ids = array_keys(carrito());

        return $ids
            ? Producto::with('emprendimiento', 'imagenes', 'emprendimiento.user')
                ->whereIn('id', $ids)
                ->get()
            : collect();
    }
}

if (! function_exists('estadoChip')) {
    /**
     * Clase CSS del chip según el estado (colores de estado del diseño).
     */
    function estadoChip(string $estado): string
    {
        return match ($estado) {
            Pedido::ESTADO_ENTREGADO, Producto::ESTADO_DISPONIBLE => 'status-verde',
            Pedido::ESTADO_PENDIENTE => 'status-ambar',
            Pedido::ESTADO_CONFIRMADO => 'status-azul',
            Pedido::ESTADO_RECHAZADO, Pedido::ESTADO_CANCELADO, Producto::ESTADO_AGOTADO => 'status-rojo',
            Producto::ESTADO_INACTIVO, Emprendimiento::ESTADO_PAUSADO => 'status-gris',
            default => 'status-gris',
        };
    }
}

if (! function_exists('estadoLabel')) {
    /**
     * Etiqueta legible del estado en español.
     */
    function estadoLabel(string $estado): string
    {
        return match ($estado) {
            Pedido::ESTADO_PENDIENTE => 'Pendiente',
            Pedido::ESTADO_CONFIRMADO => 'Confirmado',
            Pedido::ESTADO_ENTREGADO => 'Entregado',
            Pedido::ESTADO_RECHAZADO => 'Rechazado',
            Pedido::ESTADO_CANCELADO => 'Cancelado',
            Producto::ESTADO_DISPONIBLE => 'Disponible',
            Producto::ESTADO_AGOTADO => 'Agotado',
            Producto::ESTADO_INACTIVO => 'Inactivo',
            Emprendimiento::ESTADO_PENDIENTE => 'Pendiente de aprobación',
            Emprendimiento::ESTADO_APROBADO => 'Aprobado',
            Emprendimiento::ESTADO_RECHAZADO => 'Rechazado',
            Emprendimiento::ESTADO_PAUSADO => 'Pausado',
            Emprendimiento::ESTADO_SUSPENDIDO => 'Suspendido',
            default => ucfirst($estado),
        };
    }
}
