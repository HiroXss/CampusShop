# CampusShop

Tienda virtual de los **emprendimientos de estudiantes universitarios**. Los
estudiantes descubren productos y servicios, se comunican con los emprendedores por
chat, hacen pedidos, los califican y reportan contenido inapropiado. Los
emprendedores abren su tienda, publican lo que venden, atienden pedidos y miden su
crecimiento. Un administrador aprueba emprendimientos, gestiona categorías, modera
reportes y supervisa usuarios.

Este repositorio implementa el sistema descrito en `docs/SRS.md` (especificación
funcional) siguiendo el plan por fases de `docs/PLAN.md`.

## Stack

- **Laravel 12** + PHP 8.2+
- **MySQL** (Laragon)
- **Laravel Breeze** (Blade) con verificación de correo
- **Bootstrap 5** + **Vite** (Sass + JS), iconos **Bootstrap Icons**
- **Chart.js** para el panel de métricas del emprendedor

Zona horaria `America/Bogota`, locale `es`, moneda **COP** formateada con el helper
`cop()` (`$12.000`).

## Requisitos

- PHP 8.2 o superior con las extensiones habituales de Laravel.
- Composer.
- Node.js 18+ y npm.
- MySQL 8.

## Puesta en marcha

```bash
# 1. Dependencias
composer install
npm install

# 2. Entorno
cp .env.example .env          # en Windows: copy .env.example .env
php artisan key:generate

# 3. Base de datos: crea la base "campushop" en MySQL y ajusta .env si hace falta.
#    Luego migra y carga los datos de demostración:
php artisan migrate:fresh --seed

# 4. Enlace de almacenamiento (logos e imágenes de productos)
php artisan storage:link

# 5. Front-end
npm run build                 # o: npm run dev (desarrollo)

# 6. Servidor
php artisan serve
```

La aplicación queda disponible en `http://127.0.0.1:8000`.

### Correo en desarrollo

`.env.example` apunta a **Mailpit** en `127.0.0.1:1025` (interfaz web en
`http://127.0.0.1:8025`). En Laragon, Mailpit suele venir incluido. Si no lo usas,
puedes cambiar `MAIL_MAILER=log` para que los correos se escriban en
`storage/logs/laravel.log`.

## Credenciales de demostración

Todas las cuentas sembradas usan la contraseña **`password`** y el dominio
`@unimisionpaz.edu.co` (configurable con `UNIVERSITY_EMAIL_DOMAIN`).

| Rol | Correo | Uso |
|---|---|---|
| Administrador | `admin@unimisionpaz.edu.co` | Panel de administración |
| Estudiante (comprador) | `estudiante@unimisionpaz.edu.co` | Catálogo, carrito, pedidos, chat, reseñas |
| Emprendedor | `laura@unimisionpaz.edu.co` | Dulce Campus (Repostería) |
| Emprendedor | `carlos@unimisionpaz.edu.co` | Tutorías Pro (Tutorías) |
| Emprendedor | `maria@unimisionpaz.edu.co` | Estampados UMP (Ropa y accesorios) |
| Emprendedor | `andres@unimisionpaz.edu.co` | Manos Creativas (Arte y manualidades) |
| Emprendedor | `sofia@unimisionpaz.edu.co` | Código Fácil (Servicios académicos) |

El sembrador también crea un emprendimiento en estado **pendiente** («Snacks
Universitarios») para probar la cola de aprobación del administrador.

## Parámetros de negocio

Estos valores viven en `config/campushop.php` y se leen del `.env` (sección 8.5 del
SRS), de modo que nada queda fijo en el código:

| Variable | Por defecto | Descripción |
|---|---|---|
| `UNIVERSITY_EMAIL_DOMAIN` | `unimisionpaz.edu.co` | Dominio permitido en el registro (RN-01) |
| `REQUIRE_APPROVAL` | `true` | Exigir aprobación del admin a emprendimientos (RN-02) |
| `MAX_IMAGES_PER_PRODUCT` | `5` | Imágenes por producto (RN-10) |
| `MAX_IMAGE_SIZE_KB` | `2048` | Tamaño máximo por imagen (RN-10) |
| `REPORTS_THRESHOLD` | `3` | Reportes pendientes que ocultan un elemento (RN-11) |
| `CHAT_POLL_SECONDS` | `10` | Intervalo de refresco del chat (RF-33) |
| `PAGE_SIZE` | `12` | Elementos por página |
| `FEATURED_LIMIT` | `8` | Emprendimientos destacados en el inicio |
| `TOP_SELLING_DAYS` | `30` | Ventana de «más vendidos» |
| `MIN_REVIEWS_FOR_RANKING` | `3` | Reseñas mínimas para el ranking (RN-09) |
| `MAX_ORDERS_PER_HOUR` | `10` | Pedidos por usuario y hora (RNF-13) |
| `MAX_MESSAGES_PER_MINUTE` | `30` | Mensajes por usuario y minuto (RNF-13) |
| `MAX_LOGIN_ATTEMPTS` | `5` | Intentos de inicio de sesión por minuto (RNF-13) |

## Rutas principales

