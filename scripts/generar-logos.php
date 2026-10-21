<?php

/**
 * Logos de los emprendimientos de CampusShop (800x800, formato cuadrado).
 *
 * Uso:  php scripts/generar-logos.php
 */

$destino = __DIR__.'/../public/img/logos';
@mkdir($destino, 0775, true);

/**
 * Flor de loto minimalista (Lothus), reutilizable.
 */
function loto(float $cx, float $cy, float $escala, string $petalo, string $trazo): string
{
    $p = [];
    $petalos = [
        [0, -1, 1.00], [-0.62, -0.62, 0.86], [0.62, -0.62, 0.86],
        [-1.05, 0.10, 0.66], [1.05, 0.10, 0.66],
    ];
    foreach ($petalos as [$dx, $dy, $esc]) {
        $x = $cx + $dx * 46 * $escala;
        $y = $cy + $dy * 46 * $escala;
        $p[] = sprintf(
            '<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f" transform="rotate(%.1f %.1f %.1f)" fill="%s" stroke="%s" stroke-width="%.1f"/>',
            $x, $y, 34 * $escala * $esc, 78 * $escala * $esc,
            $dx * 26, $x, $y, $petalo, $trazo, 3.4 * $escala
        );
    }
    $p[] = sprintf('<circle cx="%.1f" cy="%.1f" r="%.1f" fill="%s" stroke="%s" stroke-width="%.1f"/>',
        $cx, $cy + 20 * $escala, 16 * $escala, '#FF8A3D', $trazo, 3.4 * $escala);
    $p[] = sprintf('<path d="M%.1f %.1f q%.1f %.1f %.1f %.1f" stroke="%s" stroke-width="%.1f" fill="none" stroke-linecap="round"/>',
        $cx, $cy + 96 * $escala, 0, 30 * $escala, 0, 0, $petalo, 7 * $escala);

    return implode("\n  ", $p);
}

function marco(string $contenido, string $desde, string $hasta): string
{
    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="800" viewBox="0 0 800 800">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="{$desde}"/>
      <stop offset="1" stop-color="{$hasta}"/>
    </linearGradient>
  </defs>
  <rect width="800" height="800" rx="170" fill="url(#g)"/>
{$contenido}
</svg>
SVG;
}

$logos = [];

/* Dulce Campus — cupcake en círculo pastel */
$logos['dulce-campus'] = marco(<<<SVG
  <circle cx="400" cy="400" r="280" fill="#FFFFFF" opacity="0.55"/>
  <path d="M270 430 l34 150 a16 16 0 0 0 16 12 h160 a16 16 0 0 0 16 -12 l34 -150 z" fill="#D9A15B"/>
  <path d="M300 430 q0 -96 84 -96 q28 -62 116 -34 q66 -32 112 26 q60 18 52 104 z" fill="#F6C9D6"/>
  <path d="M312 408 q10 -62 82 -66 q36 -46 106 -20 q60 -26 94 36 q40 14 34 54 z" fill="#FAD9E3"/>
  <circle cx="400" cy="316" r="24" fill="#E23B3B"/>
  <circle cx="400" cy="304" r="6" fill="#FF7B7B"/>
  <path d="M400 316 q-6 -36 -28 -52" stroke="#5E8B4A" stroke-width="7" fill="none" stroke-linecap="round"/>
SVG, '#FFF1F5', '#FFD9E4');

/* Tutorías Pro — birrete con fórmula */
$logos['tutorias-pro'] = marco(<<<SVG
  <circle cx="400" cy="400" r="280" fill="#FFFFFF" opacity="0.5"/>
  <path d="M400 250 L610 340 L400 430 L190 340 Z" fill="#5B4BDB"/>
  <path d="M400 430 L530 378 L530 470 q-60 60 -130 60 q-70 0 -130 -60 L270 378 Z" fill="#3B3AB0"/>
  <path d="M590 348 v120" stroke="#FF8A3D" stroke-width="14" stroke-linecap="round"/>
  <circle cx="590" cy="486" r="22" fill="#FF8A3D"/>
  <text x="400" y="560" text-anchor="middle" font-family="Cambria Math, Georgia, serif"
        font-size="76" fill="#FF8A3D" font-weight="bold">∫f(x)dx</text>
SVG, '#EEF0FF', '#D5DBFF');

/* Código Fácil — llaves y terminal */
$logos['codigo-facil'] = marco(<<<SVG
  <circle cx="400" cy="400" r="280" fill="#FFFFFF" opacity="0.5"/>
  <rect x="200" y="270" width="400" height="270" rx="26" fill="#0F172A"/>
  <rect x="200" y="270" width="400" height="52" rx="26" fill="#334155"/>
  <circle cx="240" cy="296" r="10" fill="#FF5F57"/>
  <circle cx="272" cy="296" r="10" fill="#FEBC2E"/>
  <circle cx="304" cy="296" r="10" fill="#28C840"/>
  <text x="400" y="470" text-anchor="middle" font-family="Consolas, monospace"
        font-size="130" fill="#7DD3FC" font-weight="bold">&lt;/&gt;</text>
  <rect x="240" y="500" width="120" height="14" rx="7" fill="#FF8A3D"/>
SVG, '#E8EEFF', '#D3E0FF');

/* Lothus — flor de loto minimalista */
$logos['lothus'] = marco(
    '<circle cx="400" cy="400" r="280" fill="#FFFFFF" opacity="0.45"/>'."\n  "
    .loto(400, 360, 2.05, '#5B4BDB', '#FFFFFF')
    ."\n  "
    .'<text x="400" y="640" text-anchor="middle" font-family="Georgia, serif" font-size="64" font-style="italic" fill="#5B4BDB" opacity="0.9">Lothus</text>',
    '#F2F5FF', '#DDE6FF'
);

$n = 0;
foreach ($logos as $nombre => $svg) {
    file_put_contents("{$destino}/{$nombre}.svg", $svg);
    $n++;
}

echo "Logos generados: {$n} en {$destino}\n";
