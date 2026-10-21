# Capturas de CampusShop (por rol)

Set de capturas de pantalla de **todas las funciones** de CampusShop, agrupadas por rol.
Generadas automáticamente con un driver CDP (Chrome headless + `node`) contra la app
corriendo en `http://127.0.0.1:8000`, con la base de datos recién sembrada
(`php artisan migrate:fresh --seed`).

Todas las cuentas demo usan la contraseña `password` y el dominio
`@unimisionpaz.edu.co`.

| Rol | Cuenta | Tienda |
| --- | --- | --- |
| Admin | `admin@unimisionpaz.edu.co` | — |
| Estudiante | `estudiante@unimisionpaz.edu.co` | — |
| Emprendedor | `laura@unimisionpaz.edu.co` | Dulce Campus |
| Emprendedor | `daniela@unimisionpaz.edu.co` | Lothus |

---

## 1. Público (sin sesión)

| # | Archivo | Qué demuestra |
| --- | --- | --- |
| 01 | `01-inicio-publico.png` | Página de inicio completa (hero, destacados, categorías). |
| 02 | `02-inicio-publico-viewport.png` | Inicio en un viewport real (sin scroll). |
| 03 | `03-catalogo-completo.png` | Catálogo con todos los productos y servicios. |
| 04 | `04-catalogo-filtro-categoria.png` | Filtro por categoría (RF-24). |
| 05 | `05-catalogo-filtro-servicio.png` | Filtro por tipo = servicio. |
| 06 | `06-catalogo-filtro-precio.png` | Filtro por precio máximo + orden ascendente. |
| 07 | `07-catalogo-busqueda.png` | Búsqueda por texto (RF-26). |
| 08 | `08-detalle-producto-torta.png` | Detalle de producto con galería (RF-25). |
| 09 | `09-detalle-producto-gorra.png` | Detalle de un producto de Lothus. |
| 10 | `10-emprendimientos-listado.png` | Listado de emprendimientos aprobados. |
| 11 | `11-emprendimiento-lothus.png` | Tienda **Lothus** (gorras con versículos). |
| 12 | `12-emprendimiento-tutorias.png` | Tienda **Tutorías Pro**. |
| 13 | `13-registro.png` | Formulario de registro. |
| 14 | `14-login.png` | Formulario de ingreso. |
| 15 | `15-recuperar-contrasena.png` | Recuperación de contraseña. |
| 16 | `16-registro-dominio-invalido.png` | RN-01: se rechaza un correo no universitario. |

## 2. Estudiante (comprador)

| # | Archivo | Qué demuestra |
| --- | --- | --- |
| 17 | `17-home-estudiante-logueado.png` | Inicio con sesión de estudiante. |
| 18 | `18-menu-usuario-estudiante.png` | Menú de usuario desplegado. |
| 19 | `19-perfil-estudiante.png` | Edición de perfil (RF-06, RF-08). |
| 20 | `20-carrito-vacio.png` | Carrito vacío (estado inicial). |
| 21 | `21-producto-acciones-comprador.png` | Acciones del comprador: agregar, contactar, reportar. |
| 22 | `22-carrito-con-productos.png` | Carrito con productos. |
| 23 | `23-carrito-checkout-enviado.png` | Checkout: pedido creado con método de pago (RF-36). |
| 24 | `24-mis-pedidos.png` | Listado «Mis pedidos» (RF-40). |
| 25 | `25-detalle-pedido.png` | Detalle de un pedido. |
| 26 | `26-pedido-cancelar-opcion.png` | Opción «Cancelar pedido» visible (RF-42). |
| 27 | `27-pedido-entregado-detalle.png` | Pedido entregado con formulario de reseña (RF-45). |
| 28 | `28-conversaciones-lista.png` | Bandeja de conversaciones (RF-29). |
| 29 | `29-chat-conversacion.png` | Chat con un emprendimiento (RF-30..34). |
| 30 | `30-chat-mensaje-enviado.png` | Mensaje enviado en el chat. |
| 31 | `31-reportar-producto-modal.png` | Modal de reporte de producto (RF-50). |
| 32 | `32-reportar-producto-motivo.png` | Reporte con motivo diligenciado. |
| 33 | `33-activar-emprendimiento.png` | Formulario «Quiero vender» (RF-09). |

## 3. Emprendedor (Dulce Campus — Laura)

