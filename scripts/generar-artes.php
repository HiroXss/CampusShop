<?php

/**
 * Generador de artes SVG para CampusShop.
 *
 * Crea imágenes (productos y logos de emprendimiento) con el look del design
 * system de la app (azul #5B4BDB, acento #FF8A3D). Cada arte es un SVG cuadrado
 * de 800x800 pensado para verse bien recortado en las tarjetas del catálogo.
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

$artes = [];

/* ------------------------------------------------------------------ */
/* DULCE CAMPUS — repostería                                           */
/* ------------------------------------------------------------------ */

$artes['torta-chocolate'] = lienzo(<<<SVG
  <ellipse cx="400" cy="640" rx="250" ry="42" fill="#3A2A22" opacity="0.25"/>
  <rect x="170" y="330" width="460" height="290" rx="26" fill="#6B4226"/>
  <rect x="170" y="330" width="460" height="70" rx="26" fill="#8A5636"/>
  <path d="M170 470 q60 46 120 0 q60 -46 120 0 q60 46 120 0 q30 -23 60 -8 l0 30 q-30 -15 -60 8 q-60 46 -120 0 q-60 -46 -120 0 q-60 46 -120 0 z" fill="#F3E0CF"/>
  <rect x="150" y="600" width="500" height="40" rx="18" fill="#C8A27A"/>
  <circle cx="290" cy="300" r="34" fill="#E23B3B"/>
  <circle cx="400" cy="278" r="38" fill="#E23B3B"/>
  <circle cx="510" cy="300" r="34" fill="#E23B3B"/>
  <circle cx="290" cy="288" r="10" fill="#FF6B6B"/>
  <circle cx="400" cy="264" r="12" fill="#FF6B6B"/>
  <circle cx="510" cy="288" r="10" fill="#FF6B6B"/>
SVG);

$artes['cupcakes'] = lienzo(<<<SVG
  <ellipse cx="400" cy="660" rx="250" ry="38" fill="#3A2A22" opacity="0.2"/>
  <path d="M170 470 l40 170 a18 18 0 0 0 18 14 h264 a18 18 0 0 0 18 -14 l40 -170 z" fill="#D9A15B"/>
  <path d="M210 470 l-8 36 h316 l-8 -36 z" fill="#C08A46"/>
  <path d="M200 470 q0 -110 100 -110 q30 -70 130 -40 q80 -40 130 30 q70 20 60 120 z" fill="#F6C9D6"/>
  <path d="M215 445 q10 -70 95 -75 q40 -52 120 -25 q70 -30 110 40 q45 16 40 60 z" fill="#FAD9E3"/>
  <circle cx="400" cy="330" r="26" fill="#E23B3B"/>
  <circle cx="400" cy="316" r="7" fill="#FF7B7B"/>
  <path d="M400 330 q-6 -40 -30 -58" stroke="#5E8B4A" stroke-width="7" fill="none" stroke-linecap="round"/>
  <circle cx="560" cy="470" r="18" fill="#8B5E34"/>
  <circle cx="240" cy="470" r="18" fill="#8B5E34"/>
SVG);

$artes['cheesecake'] = lienzo(<<<SVG
  <ellipse cx="400" cy="648" rx="240" ry="40" fill="#3A2A22" opacity="0.18"/>
  <path d="M200 400 h400 l-30 240 a20 20 0 0 1 -20 18 h-300 a20 20 0 0 1 -20 -18 z" fill="#F6E0B8"/>
  <path d="M200 400 h400 l-14 44 h-372 z" fill="#E8CB97"/>
  <path d="M210 470 q50 30 100 0 q50 -30 100 0 q50 30 100 0 q40 -24 90 -6 l-6 34 q-50 -18 -90 6 q-50 30 -100 0 q-50 -30 -100 0 q-50 30 -100 0 z" fill="#FFB03A"/>
  <ellipse cx="400" cy="400" rx="200" ry="34" fill="#FFF3D8"/>
  <circle cx="330" cy="392" r="12" fill="#FFB03A"/>
  <circle cx="400" cy="386" r="14" fill="#FF8A3D"/>
  <circle cx="470" cy="392" r="12" fill="#FFB03A"/>
  <path d="M300 250 q40 -60 100 -60 q60 0 100 60 q-50 -20 -100 -20 q-50 0 -100 20 z" fill="#FFD36E"/>
SVG);

