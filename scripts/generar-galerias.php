<?php

/**
 * Imágenes secundarias (galería) para los productos de CampusShop.
 * Cada arte muestra un "ángulo" distinto del mismo producto para que la
 * galería del detalle tenga varias miniaturas.
 *
 * Uso:  php scripts/generar-galerias.php
 */

$destino = __DIR__.'/../public/img/artes';
@mkdir($destino, 0775, true);

function marco2(string $contenido, string $desde, string $hasta, string $etiqueta): string
{
    $e = htmlspecialchars($etiqueta, ENT_QUOTES);

    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="800" viewBox="0 0 800 800">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="{$desde}"/>
      <stop offset="1" stop-color="{$hasta}"/>
    </linearGradient>
    <filter id="sh" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="14" stdDeviation="18" flood-color="#2A2350" flood-opacity="0.18"/>
    </filter>
  </defs>
  <rect width="800" height="800" fill="url(#bg)"/>
  <circle cx="690" cy="120" r="140" fill="#FFFFFF" opacity="0.3"/>
  <circle cx="110" cy="690" r="160" fill="#FFFFFF" opacity="0.22"/>
  <g filter="url(#sh)">
{$contenido}
  </g>
  <text x="400" y="760" text-anchor="middle" font-family="Inter, sans-serif"
        font-size="30" font-weight="600" fill="#5B4BDB" opacity="0.75">{$e}</text>
</svg>
SVG;
}

/** Flor de loto simplificada para vistas de detalle. */
function loto2(float $cx, float $cy, float $esc, string $petalo, string $trazo): string
{
    $p = [];
    foreach ([[0, -1, 1.0], [-0.62, -0.62, 0.86], [0.62, -0.62, 0.86], [-1.05, 0.1, 0.66], [1.05, 0.1, 0.66]] as [$dx, $dy, $e]) {
        $x = $cx + $dx * 46 * $esc;
        $y = $cy + $dy * 46 * $esc;
        $p[] = sprintf(
            '<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f" transform="rotate(%.1f %.1f %.1f)" fill="%s" stroke="%s" stroke-width="%.1f"/>',
            $x, $y, 34 * $esc * $e, 78 * $esc * $e, $dx * 26, $x, $y, $petalo, $trazo, 3.4 * $esc
        );
    }
    $p[] = sprintf('<circle cx="%.1f" cy="%.1f" r="%.1f" fill="#FF8A3D" stroke="%s" stroke-width="%.1f"/>', $cx, $cy + 20 * $esc, 16 * $esc, $trazo, 3.4 * $esc);

    return implode("\n  ", $p);
}

/** Gorra vista de perfil (solo lateral). */
function gorraPerfil(float $cx, float $cy, float $esc, string $copa, string $visera, bool $conLoto): string
{
    $x = $cx; $y = $cy; $s = $esc;
    $g = [];
    $g[] = sprintf('<path d="M%.1f %.1f a%.1f %.1f 0 0 1 %.1f 0 l0 %.1f a%.1f %.1f 0 0 1 -%.1f 0 z" fill="%s"/>',
        $x - 150 * $s, $y, 150 * $s, 165 * $s, 300 * $s, 92 * $s, 165 * $s, 32 * $s, 300 * $s, $copa);
    $g[] = sprintf('<path d="M%.1f %.1f q%.1f %.1f %.1f %.1f l%.1f %.1f q-%.1f -%.1f -%.1f -%.1f z" fill="%s"/>',
        $x - 330 * $s, $y + 94 * $s, 130 * $s, 42 * $s, 330 * $s, 42 * $s, 0, 0, 330 * $s, 42 * $s, 330 * $s, -42 * $s, $visera);
    $g[] = sprintf('<path d="M%.1f %.1f q0 -%.1f %.1f -%.1f" stroke="#00000022" stroke-width="%.1f" fill="none"/>',
        $x, $y + 92 * $s, 155 * $s, 0, -155 * $s, 6 * $s);
    $g[] = sprintf('<circle cx="%.1f" cy="%.1f" r="%.1f" fill="%s"/>', $x, $y - 155 * $s, 12 * $s, $visera);
    if ($conLoto) {
        $g[] = loto2($x, $y - 32 * $s, 0.6 * $s, '#FFFFFF', $copa === '#FFFFFF' ? '#5B4BDB' : 'transparent');
    }

    return implode("\n  ", $g);
}

$artes = [];