| Método | URI | Acceso |
|---|---|---|
| GET | `/` | Público — inicio con destacados y rankings |
| GET | `/productos`, `/productos/{id}` | Público — catálogo con filtros y detalle |
| GET | `/emprendimientos`, `/emprendimientos/{slug}` | Público — listado y página del emprendimiento |
| GET/POST | `/register`, `/login`, `/forgot-password` | Invitado (Breeze) |
| GET | `/email/verify/{id}/{hash}` | Correo firmado |
| GET/PATCH/DELETE | `/profile` | Autenticado — ver, editar y eliminar cuenta |
| GET/POST | `/vender` | Autenticado y verificado — activar emprendimiento |
| GET/PUT | `/mi-emprendimiento`, `/mi-emprendimiento/pausar`, `/mi-emprendimiento/reactivar` | Emprendedor |
| GET | `/mi-tienda`, `/mi-tienda/productos/...`, `/mi-tienda/pedidos`, `/mi-tienda/metricas` | Emprendedor |
| GET | `/carrito`, `/mis-pedidos`, `/pedidos/{id}`, `/conversaciones` | Autenticado |
| GET | `/admin`, `/admin/aprobacion`, `/admin/emprendimientos`, `/admin/categorias`, `/admin/reportes`, `/admin/usuarios` | Administrador |

## Pruebas

La suite cubre los **30 casos del plan de pruebas** del SRS (P01–P30), además de la
prueba de humo de todas las páginas y las pruebas de Breeze.

```bash
php artisan test
```

Las pruebas corren contra una base de datos independiente. Copia `.env.testing`
(ya incluido en `.gitignore`) apuntando a `campushop_test`:

```
APP_ENV=testing
DB_DATABASE=campushop_test
```

Crea la base `campushop_test` en MySQL; `php artisan test` aplica las migraciones
automáticamente.

| Archivo | Fase / casos |
|---|---|
| `tests/Feature/AccesoPerfilTest.php` | Fase 1 — P01–P04 |
| `tests/Feature/EmprendimientoTest.php` | Fase 3 — P05–P06 |
| `tests/Feature/ProductoTest.php` | Fase 4 — P07–P08 |
| `tests/Feature/CatalogoTest.php` | Fase 5 — P09–P10 |
| `tests/Feature/ChatTest.php` | Fase 7 — P11–P13 |
| `tests/Feature/PedidoTest.php` | Fase 6 — P14–P21 |
| `tests/Feature/ResenaTest.php` | Fase 8 — P22–P24 |
| `tests/Feature/MetricasTest.php` | Fase 9 — P25–P27 |
| `tests/Feature/ModeracionTest.php` | Fase 10 — P28–P30 |
| `tests/Feature/SmokeTest.php` | Prueba de humo de todas las páginas |

## Estructura del proyecto

```
app/
  Http/Controllers/       Controladores (público, comprador, Mi Tienda, Admin)
  Http/Requests/          Validación con Form Requests
  Http/Middleware/        EnsureHasEmprendimiento, EnsureUserIsAdmin
  Models/                 12 modelos Eloquent (SoftDeletes en users, emprendimientos, productos)
  Policies/               Autorización (RNF-05)
  Services/               PedidoService (pedidos/stock), MetricasService (visitas/rankings)
  helpers.php             cop(), carrito*()
config/campushop.php      Parámetros de negocio (RNF-11)
database/
  migrations/             12 tablas del dominio (+ cache, jobs y sesiones)
  factories/              Datos de prueba
  seeders/                Datos de demostración
docs/
  SRS.md                  Especificación funcional (fuente de verdad)
  PLAN.md                 Plan por fases y estado
  DECISIONES.md           Decisiones cuando el SRS es ambiguo
  design/                 Maquetas y design system de referencia
resources/
  views/                  Plantillas Blade (Bootstrap 5)
  sass/, js/              Estilos y comportamiento (Vite)
routes/web.php            Rutas de la aplicación
tests/Feature/            Pruebas por fase (P01–P30) y de humo
```

## Reglas de negocio destacadas

- **RN-01** Solo correos del dominio universitario pueden registrarse.
- **RN-02** Cada estudiante tiene un solo emprendimiento; para publicar debe estar aprobado.
- **RN-03** El catálogo muestra productos disponibles y agotados; el inactivo no aparece.
- **RN-04 / RN-05** Cada quien gestiona lo suyo: productos, pedidos y conversaciones.
- **RN-06** Un pedido es de un solo emprendimiento; precios y stock se validan en el servidor.
- **RN-07** Solo se reseña un pedido entregado, una vez, por su comprador.
- **RN-08 / RN-09** Métricas y rankings se calculan sobre hechos reales (entregas, reseñas mínimas).
- **RN-10** Imágenes `jpg/png/webp` de máximo 2 MB, hasta 5 por producto.
- **RN-11** Al alcanzar el umbral de reportes pendientes, el elemento se oculta.
- **RNF-13** Límites de pedidos, mensajes e intentos de acceso.
- **RNF-16** Operaciones de stock en transacciones con bloqueo para evitar la sobreventa.

## Documentación

- [`docs/SRS.md`](docs/SRS.md) — especificación de requerimientos.
- [`docs/PLAN.md`](docs/PLAN.md) — fases y estado.
- [`docs/DECISIONES.md`](docs/DECISIONES.md) — decisiones de diseño.
