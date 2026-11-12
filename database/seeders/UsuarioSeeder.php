<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $dominio = config('campushop.university_domain');

        // Primer administrador (RF-07). Credenciales de demostración.
        User::updateOrCreate(
            ['email' => "admin@{$dominio}"],
            [
                'name' => 'Administrador CampusShop',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'program' => 'Administración',
                'is_active' => true,
                'email_verified_at' => now(),
                'avatar' => 'avatares/admin.jpg',
            ]
        );

        // Estudiante comprador de demostración.
        User::updateOrCreate(
            ['email' => "estudiante@{$dominio}"],
            [
                'name' => 'Estudiante Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
                'program' => 'Tecnología en Desarrollo de Software',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Emprendedores demo (uno por emprendimiento activo).
        $emprendedores = [
            ['Sofía Ramírez', 'sofia@'.$dominio, 'Tecnología en Desarrollo de Software'],
            ['Daniela Herrera', 'daniela@'.$dominio, 'Diseño Gráfico'],
        ];

        foreach ($emprendedores as [$nombre, $email, $programa]) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $nombre,
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_USER,
                    'program' => $programa,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }

        // Un comprador adicional para dar vida a las reseñas, pedidos y conversaciones.
        User::updateOrCreate(
            ['email' => "andres@{$dominio}"],
            [
                'name' => 'Andrés Peña',
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
                'program' => 'Ingeniería de Sistemas',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
