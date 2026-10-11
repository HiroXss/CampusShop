<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Emprendimiento;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmprendimientoSeeder extends Seeder
{
    public function run(): void
    {
        // Copia un logo placeholder a storage para que las vistas lo muestren.
        Storage::disk('public')->put('emprendimientos/placeholder.svg', file_get_contents(public_path('img/logo-placeholder.svg')));

        $definiciones = [
            ['Dulce Campus', 'laura@', 'Repostería', 'Repostería artesanal hecha por estudiantes: tortas, cupcakes y postres para cada ocasión.', true],
            ['Tutorías Pro', 'carlos@', 'Tutorías', 'Asesorías y tutorías académicas de cálculo, programación y materias básicas.', true],
            ['Estampados UMP', 'maria@', 'Ropa y accesorios', 'Estampados personalizados en camisetas, mugs y accesorios con diseños únicos.', false],
            ['Manos Creativas', 'andres@', 'Arte y manualidades', 'Manualidades, ilustraciones y decoración hecha a mano por emprendedores.', false],
            ['Código Fácil', 'sofia@', 'Servicios académicos', 'Apoyo en desarrollo de software, trabajos y proyectos académicos.', true],
        ];

        $dominio = config('campushop.university_domain');

        foreach ($definiciones as [$nombre, $prefijo, $categoriaNombre, $descripcion, $destacado]) {
            $user = User::where('email', $prefijo.$dominio)->first();
            $categoria = Categoria::where('nombre', $categoriaNombre)->first();

            if (! $user || ! $categoria) {
                continue;
            }

            Emprendimiento::updateOrCreate(
                ['nombre' => $nombre],
                [
                    'user_id' => $user->id,
                    'categoria_id' => $categoria->id,
                    'slug' => Str::slug($nombre),
                    'descripcion' => $descripcion,
                    'logo' => 'emprendimientos/placeholder.svg',
                    'whatsapp' => '3'.random_int(100000000, 999999999),
                    'instagram' => '@'.Str::slug($nombre, ''),
                    'estado' => Emprendimiento::ESTADO_APROBADO,
                    'destacado' => $destacado,
                    'oculta' => false,
                ]
            );
        }

        // Un emprendimiento pendiente y uno rechazado para el panel de admin.
        $pendiente = User::factory()->create(['name' => 'Nuevo Emprendedor']);
        Emprendimiento::create([
            'user_id' => $pendiente->id,
            'categoria_id' => Categoria::query()->first()->id,
            'nombre' => 'Snacks Universitarios',
            'slug' => 'snacks-universitarios',
            'descripcion' => 'Snacks y bebidas para las jornadas de estudio.',
            'logo' => 'emprendimientos/placeholder.svg',
            'estado' => Emprendimiento::ESTADO_PENDIENTE,
            'destacado' => false,
            'oculta' => false,
        ]);
    }
}