/* Gorras: perfil + detalle del bordado. */
$colores = [
    'gora-loto-negra' => ['#2C2C3A', '#1F1F2A', '#F3F4FA', '#E2E5F2', 'Negra'],
    'gora-loto-beige' => ['#E9E1D2', '#D6C9B2', '#FBF7EF', '#F0E7D8', 'Beige'],
    'gora-loto-blanca' => ['#FFFFFF', '#EDEDF5', '#EEF1FB', '#DFE4F5', 'Blanca'],
    'gora-loto-azul' => ['#3B4A8C', '#2C376B', '#EDF0FA', '#DDE2F2', 'Azul índigo'],
];
foreach ($colores as $clave => [$copa, $visera, $d, $h, $nombre]) {
    $artes["{$clave}-perfil"] = marco2(
        gorraPerfil(400, 400, 1.3, $copa, $visera, false),
        $d, $h, "Gorra Loto {$nombre} · perfil"
    );
    $artes["{$clave}-bordado"] = marco2(
        '<circle cx="400" cy="380" r="250" fill="'.$copa.'" opacity="0.9"/>'."\n  "
        .loto2(400, 340, 2.2, $copa === '#FFFFFF' ? '#5B4BDB' : '#FFFFFF', $copa === '#FFFFFF' ? '#FFFFFF' : 'transparent'),
        $d, $h, "Bordado flor de loto"
    );
}

/* Repostería: cortes y detalles. */
$artes['torta-chocolate-corte'] = marco2(<<<SVG
  <ellipse cx="400" cy="640" rx="250" ry="40" fill="#3A2A22" opacity="0.22"/>
  <path d="M400 300 L400 630 L180 630 z" fill="#8A5636"/>
  <path d="M400 300 L400 630 L620 630 z" fill="#6B4226"/>
  <rect x="180" y="300" width="440" height="40" rx="16" fill="#5A3520"/>
  <path d="M400 340 L400 630" stroke="#F3E0CF" stroke-width="10" opacity="0.5"/>
  <path d="M210 470 q190 60 380 0" stroke="#F3E0CF" stroke-width="18" fill="none"/>
  <path d="M210 550 q190 60 380 0" stroke="#F3E0CF" stroke-width="18" fill="none"/>
  <circle cx="240" cy="600" r="14" fill="#E23B3B"/>
  <circle cx="320" cy="620" r="14" fill="#E23B3B"/>
SVG, '#FFF3EA', '#F6DFD2', 'Corte interior');

$artes['torta-chocolate-personalizada'] = marco2(<<<SVG
  <ellipse cx="400" cy="650" rx="240" ry="38" fill="#3A2A22" opacity="0.2"/>
  <rect x="190" y="340" width="420" height="270" rx="24" fill="#6B4226"/>
  <rect x="190" y="340" width="420" height="60" rx="24" fill="#8A5636"/>
  <rect x="170" y="610" width="460" height="34" rx="16" fill="#C8A27A"/>
  <text x="400" y="510" text-anchor="middle" font-family="Georgia, serif" font-size="70" font-style="italic" fill="#F3E0CF">Feliz día</text>
  <path d="M300 480 q50 -60 100 0 q50 -60 100 0" stroke="#F3E0CF" stroke-width="8" fill="none"/>
SVG, '#FFF3EA', '#F6DFD2', 'Personalizada');

$artes['cupcakes-variedad'] = marco2(<<<SVG
  <path d="M120 500 l30 150 a16 16 0 0 0 16 12 h120 a16 16 0 0 0 16 -12 l30 -150 z" fill="#D9A15B"/>
  <path d="M145 500 q0 -90 75 -90 q26 -56 100 -30 q56 -28 96 30 q52 16 45 90 z" fill="#FADCBF"/>
  <path d="M340 470 l30 170 a16 16 0 0 0 16 12 h168 a16 16 0 0 0 16 -12 l30 -170 z" fill="#C08A46"/>
  <path d="M370 470 q0 -100 92 -100 q30 -64 118 -36 q66 -32 112 30 q60 18 52 106 z" fill="#C8B6F0"/>
  <circle cx="230" cy="400" r="24" fill="#E23B3B"/>
  <circle cx="500" cy="368" r="26" fill="#F6C9D6"/>
  <path d="M500 368 q-6 -38 -30 -54" stroke="#5E8B4A" stroke-width="7" fill="none" stroke-linecap="round"/>
SVG, '#FFF1F5', '#FBD9E4', 'Varios sabores');

