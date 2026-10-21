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

        // [nombre, prefijo de correo, categoría, descripción, destacado, logo, instagram, color del banner]
        $definiciones = [
            [
                'Dulce Campus', 'laura@', 'Repostería',
                'Repostería artesanal hecha por estudiantes: tortas, cupcakes, cheesecakes y postres para cada ocasión.',
                true, 'logos/dulce-campus.jpg', '@dulcecampus', 'FFF1F5',
            ],
            [
                'Tutorías Pro', 'carlos@', 'Tutorías',
                'Asesorías y tutorías académicas de cálculo, programación y preparación de parciales, presenciales o virtuales.',
                true, 'logos/tutorias-pro.jpg', '@tutoriaspro', 'EEF0FF',
            ],
            [
                'Código Fácil', 'sofia@', 'Servicios académicos',
                'Apoyo en desarrollo de software, corrección de código y soporte técnico para tus proyectos académicos.',
                true, 'logos/codigo-facil.jpg', '@codigofacil', 'E8EEFF',
            ],
            [
                'Lothus', 'daniela@', 'Ropa y accesorios',
                'Gorras minimalistas con la flor de loto bordada y versículos de la Biblia. Diseño limpio, hecho a mano.',
                true, 'logos/lothus.jpg', '@lothus.co', 'F2F5FF',
            ],
        ];

        $dominio = config('campushop.university_domain');

        foreach ($definiciones as [$nombre, $prefijo, $categoriaNombre, $descripcion, $destacado, $logo, $instagram, $bannerColor]) {
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
                    'logo' => $logo,
                    'whatsapp' => '3'.random_int(100000000, 999999999),
                    'instagram' => $instagram,
                    'estado' => Emprendimiento::ESTADO_APROBADO,
                    'destacado' => $destacado,
                    'oculta' => false,
                ]
            );
        }

        // Un emprendimiento pendiente para el panel de aprobación del admin.
        $pendiente = User::firstOrCreate(
            ['email' => 'nuevo@'.$dominio],
            [
                'name' => 'Nuevo Emprendedor',
                'password' => bcrypt('password'),
                'role' => User::ROLE_USER,
                'program' => 'Ingeniería Industrial',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        Emprendimiento::updateOrCreate(
            ['nombre' => 'Snacks Universitarios'],
            [
                'user_id' => $pendiente->id,
                'categoria_id' => Categoria::where('nombre', 'Comidas y bebidas')->value('id') ?? Categoria::query()->first()->id,
                'slug' => 'snacks-universitarios',
                'descripcion' => 'Snacks, café y bebidas para las jornadas de estudio.',
                'logo' => 'emprendimientos/placeholder.svg',
                'estado' => Emprendimiento::ESTADO_PENDIENTE,
                'destacado' => false,
                'oculta' => false,
            ]
        );
    }
}
