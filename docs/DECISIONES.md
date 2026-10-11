# Decisiones de diseño y aclaraciones

Registro de decisiones tomadas cuando el SRS es ambiguo o cuando hubo que elegir la
opción más sencilla coherente con él (regla de trabajo: no inventar requerimientos).

## D-01 — Bootstrap 5 en lugar de Tailwind

Las maquetas de `docs/design/` están hechas con Tailwind (CDN). El SRS y el
enunciado exigen **Blade + Bootstrap 5**. Se replica el diseño con Bootstrap,
variables CSS del design system (`docs/design/campus_commerce_experience/DESIGN.md`)
y componentes Blade propios.

## D-02 — Iconografía

Las maquetas usan *Material Symbols Outlined*. Se mantiene esa fuente vía Google
Fonts para no romper la fidelidad visual, sin dependencia JS.

## D-03 — Elementos decorativos de las capturas fuera del SRS

Según el enunciado, **no se implementan**: descuento tachado, horarios y puntos de
entrega, alérgenos, políticas de entrega, «Ver reporte detallado» ni la notificación
flotante de nuevo pedido. Donde aportan contexto se dejan como texto estático
opcional; el aviso de pedido nuevo será una alerta simple al cargar la página.

## D-04 — Estados de pedido

Solo `pendiente`, `confirmado`, `entregado`, `rechazado` y `cancelado`.
No existe «En preparación».

## D-05 — Carrito en sesión

El carrito vive en la sesión (no hay tabla extra), tal como indica el SRS (8.1).

## D-06 — Imágenes placeholder

Los seeders usan imágenes placeholder locales generadas (sin depender de la red).

## D-07 — Dominio universitario de desarrollo

`UNIVERSITY_EMAIL_DOMAIN=unimisionpaz.edu.co` como valor por defecto en `.env.example`,
configurable. Los seeders crean correos con ese dominio.

## D-08 — Una conversación por pareja (emprendimiento, comprador)

Restricción `UNIQUE (emprendimiento_id, comprador_id)` y `firstOrCreate`.

## D-09 — Visitas

Se registra como máximo una visita por sesión de navegador, página y día (RN-08),
usando un hash de la sesión en `visitas.sesion` + `visitas.fecha`.
