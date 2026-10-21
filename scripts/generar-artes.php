<?php

/**
 * Generador de artes SVG para CampusShop.
 *
 * Los productos que requieren coincidencia exacta (por ejemplo el color de una
 * gorra) se generan como SVG hechos a medida. Los servicios se sirven con fotos
 * reales, por lo que aquí solo se generan las gorras de Lothus.
 *
 * Uso:  php scripts/generar-artes.php
 */

$destino = __DIR__.'/../public/img/artes';
@mkdir($destino, 0775, true);

/**
 * Envuelve el contenido en un lienzo cuadrado con fondo degradado y sombra suave.
 */
function lienzo(string $contenido, string $desde = '#EEF0FF', string $hasta = '#DCE3FF'): string
{
    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="800" viewBox="0 0 800 800">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="{$desde}"/>
      <stop offset="1" stop-color="{$hasta}"/>
    </linearGradient>
    <filter id="sombra" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="14" stdDeviation="18" flood-color="#2A2350" flood-opacity="0.18"/>
    </filter>
  </defs>
  <rect width="800" height="800" fill="url(#bg)"/>
  <circle cx="700" cy="110" r="150" fill="#FFFFFF" opacity="0.35"/>
  <circle cx="90" cy="700" r="180" fill="#FFFFFF" opacity="0.25"/>
  <g filter="url(#sombra)">
{$contenido}
  </g>
</svg>
SVG;
}

/** Pinta un texto centrado en estilo etiqueta minimalista. */
function versiculo(string $texto, float $y = 690): string
{
    $t = htmlspecialchars($texto, ENT_QUOTES);

    return <<<SVG
  <text x="400" y="{$y}" text-anchor="middle" font-family="Georgia, 'Times New Roman', serif"
        font-size="30" font-style="italic" fill="#5B4BDB" opacity="0.85">{$t}</text>
SVG;
}

/** Flor de loto estilizada, reutilizable. */
function loto(float $cx, float $cy, float $escala = 1.0, string $color = '#5B4BDB', string $colorBorde = '#FFFFFF'): string
{
    $p = [];
    // Pétalos (5): centro + laterales en dos niveles.
    $petalos = [
        [0, -1, 1.00],
        [-0.62, -0.62, 0.86],
        [0.62, -0.62, 0.86],
        [-1.05, 0.10, 0.66],
        [1.05, 0.10, 0.66],
    ];
    foreach ($petalos as [$dx, $dy, $esc]) {
        $x = $cx + $dx * 46 * $escala;
        $y = $cy + $dy * 46 * $escala;
        $w = 34 * $escala * $esc;
        $h = 78 * $escala * $esc;
        $rot = $dx * 26;
        $p[] = sprintf(
            '<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f" transform="rotate(%.1f %.1f %.1f)" fill="%s" stroke="%s" stroke-width="%.1f"/>',
            $x, $y, $w, $h, $rot, $x, $y, $color, $colorBorde, 4 * $escala
        );
    }
    // Núcleo.
    $p[] = sprintf('<circle cx="%.1f" cy="%.1f" r="%.1f" fill="%s" stroke="%s" stroke-width="%.1f"/>', $cx, $cy + 20 * $escala, 16 * $escala, '#FF8A3D', $colorBorde, 4 * $escala);
    $p[] = sprintf('<path d="M%.1f %.1f q%.1f %.1f %.1f %.1f" stroke="%s" stroke-width="%.1f" fill="none" stroke-linecap="round"/>',
        $cx, $cy + 96 * $escala, 0, 34 * $escala, 0, 0, $color, 8 * $escala);

    return implode("\n  ", $p);
}

