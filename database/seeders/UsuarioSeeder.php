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

        // Emprendedores demo (uno por emprendimiento).
        $emprendedores = [
            ['Laura Gómez', 'laura@'.$dominio, 'Ingeniería de Sistemas'],
            ['Carlos Ruiz', 'carlos@'.$dominio, 'Administración de Empresas'],
            ['María Torres', 'maria@'.$dominio, 'Diseño Gráfico'],
            ['Andrés Peña', 'andres@'.$dominio, 'Contaduría Pública'],
            ['Sofía Ramírez', 'sofia@'.$dominio, 'Tecnología en Desarrollo de Software'],
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

        // Compradores adicionales para dar vida a las reseñas y pedidos.
        User::factory()->count(8)->create();
    }
}
