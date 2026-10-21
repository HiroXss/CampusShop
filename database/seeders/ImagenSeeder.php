<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Publica las imágenes del proyecto (public/img/artes y public/img/logos) en el
 * disco público, para que las vistas puedan servirlas con Storage::url().
 *
 * Las imágenes fuente viven en el repositorio (public/img), de modo que un
 * `migrate:fresh --seed` en un clon nuevo vuelve a dejar el catálogo con arte real.
 */
class ImagenSeeder extends Seeder
{
    public function run(): void
    {
        $mapas = [
            public_path('img/artes') => 'artes',
            public_path('img/logos') => 'logos',
        ];

        foreach ($mapas as $origen => $destino) {
            if (! File::isDirectory($origen)) {
                continue;
            }

            foreach (File::files($origen) as $archivo) {
                Storage::disk('public')->put(
                    "{$destino}/{$archivo->getFilename()}",
                    File::get($archivo->getPathname())
                );
            }
        }
    }
}
