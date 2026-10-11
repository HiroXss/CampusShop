<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Datos de demostración de CampusShop, en orden de dependencias.
     */
    public function run(): void
    {
        $this->call([
            CategoriaSeeder::class,
            UsuarioSeeder::class,
            EmprendimientoSeeder::class,
            ProductoSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