$artes['cheesecake-porcion'] = marco2(<<<SVG
  <ellipse cx="400" cy="620" rx="220" ry="40" fill="#3A2A22" opacity="0.16"/>
  <path d="M260 380 L540 380 L470 610 L330 610 z" fill="#F6E0B8"/>
  <path d="M260 380 L540 380 L520 440 L280 440 z" fill="#FFB03A"/>
  <ellipse cx="400" cy="380" rx="140" ry="24" fill="#FFF3D8"/>
  <path d="M330 500 q70 34 140 0" stroke="#FF8A3D" stroke-width="12" fill="none"/>
  <circle cx="400" cy="360" r="16" fill="#FF8A3D"/>
  <path d="M170 520 q40 -80 100 -90" stroke="#FFD36E" stroke-width="10" fill="none" stroke-linecap="round"/>
SVG, '#FFF8EA', '#F7E9D6', 'Porción individual');

$artes['alfajores-caja'] = marco2(<<<SVG
  <rect x="180" y="330" width="440" height="270" rx="20" fill="#FBF3E2" stroke="#D9A15B" stroke-width="6"/>
  <ellipse cx="400" cy="330" rx="220" ry="60" fill="#F3E3C8"/>
  <g transform="translate(250,360)">
    <ellipse cx="70" cy="60" rx="70" ry="22" fill="#E8CB97"/>
    <rect x="0" y="40" width="140" height="26" fill="#FBF3E2"/>
    <ellipse cx="70" cy="40" rx="70" ry="22" fill="#E8CB97"/>
    <path d="M0 60 q70 30 140 0" stroke="#7A5230" stroke-width="5" fill="none" opacity="0.4"/>
  </g>
  <g transform="translate(400,400)">
    <ellipse cx="70" cy="60" rx="70" ry="22" fill="#E8CB97"/>
    <rect x="0" y="40" width="140" height="26" fill="#FBF3E2"/>
    <ellipse cx="70" cy="40" rx="70" ry="22" fill="#E8CB97"/>
    <path d="M0 60 q70 30 140 0" stroke="#7A5230" stroke-width="5" fill="none" opacity="0.4"/>
  </g>
  <text x="400" y="270" text-anchor="middle" font-family="Georgia, serif" font-size="44" font-style="italic" fill="#8A5636">Dulce Campus</text>
SVG, '#FDF7EA', '#F2E6CE', 'Caja x6');

/* Servicios: detalles adicionales. */
$artes['tutoria-calculo-pizarra'] = marco2(<<<SVG
  <rect x="130" y="150" width="540" height="460" rx="22" fill="#1F3A2E"/>
  <rect x="130" y="150" width="540" height="460" rx="22" fill="none" stroke="#8A6E4B" stroke-width="16"/>
  <text x="200" y="270" font-family="Cambria Math, Georgia, serif" font-size="60" fill="#F1F5F9">d/dx [ x² ] = 2x</text>
  <text x="200" y="370" font-family="Cambria Math, Georgia, serif" font-size="60" fill="#F1F5F9">∫ 2x dx = x² + C</text>
  <path d="M200 450 q80 -160 160 -70 q80 90 190 -50" stroke="#FF8A3D" stroke-width="8" fill="none" stroke-linecap="round"/>
  <text x="200" y="560" font-family="Cambria Math, Georgia, serif" font-size="44" fill="#94A3B8">Repaso de temas</text>
SVG, '#EEF7F1', '#DCEBE2', 'Pizarra de clase');

$artes['tutoria-programacion-consola'] = marco2(<<<SVG
  <rect x="150" y="180" width="500" height="440" rx="22" fill="#0B1220"/>
  <text x="195" y="260" font-family="Consolas, monospace" font-size="36" fill="#4ADE80">$ python tutor.py</text>
  <text x="195" y="320" font-family="Consolas, monospace" font-size="34" fill="#7DD3FC">def suma(a, b):</text>
  <text x="235" y="370" font-family="Consolas, monospace" font-size="34" fill="#E2E8F0">return a + b</text>
  <text x="195" y="440" font-family="Consolas, monospace" font-size="36" fill="#4ADE80">&gt;&gt;&gt; suma(2, 3)</text>
  <text x="195" y="500" font-family="Consolas, monospace" font-size="36" fill="#FF8A3D">5</text>
  <rect x="195" y="540" width="26" height="40" fill="#4ADE80"/>
SVG, '#E8F0FF', '#D3E0FF', 'En vivo');

