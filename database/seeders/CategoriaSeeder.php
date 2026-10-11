<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            'Comidas y bebidas' => 'Almuerzos, snacks, bebidas y comidas preparadas.',
            'Repostería' => 'Postres, tortas, galletas y dulces caseros.',
            'Tutorías' => 'Clases y asesorías académicas entre estudiantes.',
            'Servicios académicos' => 'Transcripción, edición, diseño y apoyo académico.',
            'Ropa y accesorios' => 'Prendas, bisutería y accesorios.',
            'Arte y manualidades' => 'Ilustración, pintura, tejidos y trabajos manuales.',
            'Tecnología' => 'Desarrollo de software, soporte y accesorios tecnológicos.',
            'Papelería' => 'Útiles, apuntes e impresiones.',
        ];

        foreach ($categorias as $nombre => $descripcion) {
            Categoria::updateOrCreate(
                ['nombre' => $nombre],
                ['slug' => Str::slug($nombre), 'descripcion' => $descripcion]
            );
        }
    }
}