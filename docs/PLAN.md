# CampusShop — Plan de trabajo

Tienda virtual para los emprendimientos de estudiantes universitarios.
Stack: **Laravel 12 · PHP 8.2+ · MySQL · Breeze (Blade) · Bootstrap 5 + Vite · Chart.js**.

Fuentes de verdad:

- `docs/SRS.md` — especificación funcional (RF-01 a RF-57, RNF, RN-01 a RN-11, rutas, modelo de datos, plan de pruebas). **Manda sobre las capturas.**
- `docs/design/` — maquetas HTML (`code.html`) y capturas (`screen.png`) de Inicio, Catálogo, Detalle de producto y Mi tienda, más `DESIGN.md` con el design system.

## Ramas

- `main` — versión estable / entregable.
- `develop` — integración.
- `feat/fase-N-...` — una rama por fase; al terminar se fusiona a `develop`.

## Estado

| Fase | Descripción | Estado |
|---|---|---|
| 0 | Base: Laravel 12, Breeze, Bootstrap, Vite, config, layouts, componentes, helper `cop()` | ✅ completada |
| 1 | Acceso y perfil (RF-01 a RF-08) | ✅ completada |
| 2 | Modelo de datos: 12 tablas, modelos, factories, seeders | ✅ completada |
| 3 | Emprendimientos (RF-09 a RF-14, RN-02) | ✅ completada |
| 4 | Productos y servicios (RF-15 a RF-22, RN-03, RN-10) | ✅ completada |
| 5 | Catálogo y home (RF-23 a RF-28, RN-09, RN-12) | ✅ completada |
| 6 | Pedidos (RF-35 a RF-44, RN-06) | ✅ completada |
| 7 | Chat (RF-29 a RF-34, RN-05) | ✅ completada |
| 8 | Reseñas (RF-45, RF-46, RN-07) | ✅ completada |
| 9 | Métricas y reconocimiento (RF-47 a RF-49, RN-08, RN-09) | ✅ completada |
| 10 | Moderación y administración (RF-50 a RF-57, RN-11) | ✅ completada |
| 11 | Seguridad y calidad (Policies, tests RN-01 a RN-11) | ✅ completada (P01–P30) |
| 12 | Entrega: README, credenciales demo, docs | ⬜ pendiente |

Leyenda: ⬜ pendiente · 🟡 en curso · ✅ completada · 🧪 migrada y con tests en verde

## Flujo por fase

1. Implementar el código de la fase.
2. `php artisan migrate:fresh --seed`.
3. `php artisan test` (los tests de la fase pasan).
4. Commit con mensaje claro (`feat: fase N ...`).
5. Fusionar `feat/fase-N-...` a `develop` y marcar la fase ✅.

## Convenciones

- Idioma: español. Moneda COP con el helper `cop()` (formato `$12.000`).
- Zona horaria `America/Bogota`, locale `es`.
- Lógica crítica en `app/Services` (`PedidoService`, `MetricasService`).
- Autorización con Policies; validación con Form Requests.
- Eliminación lógica (`SoftDeletes`) en usuarios, emprendimientos y productos.
- Componentes Blade reutilizables `<x-...>` y variables CSS sobre Bootstrap.

## Decisiones abiertas

Se registran en `docs/DECISIONES.md` conforme aparezcan.
