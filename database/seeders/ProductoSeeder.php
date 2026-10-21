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
            'Dulce Campus' => [
                [
                    'Torta de chocolate', 'producto', 55000, 6, ['torta-chocolate', 'torta-chocolate-corte', 'torta-chocolate-personalizada'],
                    "Torta húmeda de chocolate con relleno de arequipe y cobertura de chocolate semiamargo.\nPorción para 12 personas. Se prepara por encargo con 24 horas de anticipación.",
                ],
                [
                    'Caja de cupcakes (6)', 'producto', 28000, 12, ['cupcakes', 'cupcakes-variedad'],
                    "Caja de seis cupcakes decorados a elección: vainilla, chocolate o red velvet.\nIdeales para cumpleaños y reuniones de estudio.",
                ],
                [
                    'Cheesecake de maracuyá', 'producto', 38000, 8, ['cheesecake', 'cheesecake-porcion'],
                    'Cheesecake cremoso con base de galleta y salsa de maracuyá natural. Disponible en porciones individuales o entero.',
                ],
                [
                    'Alfajores artesanales', 'producto', 12000, 30, ['alfajores', 'alfajores-caja'],
                    'Seis bocaditos de arequipe bañados en chocolate, espolvoreados con azúcar en polvo. Receta de la abuela.',
                ],
            ],
            'Tutorías Pro' => [
                [
                    'Tutoría de Cálculo I (1 hora)', 'servicio', 25000, null, ['tutoria-calculo', 'tutoria-calculo-pizarra'],
                    'Asesoría personalizada de cálculo diferencial: límites, derivadas e integrales. Presencial en el campus o virtual por videollamada.',
                ],
                [
                    'Tutoría de Programación (1 hora)', 'servicio', 30000, null, ['tutoria-programacion', 'tutoria-programacion-consola'],
                    'Apoyo en fundamentos de programación, estructuras de datos y resolución de ejercicios en Python, Java o C++.',
                ],
                [
                    'Preparación de parciales', 'servicio', 75000, null, ['preparacion-parciales', 'preparacion-parciales-simulacro'],
                    'Acompañamiento de dos horas para preparar tus parciales: repaso de temas, simulacros y tips de examen.',
                ],
            ],
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
                    'Gorra Loto Negra', 'producto', 45000, 15, ['gora-loto-negra', 'gora-loto-negra-perfil', 'gora-loto-negra-bordado'],
                    "Gorra negra con la flor de loto bordada en blanco.\nBordado minimalista y versículo «Jehová es mi pastor; nada me faltará» — Salmo 23:1 en el lateral.",
                ],
                [
                    'Gorra Loto Beige', 'producto', 45000, 12, ['gora-loto-beige', 'gora-loto-beige-perfil', 'gora-loto-beige-bordado'],
                    "Gorra beige de algodón con flor de loto bordada.\nIncluye el versículo «Todo lo puedo en Cristo que me fortalece» — Filipenses 4:13.",
                ],
                [
                    'Gorra Loto Blanca', 'producto', 45000, 18, ['gora-loto-blanca', 'gora-loto-blanca-perfil', 'gora-loto-blanca-bordado'],
                    "Gorra blanca con flor de loto en azul índigo.\nVersículo «El Señor es mi luz y mi salvación» — Salmo 27:1 bordado al frente.",
                ],
                [
                    'Gorra Loto Azul', 'producto', 48000, 10, ['gora-loto-azul', 'gora-loto-azul-perfil', 'gora-loto-azul-bordado'],
                    "Gorra azul índigo con flor de loto bordada en blanco.\nVersículo «Esfuérzate y sé valiente» — Josué 1:9. Edición limitada.",
                ],
            ],
        ];

        // Categoría por tienda (los servicios académicos usan la categoría del emprendimiento).
        $categoriaServicio = [
            'Tutorías Pro' => 'Tutorías',
            'Código Fácil' => 'Servicios académicos',
            'Dulce Campus' => 'Repostería',
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

                // Asocia las imagenes del catalogo (public/img/artes).
                $producto->imagenes()->delete();
                foreach (array_values($imagenes) as $i => $arte) {
                    ProductoImagen::create([
                        'producto_id' => $producto->id,
                        'ruta' => "artes/{$arte}.jpg",
                        'orden' => $i + 1,
                    ]);
                }
            }
        }
    }
}