/** Gorra minimalista: visera + copa + logo. */
function gorra(float $cx, float $cy, float $escala, string $copa, string $visera, float $logoCx = 0, float $logoCy = 0): string
{
    $x = $cx; $y = $cy;
    $s = $escala;

    $copaPath = sprintf(
        'M%.1f %.1f a%.1f %.1f 0 0 1 %.1f 0 l0 %.1f a%.1f %.1f 0 0 1 -%.1f 0 z',
        $x - 150 * $s, $y, 150 * $s, 160 * $s, 300 * $s, 90 * $s, 160 * $s, 30 * $s, 300 * $s
    );

    $g = [];
    $g[] = sprintf('<path d="M%.1f %.1f q%.1f %.1f %.1f %.1f l%.1f %.1f q-%.1f -%.1f -%.1f -%.1f z" fill="%s"/>',
        $x - 300 * $s, $y + 92 * $s, 120 * $s, 40 * $s, 300 * $s, 40 * $s, 0, 0, 300 * $s, 40 * $s, 300 * $s, -40 * $s, $visera);
    $g[] = sprintf('<path d="%s" fill="%s"/>', $copaPath, $copa);
    // Costuras.
    $g[] = sprintf('<path d="M%.1f %.1f q0 -%.1f %.1f -%.1f" stroke="#00000022" stroke-width="%.1f" fill="none"/>',
        $x, $y + 90 * $s, 150 * $s, 0, -150 * $s, 6 * $s);
    $g[] = sprintf('<path d="M%.1f %.1f v%.1f M%.1f %.1f v%.1f" stroke="#00000018" stroke-width="%.1f"/>',
        $x - 60 * $s, $y - 20 * $s, 110 * $s, $x + 60 * $s, $y - 20 * $s, 110 * $s, 4 * $s);
    $g[] = sprintf('<circle cx="%.1f" cy="%.1f" r="%.1f" fill="%s"/>', $x, $y - 150 * $s, 12 * $s, $visera);
    // Logo (flor de loto) centrado en la copa.
    $g[] = loto($x + $logoCx, $y - 30 * $s + $logoCy, 0.62 * $s, '#FFFFFF', $copa === '#FFFFFF' ? '#5B4BDB' : 'transparent');

    return implode("\n  ", $g);
}

/** Gorra de perfil: copa lateral + visera hacia la derecha. */
function gorraPerfil(float $cx, float $cy, float $escala, string $copa, string $visera): string
{
    $x = $cx; $y = $cy;
    $s = $escala;

    $g = [];
    // Visera lateral hacia la derecha.
    $g[] = sprintf('<path d="M%.1f %.1f q%.1f %.1f %.1f %.1f l%.1f %.1f q-%.1f -%.1f -%.1f -%.1f z" fill="%s"/>',
        $x + 40 * $s, $y + 70 * $s, 150 * $s, 10 * $s, 260 * $s, 60 * $s, 0, 0, 260 * $s, -60 * $s, 150 * $s, -30 * $s, $visera);
    // Copa de perfil (media cúpula).
    $g[] = sprintf('<path d="M%.1f %.1f a%.1f %.1f 0 0 1 %.1f 0 l0 %.1f a%.1f %.1f 0 0 1 -%.1f 0 z" fill="%s"/>',
        $x - 170 * $s, $y + 70 * $s, 170 * $s, 200 * $s, 340 * $s, 20 * $s, 200 * $s, 20 * $s, 340 * $s, $copa);
    // Costura central.
    $g[] = sprintf('<path d="M%.1f %.1f q%.1f -%.1f %.1f -%.1f" stroke="#00000022" stroke-width="%.1f" fill="none"/>',
        $x - 170 * $s, $y + 70 * $s, 170 * $s, 200 * $s, 340 * $s, 0, 6 * $s);
    // Botón superior.
    $g[] = sprintf('<circle cx="%.1f" cy="%.1f" r="%.1f" fill="%s"/>', $x, $y - 130 * $s, 12 * $s, $visera);
    // Loto al frente.
    $g[] = loto($x - 40 * $s, $y - 10 * $s, 0.55 * $s, '#FFFFFF', $copa === '#FFFFFF' ? '#5B4BDB' : 'transparent');

    return implode("\n  ", $g);
}

