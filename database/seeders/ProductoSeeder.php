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

        // [nombre, tipo, precio, stock, imágenes, descripción]
        $catalogo = [
            'Código Fácil' => [
                [
                    'Página web básica', 'servicio', 350000, null, ['web-basica', 'web-basica-responsive'],
                    'Desarrollo de una página web básica para tu emprendimiento: portada, catálogo y formulario de contacto. Incluye dominio por 1 año.',
                ],
                [
                    'Corrección de código', 'servicio', 50000, null, ['correccion-codigo', 'correccion-codigo-informe'],
                    'Revisión y corrección de proyectos de software: buenas prácticas, errores, rendimiento y entrega de un informe.',
                ],
                [
                    'Soporte técnico a domicilio', 'servicio', 40000, null, ['soporte-tecnico', 'soporte-tecnico-diagnostico'],
                    'Diagnóstico y solución de problemas de tu computador: lentitud, virus, formateo e instalación de programas.',
                ],
            ],
            'Lothus' => [
                [
                    'Gorra Loto Negra', 'producto', 45000, 15, ['gorra-loto-negra', 'gorra-loto-negra-perfil', 'gorra-loto-negra-bordado'],
                    "Gorra negra con la flor de loto bordada en blanco.\nBordado minimalista y versículo «Jehová es mi pastor; nada me faltará» — Salmo 23:1 en el lateral.",
                ],
                [
                    'Gorra Loto Beige', 'producto', 45000, 12, ['gorra-loto-beige', 'gorra-loto-beige-perfil', 'gorra-loto-beige-bordado'],
                    "Gorra beige de algodón con flor de loto bordada.\nIncluye el versículo «Todo lo puedo en Cristo que me fortalece» — Filipenses 4:13.",
                ],
                [
                    'Gorra Loto Blanca', 'producto', 45000, 18, ['gorra-loto-blanca', 'gorra-loto-blanca-perfil', 'gorra-loto-blanca-bordado'],
                    "Gorra blanca con flor de loto en azul índigo.\nVersículo «El Señor es mi luz y mi salvación» — Salmo 27:1 bordado al frente.",
                ],
                [
                    'Gorra Loto Azul', 'producto', 48000, 10, ['gorra-loto-azul', 'gorra-loto-azul-perfil', 'gorra-loto-azul-bordado'],
                    "Gorra azul índigo con flor de loto bordada en blanco.\nVersículo «Esfuérzate y sé valiente» — Josué 1:9. Edición limitada.",
                ],
            ],
        ];

        // Categoría por tienda (los servicios académicos usan la categoría del emprendimiento).
        $categoriaServicio = [
            'Código Fácil' => 'Servicios académicos',
            'Lothus' => 'Ropa y accesorios',
        ];

        $shops = Emprendimiento::whereIn('nombre', array_keys($catalogo))
            ->get()
            ->keyBy('nombre');

        foreach ($catalogo as $tienda => $productos) {
            $emprendimiento = $shops->get($tienda);
            if (! $emprendimiento) {
                continue;
            }

            foreach ($productos as [$nombre, $tipo, $precio, $stock, $imagenes, $descripcion]) {
                $categoria = Categoria::where('nombre', $categoriaServicio[$tienda])->first()
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

                // Asocia las imagenes del catalogo (public/img/artes). Las gorras
                // son SVG hechos a medida (color exacto) y los servicios, fotos JPG.
                $producto->imagenes()->delete();
                foreach (array_values($imagenes) as $i => $arte) {
                    $extension = str_starts_with($arte, 'gorra-loto') ? 'svg' : 'jpg';

                    ProductoImagen::create([
                        'producto_id' => $producto->id,
                        'ruta' => "artes/{$arte}.{$extension}",
                        'orden' => $i + 1,
                    ]);
                }
            }
        }
    }
}