$artes['alfajores'] = lienzo(<<<SVG
  <ellipse cx="400" cy="650" rx="240" ry="36" fill="#3A2A22" opacity="0.18"/>
  <g transform="translate(150,380)">
    <ellipse cx="120" cy="180" rx="110" ry="34" fill="#D9A15B"/>
    <ellipse cx="120" cy="140" rx="110" ry="34" fill="#FBF3E2"/>
    <rect x="10" y="140" width="220" height="40" fill="#FBF3E2"/>
    <ellipse cx="120" cy="140" rx="110" ry="34" fill="#C98A4B"/>
    <ellipse cx="120" cy="96" rx="110" ry="34" fill="#FBF3E2"/>
    <rect x="10" y="60" width="220" height="40" fill="#FBF3E2"/>
    <ellipse cx="120" cy="60" rx="110" ry="34" fill="#E8CB97"/>
    <path d="M10 140 q110 44 220 0" stroke="#7A5230" stroke-width="6" fill="none" opacity="0.4"/>
  </g>
  <g transform="translate(400,320)">
    <ellipse cx="120" cy="180" rx="110" ry="34" fill="#D9A15B"/>
    <ellipse cx="120" cy="140" rx="110" ry="34" fill="#FBF3E2"/>
    <rect x="10" y="140" width="220" height="40" fill="#FBF3E2"/>
    <ellipse cx="120" cy="140" rx="110" ry="34" fill="#C98A4B"/>
    <ellipse cx="120" cy="96" rx="110" ry="34" fill="#FBF3E2"/>
    <rect x="10" y="60" width="220" height="40" fill="#FBF3E2"/>
    <ellipse cx="120" cy="60" rx="110" ry="34" fill="#E8CB97"/>
    <path d="M10 140 q110 44 220 0" stroke="#7A5230" stroke-width="6" fill="none" opacity="0.4"/>
  </g>
SVG);