/** Gorra vista de detalle del bordado (primer plano del logo). */
function gorraBordado(float $cx, float $cy, float $escala, string $copa, string $hilo): string
{
    $x = $cx; $y = $cy;
    $s = $escala;

    $g = [];
    // Parche de tela con textura de bordado.
    $g[] = sprintf('<rect x="%.1f" y="%.1f" width="%.1f" height="%.1f" rx="%.1f" fill="%s"/>',
        $x - 280 * $s, $y - 280 * $s, 560 * $s, 560 * $s, 40 * $s, $copa);
    // Líneas de tela.
    for ($i = 0; $i < 6; $i++) {
        $yy = $y - 240 * $s + $i * 96 * $s;
        $g[] = sprintf('<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="#00000012" stroke-width="%.1f"/>',
            $x - 260 * $s, $yy, $x + 260 * $s, $yy, 3 * $s);
    }
    // Flor de loto bordada en grande, con color de hilo.
    $g[] = loto($x, $y, 1.7 * $s, $hilo, $copa === '#FFFFFF' ? '#5B4BDB' : 'transparent');

    return implode("\n  ", $g);
}

$artes = [];

/* ------------------------------------------------------------------ */
/* LOTHUS — gorras minimalistas con flor de loto y versículos          */
/* ------------------------------------------------------------------ */

$gorras = [
    'negra' => ['copa' => '#2C2C3A', 'visera' => '#1F1F2A', 'hilo' => '#E9EAF2',
        'bg' => ['#F3F4FA', '#E2E5F2'], 'vers' => '«Jehová es mi pastor; nada me faltará» — Salmo 23:1'],
    'beige' => ['copa' => '#E9E1D2', 'visera' => '#D6C9B2', 'hilo' => '#8A7A5C',
        'bg' => ['#FBF7EF', '#F0E7D8'], 'vers' => '«Todo lo puedo en Cristo que me fortalece» — Filipenses 4:13'],
    'blanca' => ['copa' => '#FFFFFF', 'visera' => '#EDEDF5', 'hilo' => '#5B4BDB',
        'bg' => ['#EEF1FB', '#DFE4F5'], 'vers' => '«El Señor es mi luz y mi salvación» — Salmo 27:1'],
    'azul' => ['copa' => '#3B4A8C', 'visera' => '#2C376B', 'hilo' => '#E9EAF2',
        'bg' => ['#EDF0FA', '#DDE2F2'], 'vers' => '«Esfuérzate y sé valiente» — Josué 1:9'],
];

foreach ($gorras as $color => $d) {
    [$bg1, $bg2] = $d['bg'];

    // Vista frontal.
    $artes["gorra-loto-{$color}"] = lienzo(
        gorra(400, 430, 1.28, $d['copa'], $d['visera']).versiculo($d['vers'], 700),
        $bg1, $bg2
    );

    // Vista de perfil.
    $artes["gorra-loto-{$color}-perfil"] = lienzo(
        gorraPerfil(400, 430, 1.15, $d['copa'], $d['visera']).versiculo($d['vers'], 700),
        $bg1, $bg2
    );

    // Detalle del bordado.
    $artes["gorra-loto-{$color}-bordado"] = lienzo(
        gorraBordado(400, 400, 1.0, $d['copa'], $d['hilo']).versiculo($d['vers'], 730),
        $bg1, $bg2
    );
}

/* ------------------------------------------------------------------ */
/* ESCRIBIR ARCHIVOS                                                   */
/* ------------------------------------------------------------------ */

// Limpia los SVG anteriores para no dejar arte obsoleto.
foreach (glob("{$destino}/*.svg") as $viejo) {
    @unlink($viejo);
}

$escritos = 0;
foreach ($artes as $nombre => $svg) {
    file_put_contents("{$destino}/{$nombre}.svg", $svg);
    $escritos++;
}

echo "Artes generadas: {$escritos} en {$destino}\n";