| # | Archivo | Qué demuestra |
| --- | --- | --- |
| 34 | `34-mi-tienda-dashboard.png` | Panel «Mi tienda». |
| 35 | `35-mi-tienda-producto-agotado.png` | Cambio de estado de producto a «agotado» (RF-20). |
| 36 | `36-crear-producto-formulario.png` | Formulario para publicar producto/servicio (RF-16). |
| 37 | `37-crear-producto-diligenciado.png` | Formulario diligenciado con imagen adjunta. |
| 38 | `38-producto-creado-confirmacion.png` | Confirmación «Producto publicado correctamente». |
| 39 | `39-editar-producto.png` | Edición de producto (RF-17). |
| 40 | `40-eliminar-producto-modal.png` | Confirmación para eliminar producto (RF-22). |
| 41 | `41-pedidos-recibidos.png` | Pedidos recibidos (RF-41). |
| 42 | `42-pedidos-filtro-estado.png` | Filtro de pedidos por estado. |
| 43 | `43-pedido-recibido-detalle.png` | Detalle de un pedido recibido. |
| 44a | `44a-pedido-accion-confirmar.png` | Acción «Confirmar» disponible (RF-42). |
| 44b | `44b-pedido-confirmado.png` | Pedido confirmado. |
| 44c | `44c-pedido-entregado.png` | Pedido marcado como entregado (RF-43). |
| 45 | `45-metricas-emprendedor.png` | Métricas del emprendimiento (RF-48, RF-49). |
| 46 | `46-metricas-periodo-30d.png` | Métricas con periodo de 30 días. |
| 47 | `47-mi-emprendimiento-editar.png` | Edición de datos del emprendimiento (RF-11). |
| 48 | `48-mi-emprendimiento-pausar-modal.png` | Confirmación para pausar la tienda (RF-12). |

## 3b. Emprendedor (Lothus — Daniela)

| # | Archivo | Qué demuestra |
| --- | --- | --- |
| 49 | `49-mi-tienda-lothus.png` | Panel de Mi tienda para **Lothus**. |
| 50 | `50-pedidos-lothus.png` | Pedidos recibidos de Lothus. |
| 51 | `51-metricas-lothus.png` | Métricas de Lothus. |
| 52 | `52-mi-emprendimiento-lothus.png` | Datos del emprendimiento Lothus. |

## 4. Administrador

| # | Archivo | Qué demuestra |
| --- | --- | --- |
| 53 | `53-admin-dashboard.png` | Panel de administración (resumen). |
| 54 | `54-admin-aprobacion.png` | Emprendimientos por aprobar (RF-53). |
| 55 | `55-admin-rechazar-modal.png` | Modal para rechazar un emprendimiento (RF-54). |
| 56 | `56-admin-rechazar-motivo.png` | Rechazo con motivo diligenciado. |
| 57 | `57-admin-aprobacion-realizada.png` | Aprobación realizada. |
| 58 | `58-admin-emprendimientos.png` | Gestión de emprendimientos (RF-55). |
| 59 | `59-admin-suspender-modal.png` | Confirmación para suspender una tienda. |
| 60 | `60-admin-emprendimiento-destacado.png` | Tienda marcada como destacada. |
| 61 | `61-admin-categorias.png` | Gestión de categorías (RF-52). |
| 62 | `62-admin-categoria-nueva-formulario.png` | Formulario de nueva categoría. |
| 62b | `62b-admin-categoria-creada.png` | Categoría creada. |
| 62c | `62c-admin-categoria-editar-modal.png` | Edición de categoría. |
| 63 | `63-admin-reportes.png` | Gestión de reportes (RF-50, RF-51). |
| 64 | `64-admin-reportes-pendientes.png` | Filtro de reportes pendientes. |
| 65 | `65-admin-reporte-descartado.png` | Reporte descartado. |
| 66 | `66-admin-usuarios.png` | Gestión de cuentas (RF-56). |
| 67 | `67-admin-usuarios-filtro-admin.png` | Filtro de usuarios por rol. |
| 68 | `68-admin-usuario-eliminar-modal.png` | Confirmación para eliminar cuenta (RF-57). |

## 5. Móvil

| # | Archivo | Qué demuestra |
| --- | --- | --- |
| 69 | `69-movil-inicio.png` | Inicio en pantalla móvil (390×844). |
| 70 | `70-movil-catalogo.png` | Catálogo en móvil. |
| 71 | `71-movil-emprendimiento.png` | Tienda Lothus en móvil. |

## 6. Extras

| # | Archivo | Qué demuestra |
| --- | --- | --- |
| 72 | `72-error-404.png` | Página de error 404. |

---

## Cómo se generaron

1. MySQL arriba y app servida: `php artisan serve` en `http://127.0.0.1:8000`.
2. Base de datos limpia: `php artisan migrate:fresh --seed`.
3. Chrome con `--remote-debugging-port=9222`.
4. Driver CDP + escenario: `node shot.js <carpeta-salida> <escenario.js>`.

Las imágenes del catálogo y los logos de las tiendas **viven en el repositorio**
(`public/img/artes` y `public/img/logos`) y se publican al disco público mediante
`ImagenSeeder`, de modo que un clon nuevo reproduce el catálogo con arte real.

Para la captura de publicar un producto (#37) el driver adjunta una imagen PNG de
ejemplo desde su propia carpeta temporal; no forma parte del repositorio.