/* ------------------------------------------------------------------ */
/* LOTHUS — gorras minimalistas con flor de loto y versículos          */
/* ------------------------------------------------------------------ */

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
    foreach ($petalos as $i => [$dx, $dy, $esc]) {
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

$artes['gora-loto-negra'] = lienzo(gorra(400, 430, 1.28, '#2C2C3A', '#1F1F2A').versiculo('«Jehová es mi pastor; nada me faltará» — Salmo 23:1', 700), '#F3F4FA', '#E2E5F2');

$artes['gora-loto-beige'] = lienzo(gorra(400, 430, 1.28, '#E9E1D2', '#D6C9B2').versiculo('«Todo lo puedo en Cristo que me fortalece» — Filipenses 4:13', 700), '#FBF7EF', '#F0E7D8');

$artes['gora-loto-blanca'] = lienzo(gorra(400, 430, 1.28, '#FFFFFF', '#EDEDF5').versiculo('«El Señor es mi luz y mi salvación» — Salmo 27:1', 700), '#EEF1FB', '#DFE4F5');

$artes['gora-loto-azul'] = lienzo(gorra(400, 430, 1.28, '#3B4A8C', '#2C376B').versiculo('«Esfuérzate y sé valiente» — Josué 1:9', 700), '#EDF0FA', '#DDE2F2');

/* ------------------------------------------------------------------ */
/* TUTORÍAS PRO — servicios académicos                                 */
/* ------------------------------------------------------------------ */

$artes['tutoria-calculo'] = lienzo(<<<SVG
  <rect x="150" y="150" width="500" height="500" rx="28" fill="#1E293B"/>
  <rect x="180" y="180" width="440" height="440" rx="18" fill="#0F172A" stroke="#334155" stroke-width="4"/>
  <text x="230" y="300" font-family="Cambria Math, Georgia, serif" font-size="90" fill="#7DD3FC">∫</text>
  <text x="300" y="300" font-family="Cambria Math, Georgia, serif" font-size="66" fill="#E2E8F0">f(x) dx</text>
  <path d="M240 460 q60 -140 120 -60 q60 80 130 -40" stroke="#FF8A3D" stroke-width="8" fill="none" stroke-linecap="round"/>
  <line x1="230" y1="520" x2="570" y2="520" stroke="#475569" stroke-width="5"/>
  <line x1="230" y1="520" x2="230" y2="370" stroke="#475569" stroke-width="5"/>
  <text x="250" y="580" font-family="Cambria Math, Georgia, serif" font-size="44" fill="#94A3B8">y = f(x)</text>
  <path d="M560 480 l50 40 l-50 40 z" fill="#FF8A3D"/>
SVG);

$artes['tutoria-programacion'] = lienzo(<<<SVG
  <rect x="140" y="170" width="520" height="440" rx="26" fill="#1E293B"/>
  <rect x="140" y="170" width="520" height="70" rx="26" fill="#334155"/>
  <circle cx="190" cy="205" r="13" fill="#FF5F57"/>
  <circle cx="232" cy="205" r="13" fill="#FEBC2E"/>
  <circle cx="274" cy="205" r="13" fill="#28C840"/>
  <rect x="180" y="280" width="240" height="18" rx="9" fill="#7DD3FC"/>
  <rect x="220" y="320" width="330" height="18" rx="9" fill="#FF8A3D"/>
  <rect x="220" y="360" width="200" height="18" rx="9" fill="#A7F3D0"/>
  <rect x="260" y="400" width="290" height="18" rx="9" fill="#C4B5FD"/>
  <rect x="220" y="440" width="180" height="18" rx="9" fill="#7DD3FC"/>
  <rect x="180" y="480" width="300" height="18" rx="9" fill="#FDA4AF"/>
  <rect x="180" y="520" width="120" height="18" rx="9" fill="#FDE68A"/>
  <text x="470" y="560" font-family="Consolas, monospace" font-size="54" fill="#5B4BDB" font-weight="bold">{ }</text>
SVG);

$artes['preparacion-parciales'] = lienzo(<<<SVG
  <rect x="200" y="150" width="360" height="500" rx="20" fill="#FFFDF7" stroke="#E2D8C3" stroke-width="4"/>
  <rect x="330" y="150" width="100" height="60" rx="12" fill="#D6C9B2"/>
  <line x1="260" y1="300" x2="500" y2="300" stroke="#CBD5E1" stroke-width="8" stroke-linecap="round"/>
  <line x1="260" y1="350" x2="500" y2="350" stroke="#CBD5E1" stroke-width="8" stroke-linecap="round"/>
  <line x1="260" y1="400" x2="430" y2="400" stroke="#CBD5E1" stroke-width="8" stroke-linecap="round"/>
  <path d="M300 500 l60 60 l140 -150" stroke="#22C55E" stroke-width="18" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
  <circle cx="560" cy="230" r="54" fill="#FF8A3D"/>
  <path d="M560 200 v34 l24 16" stroke="#FFFFFF" stroke-width="8" fill="none" stroke-linecap="round"/>
  <text x="400" y="620" text-anchor="middle" font-family="Inter, sans-serif" font-size="40" font-weight="700" fill="#1E293B">A preparar</text>
SVG);

/* ------------------------------------------------------------------ */
/* CÓDIGO FÁCIL — servicios de software                                */
/* ------------------------------------------------------------------ */

$artes['web-basica'] = lienzo(<<<SVG
  <rect x="120" y="180" width="560" height="380" rx="24" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="4"/>
  <rect x="120" y="180" width="560" height="64" rx="24" fill="#5B4BDB"/>
  <circle cx="170" cy="212" r="11" fill="#FFFFFF" opacity="0.9"/>
  <circle cx="206" cy="212" r="11" fill="#FFFFFF" opacity="0.6"/>
  <circle cx="242" cy="212" r="11" fill="#FFFFFF" opacity="0.35"/>
  <rect x="160" y="290" width="200" height="24" rx="12" fill="#5B4BDB"/>
  <rect x="160" y="336" width="480" height="16" rx="8" fill="#CBD5E1"/>
  <rect x="160" y="368" width="420" height="16" rx="8" fill="#CBD5E1"/>
  <rect x="160" y="418" width="150" height="46" rx="12" fill="#FF8A3D"/>
  <rect x="380" y="290" width="260" height="150" rx="16" fill="#EEF0FF"/>
  <path d="M430 400 l60 -60 l50 40 l70 -80" stroke="#5B4BDB" stroke-width="8" fill="none" stroke-linecap="round"/>
  <rect x="160" y="500" width="140" height="30" rx="15" fill="#EEF0FF"/>
  <rect x="320" y="500" width="140" height="30" rx="15" fill="#EEF0FF"/>
SVG);

$artes['correccion-codigo'] = lienzo(<<<SVG
  <rect x="150" y="170" width="500" height="460" rx="24" fill="#0F172A"/>
  <text x="200" y="270" font-family="Consolas, monospace" font-size="42" fill="#7DD3FC">const app = ()=&gt; {</text>
  <text x="230" y="330" font-family="Consolas, monospace" font-size="42" fill="#FDA4AF">return  data.map(x =&gt; x.id)</text>
  <text x="200" y="390" font-family="Consolas, monospace" font-size="42" fill="#A7F3D0">}</text>
  <path d="M200 430 l0 0" stroke="#FF8A3D" stroke-width="6"/>
  <rect x="190" y="410" width="330" height="52" rx="8" fill="none" stroke="#FF8A3D" stroke-width="5" stroke-dasharray="14 10"/>
  <g transform="translate(520,430)">
    <circle r="60" fill="#FF8A3D"/>
    <path d="M-26 -6 l30 30 l-30 30" stroke="#FFFFFF" stroke-width="12" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
    <line x1="-30" y1="24" x2="-6" y2="24" stroke="#FFFFFF" stroke-width="12" stroke-linecap="round"/>
  </g>
  <text x="200" y="540" font-family="Consolas, monospace" font-size="38" fill="#22C55E">// corregido ✓</text>
SVG);

$artes['soporte-tecnico'] = lienzo(<<<SVG
  <rect x="180" y="200" width="440" height="300" rx="20" fill="#334155"/>
  <rect x="210" y="230" width="380" height="240" rx="12" fill="#0F172A"/>
  <path d="M300 330 l-50 40 l50 40 M500 330 l50 40 l-50 40" stroke="#7DD3FC" stroke-width="14" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
  <line x1="430" y1="300" x2="370" y2="440" stroke="#FF8A3D" stroke-width="14" stroke-linecap="round"/>
  <rect x="330" y="500" width="140" height="40" rx="12" fill="#334155"/>
  <rect x="270" y="540" width="260" height="26" rx="13" fill="#475569"/>
  <g transform="translate(600,300)">
    <circle r="70" fill="#FF8A3D"/>
    <path d="M-20 -30 a30 30 0 1 1 34 40 l0 18" stroke="#FFFFFF" stroke-width="12" fill="none" stroke-linecap="round"/>
    <circle cx="-3" cy="46" r="10" fill="#FFFFFF"/>
  </g>
SVG);

/* ------------------------------------------------------------------ */
/* ESCRIBIR ARCHIVOS                                                   */
/* ------------------------------------------------------------------ */

$escritos = 0;
foreach ($artes as $nombre => $svg) {
    file_put_contents("{$destino}/{$nombre}.svg", $svg);
    $escritos++;
}

echo "Artes generadas: {$escritos} en {$destino}\n";