$artes['preparacion-parciales-simulacro'] = marco2(<<<SVG
  <rect x="200" y="160" width="400" height="480" rx="16" fill="#FFFDF7" stroke="#E2D8C3" stroke-width="4"/>
  <text x="400" y="240" text-anchor="middle" font-family="Inter, sans-serif" font-size="40" font-weight="700" fill="#1E293B">Simulacro</text>
  <line x1="260" y1="300" x2="540" y2="300" stroke="#CBD5E1" stroke-width="6" stroke-linecap="round"/>
  <line x1="260" y1="350" x2="540" y2="350" stroke="#CBD5E1" stroke-width="6" stroke-linecap="round"/>
  <line x1="260" y1="400" x2="440" y2="400" stroke="#CBD5E1" stroke-width="6" stroke-linecap="round"/>
  <circle cx="560" cy="200" r="52" fill="#FF8A3D"/>
  <path d="M560 172 v30 l20 14" stroke="#FFF" stroke-width="7" fill="none" stroke-linecap="round"/>
  <path d="M290 480 l48 48 l110 -120" stroke="#22C55E" stroke-width="16" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
SVG, '#FFFBF0', '#F3EBD8', 'Prueba cronometrada');

$artes['web-basica-responsive'] = marco2(<<<SVG
  <rect x="150" y="200" width="380" height="300" rx="18" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="4"/>
  <rect x="150" y="200" width="380" height="46" rx="18" fill="#5B4BDB"/>
  <rect x="180" y="290" width="150" height="20" rx="10" fill="#5B4BDB"/>
  <rect x="180" y="330" width="320" height="14" rx="7" fill="#CBD5E1"/>
  <rect x="180" y="360" width="280" height="14" rx="7" fill="#CBD5E1"/>
  <rect x="180" y="400" width="120" height="40" rx="10" fill="#FF8A3D"/>
  <rect x="560" y="180" width="150" height="330" rx="26" fill="#0F172A"/>
  <rect x="580" y="220" width="110" height="20" rx="10" fill="#7DD3FC"/>
  <rect x="580" y="260" width="110" height="12" rx="6" fill="#475569"/>
  <rect x="580" y="290" width="80" height="12" rx="6" fill="#475569"/>
  <rect x="580" y="340" width="110" height="50" rx="12" fill="#FF8A3D"/>
SVG, '#EFF3FF', '#DCE4FA', 'Escritorio y móvil');

$artes['correccion-codigo-informe'] = marco2(<<<SVG
  <rect x="180" y="150" width="440" height="500" rx="18" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="4"/>
  <rect x="180" y="150" width="440" height="70" rx="18" fill="#22C55E"/>
  <text x="240" y="198" font-family="Inter, sans-serif" font-size="34" font-weight="700" fill="#FFFFFF">Informe de revisión</text>
  <circle cx="240" cy="290" r="14" fill="#22C55E"/>
  <rect x="280" y="280" width="280" height="16" rx="8" fill="#CBD5E1"/>
  <circle cx="240" cy="350" r="14" fill="#FF8A3D"/>
  <rect x="280" y="340" width="240" height="16" rx="8" fill="#CBD5E1"/>
  <circle cx="240" cy="410" r="14" fill="#EF4444"/>
  <rect x="280" y="400" width="200" height="16" rx="8" fill="#CBD5E1"/>
  <rect x="240" y="470" width="320" height="14" rx="7" fill="#E2E8F0"/>
  <rect x="240" y="510" width="360" height="14" rx="7" fill="#E2E8F0"/>
  <rect x="240" y="550" width="260" height="14" rx="7" fill="#E2E8F0"/>
SVG, '#EEFBF4', '#D9F2E4', 'Reporte escrito');

$artes['soporte-tecnico-diagnostico'] = marco2(<<<SVG
  <rect x="200" y="220" width="400" height="260" rx="18" fill="#334155"/>
  <rect x="230" y="250" width="340" height="200" rx="10" fill="#0F172A"/>
  <path d="M300 320 q40 -50 100 -10 q60 40 110 -20" stroke="#FF8A3D" stroke-width="10" fill="none" stroke-linecap="round"/>
  <path d="M510 330 l30 30 l-30 30" stroke="#4ADE80" stroke-width="12" fill="none" stroke-linecap="round"/>
  <rect x="330" y="480" width="140" height="36" rx="10" fill="#334155"/>
  <rect x="270" y="516" width="260" height="24" rx="12" fill="#475569"/>
  <text x="400" y="620" text-anchor="middle" font-family="Inter, sans-serif" font-size="40" font-weight="700" fill="#1E293B">Diagnóstico</text>
SVG, '#EEF4FB', '#DBE7F5', 'Revisión completa');

$escritos = 0;
foreach ($artes as $nombre => $svg) {
    file_put_contents("{$destino}/{$nombre}.svg", $svg);
    $escritos++;
}

echo "Galerías generadas: {$escritos} en {$destino}\n";
