<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Emprendimiento;
use App\Models\Producto;
use App\Models\ProductoImagen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->put('productos/placeholder.svg', file_get_contents(public_path('img/placeholder-producto.svg')));

        // [nombre, tipo, precio, stock, categoría, descripción]
        $catalogo = [
            'Dulce Campus' => [
                ['Torta de chocolate', 'producto', 55000, 6, 'Repostería', 'Torta húmeda de chocolate con arequipe y cobertura de chocolate.'],
                ['Caja de cupcakes (6)', 'producto', 28000, 12, 'Repostería', 'Caja de seis cupcakes decorados a elección.'],
                ['Cheesecake de maracuyá', 'producto', 38000, 8, 'Repostería', 'Cheesecake cremoso con salsa de maracuyá natural.'],
                ['Alfajores artesanales', 'producto', 12000, 30, 'Repostería', 'Seis bocaditos de arequipe bañados en chocolate.'],
            ],
            'Tutorías Pro' => [
                ['Tutoría de Cálculo I (1 hora)', 'servicio', 25000, null, 'Tutorías', 'Asesoría personalizada de cálculo diferencial, presencial o virtual.'],
                ['Tutoría de Programación (1 hora)', 'servicio', 30000, null, 'Tutorías', 'Apoyo en fundamentos de programación y resolución de ejercicios.'],
                ['Preparación de parciales', 'servicio', 75000, null, 'Tutorías', 'Acompañamiento de dos horas para preparar tus parciales.'],
            ],
            'Estampados UMP' => [
                ['Camiseta personalizada', 'producto', 45000, 20, 'Ropa y accesorios', 'Camiseta algodón con estampado personalizado a elección.'],
                ['Mug personalizado', 'producto', 22000, 25, 'Ropa y accesorios', 'Mug cerámico con diseño impreso de alta durabilidad.'],
                ['Gorra bordada', 'producto', 35000, 15, 'Ropa y accesorios', 'Gorra con bordado del logo que elijas.'],
            ],
            'Manos Creativas' => [
                ['Ilustración personalizada', 'servicio', 60000, null, 'Arte y manualidades', 'Retrato ilustrado digital de la persona que elijas.'],
                ['Cuadro decorativo', 'producto', 40000, 10, 'Arte y manualidades', 'Cuadro decorativo pintado a mano en acrílico.'],
                ['Pulsera tejida', 'producto', 15000, 40, 'Arte y manualidades', 'Pulsera tejida a mano con mostacillas de colores.'],
            ],
            'Código Fácil' => [
                ['Página web básica', 'servicio', 350000, null, 'Servicios académicos', 'Desarrollo de página web básica para tu emprendimiento.'],
                ['Corrección de código', 'servicio', 50000, null, 'Servicios académicos', 'Revisión y corrección de proyectos de software.'],
                ['Soporte técnico a domicilio', 'servicio', 40000, null, 'Tecnología', 'Diagnóstico y solución de problemas de tu computador.'],
            ],
        ];

        $shops = Emprendimiento::whereIn('nombre', array_keys($catalogo))
            ->get()
            ->keyBy('nombre');

        foreach ($catalogo as $tienda => $productos) {
            $emprendimiento = $shops->get($tienda);
            if (! $emprendimiento) {
                continue;
            }

            foreach ($productos as [$nombre, $tipo, $precio, $stock, $categoriaNombre, $descripcion]) {
                $categoria = Categoria::where('nombre', $categoriaNombre)->first()
                    ?? Categoria::query()->first();

                $producto = Producto::updateOrCreate(
                    ['emprendimiento_id' => $emprendimiento->id, 'nombre' => $nombre],
                    [
                        'categoria_id' => $categoria->id,
                        'descripcion' => $descripcion,
                        'tipo' => $tipo === 'servicio' ? Producto::TIPO_SERVICIO : Producto::TIPO_PRODUCTO,
                        'precio' => $precio,
                        'stock' => $stock,
                        'estado' => Producto::ESTADO_DISPONIBLE,
                        'oculto' => false,
                    ]
                );

                if ($producto->imagenes()->count() === 0) {
                    ProductoImagen::create([
                        'producto_id' => $producto->id,
                        'ruta' => 'productos/placeholder.svg',
                        'orden' => 1,
                    ]);
                }
            }
        }
    }
}
