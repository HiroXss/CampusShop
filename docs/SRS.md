CampusShop
Especificación de Requerimientos y Diseño de Software (SRS)
Tienda virtual para los emprendimientos de estudiantes universitarios
| Dato | Detalle |
| Autor | Samuel Peña Hurtado |
| Programa | Tecnología en Desarrollo de Software |
| Institución | UniMisiónPaz |
| Stack | Laravel 12 (PHP 8.2+) · MySQL (gestionado con Laragon) · Blade + Bootstrap |
| Versión | 2.0 — 2026 (reemplaza a TruequeU v1.1) |

# Contenido
- Introducción
- Descripción General
- Requerimientos Específicos (incluye 3.4 Reglas de Negocio)
- Requerimientos Funcionales Detallados
- Casos de Uso
- Historias de Usuario
- Diagramas de Actividades y de Estados
- Diseño del Sistema (arquitectura, modelo de datos, rutas, seguridad, métricas)
- Entrevista y Tabulación de Resultados
- Manual de Instalación (incluye plan de pruebas)
- Manual de Usuario
## Registro de cambios de la versión 2.0
Esta versión cambia el propósito del sistema: de una plataforma de trueque entre estudiantes a una tienda virtual donde los estudiantes emprendedores exhiben sus productos y servicios, reciben pedidos y construyen reputación, para vender más y ser más reconocidos. El pedido se gestiona dentro de la plataforma, pero el pago y la entrega se acuerdan por fuera (efectivo o transferencia); los pagos en línea quedan como mejora futura. El sistema pasa a llamarse CampusShop. Como el modelo cambió, los códigos RF, RNF, RN, CU y HU se renumeraron y no tienen correspondencia con la versión 1.1.
| # | Cambio | Secciones afectadas |
| 1 | Nuevo nombre y propósito: CampusShop, tienda virtual de emprendimientos universitarios. Desaparecen el trueque y los estados «reservado» e «intercambiado». | Todo el documento |
| 2 | Se introduce el emprendimiento (una tienda por estudiante) con página pública, logo, descripción y contacto opcional, y con aprobación previa del administrador. | RF-09 a RF-14, RN-02, RFD-02, CU07 a CU10 |
| 3 | La publicación de trueque se reemplaza por producto o servicio con precio, tipo, stock opcional y estados disponible, agotado e inactivo. | RF-15 a RF-22, RN-03, RN-10, RFD-03, CU11 a CU15 |
| 4 | Catálogo con filtros por categoría, tipo y precio, orden por mejor calificados y más vendidos, y una página de inicio con destacados. | RF-23 a RF-28, RN-09, RFD-04, CU16, CU17 |
| 5 | El chat pasa a ser entre comprador y emprendimiento (una conversación por pareja) y también lo usa el emprendedor desde «Mi tienda». | RF-29 a RF-34, RN-05, RFD-05, CU18, CU19 |
| 6 | Nuevo módulo de pedidos (opción B): carrito de un solo emprendimiento, pedido con precios congelados, confirmación, entrega, rechazo y cancelación, con control de stock. | RF-35 a RF-44, RN-06, RFD-06, CU20 a CU23 |
| 7 | Las calificaciones entre usuarios pasan a reseñas del comprador al emprendimiento, solo tras un pedido entregado (una por pedido). | RF-45, RF-46, RN-07, RFD-07, CU24 |
| 8 | Nuevo módulo de reconocimiento y métricas: visitas, pedidos y ventas por emprendedor; emprendimientos destacados, productos más vendidos y mejor calificados. | RF-28, RF-47 a RF-49, RN-08, RN-09, RFD-08, CU16, CU25 |
| 9 | La moderación cubre productos y emprendimientos (reportes, ocultamiento, suspensión) y el administrador aprueba emprendimientos nuevos y destaca los mejores. | RF-50 a RF-57, RN-11, RFD-09, CU26 a CU32 |
| 10 | Modelo de datos nuevo (12 tablas), rutas, seguridad (sobreventa, manipulación de precios, reseñas falsas), cálculo de métricas, diagramas y plan de pruebas actualizados. | Secciones 5, 7, 8, 10 |

# 1. Introducción
## 1.1 Propósito
Este documento especifica los requisitos funcionales y no funcionales de CampusShop, una plataforma web que funciona como tienda virtual para los estudiantes emprendedores de una universidad: cada uno puede abrir su emprendimiento, publicar productos o servicios con precio, recibir pedidos, conversar con los compradores y acumular reseñas que aumentan su reconocimiento. Además describe el diseño técnico del sistema: arquitectura en Laravel, modelo de datos, rutas, seguridad y cálculo de métricas.
## 1.2 Ámbito del Sistema
Nombre del sistema: CampusShop.
Descripción: aplicación web que centraliza la vitrina, la búsqueda, el contacto y el pedido de productos y servicios de emprendimientos estudiantiles. Incluye registro con correo universitario verificado, emprendimientos con página pública y aprobación previa, catálogo con filtros, chat interno entre comprador y emprendedor, pedidos con seguimiento de estado, reseñas verificadas por pedido entregado, métricas para el emprendedor, destacados y rankings, y un módulo de moderación y administración. Opera en español y en pesos colombianos (COP).
Fuera del alcance de esta versión: el cobro en línea, la logística de envíos y la facturación. El comprador y el emprendedor acuerdan el pago (efectivo o transferencia) y la entrega fuera de la plataforma; el sistema solo registra el pedido y su estado.
Beneficiarios: los estudiantes emprendedores, que ganan visibilidad y ventas; los estudiantes compradores, que encuentran emprendimientos de su comunidad con reputación visible; y el personal administrador, que modera el contenido.
## 1.3 Definiciones, Acrónimos y Abreviaturas
- SRS: Especificación de Requerimientos de Software. BD: Base de Datos. ER: modelo Entidad-Relación.
- CRUD: Crear, Leer, Actualizar y Eliminar. MVC: Modelo-Vista-Controlador. COP: peso colombiano.
- Emprendimiento: negocio o tienda de un estudiante dentro de la plataforma, con su propia página pública.
- Emprendedor: estudiante verificado que tiene un emprendimiento aprobado.
- Comprador: estudiante verificado que solicita pedidos a un emprendimiento (todo estudiante puede serlo).
- Producto / servicio: ítem que un emprendimiento ofrece con un precio; el servicio no necesita control de inventario.
- Pedido: solicitud de compra de uno o varios ítems de un solo emprendimiento, con estado y precios congelados.
- Venta: pedido en estado «entregado» (RN-08). Reseña: calificación de 1 a 5 y comentario que un comprador deja a un emprendimiento por un pedido entregado.
- Conversación: hilo de mensajes entre un comprador y un emprendimiento.
- Correo universitario: correo cuyo dominio coincide con el configurado para la institución (RN-01).
- Policy: clase de Laravel que decide si un usuario puede ejecutar una acción sobre un recurso. Soft-delete: eliminación lógica con marca de fecha de borrado.
- RF / RNF: Requerimiento Funcional / No Funcional. RN: Regla de Negocio. CU: Caso de Uso. HU: Historia de Usuario.
## 1.4 Visión General del Documento
El documento describe el sistema en general, lista los requerimientos funcionales y no funcionales con sus reglas de negocio, los detalla por módulo, documenta casos de uso e historias de usuario, representa los flujos principales con diagramas, presenta el diseño técnico, recoge la entrevista de levantamiento y cierra con los manuales de instalación (con plan de pruebas) y de usuario.
# 2. Descripción General
## 2.1 Perspectiva del Producto
CampusShop es una aplicación web monolítica construida con Laravel 12 (arquitectura MVC), vistas Blade y Bootstrap. La autenticación se apoya en Laravel Breeze (con verificación de correo) y la autorización en Policies. La lógica que debe ser consistente —crear y confirmar pedidos, y calcular métricas— se concentra en clases de servicio. La base de datos MySQL (12 tablas propias más las tablas estándar de Laravel) se gestiona localmente con Laragon durante el desarrollo. La versión base no incluye pasarela de pagos, API REST ni aplicación móvil (ver 2.6).
## 2.2 Funciones del Producto
- Acceso y perfil: registro con correo universitario, verificación, inicio y cierre de sesión, recuperación de contraseña y edición del perfil.
- Emprendimientos: activación de la tienda, aprobación del administrador, página pública con logo, descripción, contacto y reseñas, y pausa temporal.
- Productos y servicios: creación con imágenes, precio, tipo y stock opcional; edición, eliminación lógica y cambio de estado (disponible, agotado, inactivo).
- Catálogo y búsqueda: inicio con destacados, más vendidos y mejor calificados; búsqueda por palabra clave, filtros por categoría, tipo y precio, orden y paginación.
- Chat: conversaciones entre comprador y emprendimiento, con bandeja, mensajes no leídos y actualización automática.
- Pedidos: carrito de un emprendimiento, solicitud de pedido, confirmación, entrega, rechazo y cancelación, con control de stock e historial.
- Reseñas: calificación del emprendimiento por parte de quien recibió su pedido, con promedio visible.
- Métricas del emprendedor: visitas, pedidos por estado, ventas y productos más vendidos.
- Moderación y administración: reportes, ocultamiento automático, suspensión, aprobación de emprendimientos, destacados, categorías, cuentas y estadísticas globales.
## 2.3 Características de los Usuarios
- Visitante: persona sin sesión. Puede ver el inicio, el catálogo, el detalle de productos y las páginas de emprendimientos, y buscar y filtrar, pero no conversar, pedir, reseñar ni reportar.
- Estudiante: usuario registrado y verificado. Hereda lo del visitante y puede conversar, armar pedidos, reseñar, reportar y activar su emprendimiento.
- Emprendedor: estudiante con un emprendimiento aprobado. No es un tipo de cuenta distinto: es el mismo usuario con un emprendimiento. Además gestiona su tienda, sus productos, sus pedidos recibidos y sus métricas.
- Administrador: hereda lo del estudiante y además aprueba emprendimientos, destaca, modera, gestiona categorías y cuentas, y consulta estadísticas. La primera cuenta administradora se crea con un seeder (ver sección 10).
## 2.4 Restricciones
- El backend debe desarrollarse con Laravel 12 sobre PHP 8.2 o superior, y la base de datos debe ser MySQL.
- El frontend debe construirse con Blade y Bootstrap.
- El sistema debe operar en español, en pesos colombianos y en la zona horaria configurada (por defecto America/Bogota).
- El registro solo se permite con correos del dominio universitario configurado.
- No se procesan pagos dentro de la plataforma en esta versión (RN-06).
- Los parámetros de negocio (dominio, límites de imágenes, umbral de reportes, intervalo del chat, tamaño de página, tamaño de los rankings) deben estar en configuración y no fijos en el código.
## 2.5 Suposiciones y Dependencias
- Los usuarios tienen conocimientos básicos de navegación web.
- El sistema depende de Composer para las dependencias de PHP y de Node.js/npm para compilar los assets con Vite.
- Se requiere un servicio de correo para la verificación y la recuperación de contraseña: en desarrollo, un servidor de correo local o de pruebas (por ejemplo Mailpit o Mailtrap); en producción, un servicio SMTP.
- Los estudiantes disponen de un correo institucional activo del dominio configurado.
- Los emprendedores se encargan de cobrar y entregar fuera de la plataforma; el sistema confía en que marquen el pedido como entregado de forma honesta (ver riesgos en 8.4).
## 2.6 Requerimientos Futuros
- Pagos en línea con una pasarela (PSE, tarjetas o billeteras) para que el pago ocurra dentro de la plataforma y la venta quede verificada.
- Chat en tiempo real con WebSockets (Laravel Reverb) en lugar de actualización periódica.
- API REST con Laravel Sanctum y aplicación móvil en Flutter.
- Notificaciones por correo o push ante nuevos pedidos y mensajes.
- Varios emprendimientos por estudiante, o emprendimientos con equipo (varios miembros).
- Respuesta del emprendedor a las reseñas y moderación de reseñas abusivas por parte del administrador.
- Carrito con productos de varios emprendimientos, cupones y promociones.
- Filtro por sede o facultad para facilitar la entrega presencial.
# 3. Requerimientos Específicos
## 3.1 Requisitos Funcionales
| Código | Requerimiento |
| RF-01 | [Acceso] Registrar un usuario con nombre, correo universitario, contraseña y programa/facultad. |
| RF-02 | [Acceso] Aceptar solo correos del dominio universitario configurado y rechazar correos ya registrados (RN-01). |
| RF-03 | [Acceso] Enviar un correo de verificación al registrarse y habilitar las funciones de estudiante solo cuando el correo esté verificado. |
| RF-04 | [Acceso] Iniciar sesión con correo y contraseña, con opción de «recordarme». |
| RF-05 | [Acceso] Bloquear el inicio de sesión de cuentas desactivadas por un administrador. |
| RF-06 | [Acceso] Cerrar sesión invalidando la sesión. |
| RF-07 | [Acceso] Recuperar la contraseña mediante un enlace enviado al correo registrado. |
| RF-08 | [Perfil] Editar el perfil propio: nombre, programa/facultad y foto. |
| RF-09 | [Emprendimientos] Permitir a un estudiante verificado activar su emprendimiento con nombre, descripción, categoría (rubro), logo y contacto público opcional (WhatsApp, Instagram); queda en estado «pendiente» y cada estudiante puede tener uno solo (RN-02). |
| RF-10 | [Emprendimientos] Permitir al administrador aprobar o rechazar (con motivo) los emprendimientos pendientes; un emprendimiento rechazado puede corregirse y reenviarse. |
| RF-11 | [Emprendimientos] Editar el emprendimiento propio (descripción, logo, banner, rubro y contacto) sin nueva aprobación. |
| RF-12 | [Emprendimientos] Pausar y reactivar el emprendimiento propio; mientras está pausado no aparece en el catálogo ni recibe pedidos nuevos. |
| RF-13 | [Emprendimientos] Mostrar una página pública por emprendimiento con logo, banner, descripción, rubro, contacto público, sus productos, el promedio y la cantidad de reseñas, y las reseñas recibidas. |
| RF-14 | [Emprendimientos] Listar los emprendimientos visibles, con búsqueda por nombre, filtro por categoría y paginación de a 12. |
| RF-15 | [Productos] Crear un producto o servicio con nombre, descripción, tipo, categoría, precio en COP, stock opcional y de 1 a 5 imágenes; solo lo puede hacer el dueño de un emprendimiento aprobado o pausado. |
| RF-16 | [Productos] Validar las imágenes por formato (jpg, png, webp) y tamaño máximo de 2 MB cada una (RN-10). |
| RF-17 | [Productos] Editar un producto propio. |
| RF-18 | [Productos] Eliminar (soft-delete) un producto propio; los pedidos ya hechos conservan sus datos (RN-03). |
| RF-19 | [Productos] Cambiar el estado de un producto propio entre «disponible», «agotado» e «inactivo» desde «Mi tienda», con una sola acción. |
| RF-20 | [Productos] Ofrecer el panel «Mi tienda» con todos los productos propios, su estado y stock, y filtro por estado. |
| RF-21 | [Productos] Marcar automáticamente un producto como «agotado» cuando su stock llega a 0, y permitir reponer el stock. |
| RF-22 | [Productos] Mostrar el detalle de un producto con galería, descripción, precio, disponibilidad y datos del emprendimiento (nombre, calificación y enlace a su página). |
| RF-23 | [Catálogo] Listar los productos visibles, paginados de a 12, a visitantes y estudiantes (RN-12). |
| RF-24 | [Catálogo] Filtrar productos por categoría. |
| RF-25 | [Catálogo] Filtrar productos por tipo (producto o servicio) y por rango de precio. |
| RF-26 | [Catálogo] Buscar por palabra clave en el nombre, la descripción y el nombre del emprendimiento. |
| RF-27 | [Catálogo] Combinar búsqueda y filtros, y ordenar por más recientes (por defecto), precio menor, precio mayor, mejor calificados o más vendidos. |
| RF-28 | [Catálogo] Mostrar una página de inicio con emprendimientos destacados, productos más vendidos, emprendimientos mejor calificados y novedades (RN-09). |
| RF-29 | [Chat] Permitir a un estudiante iniciar una conversación con un emprendimiento desde su página o desde un producto; existe una sola conversación por comprador y emprendimiento. |
| RF-30 | [Chat] Enviar mensajes de texto de hasta 1000 caracteres dentro de una conversación. |
| RF-31 | [Chat] Mostrar una bandeja con las conversaciones, el último mensaje y el número de mensajes no leídos; el emprendedor ve las de su emprendimiento. |
| RF-32 | [Chat] Marcar como leídos los mensajes recibidos al abrir la conversación. |
| RF-33 | [Chat] Actualizar automáticamente la conversación abierta y el contador de no leídos cada 10 segundos. |
| RF-34 | [Chat] Impedir conversaciones con el propio emprendimiento y con emprendimientos que no estén aprobados o estén ocultos o suspendidos (RN-05). |
| RF-35 | [Pedidos] Agregar productos disponibles al carrito con su cantidad, modificarlas o quitarlas; el carrito contiene ítems de un solo emprendimiento. |
| RF-36 | [Pedidos] Solicitar el pedido indicando el método de pago acordado (efectivo o transferencia) y una nota de entrega opcional; el sistema valida la disponibilidad, congela los precios y crea el pedido «pendiente». |
| RF-37 | [Pedidos] Impedir pedir productos del propio emprendimiento, productos no disponibles, de emprendimientos pausados o no aprobados, o cantidades mayores al stock (RN-06). |
| RF-38 | [Pedidos] Ofrecer al comprador «Mis pedidos» con el estado, el detalle y el historial. |
| RF-39 | [Pedidos] Permitir al comprador cancelar un pedido mientras esté «pendiente». |
| RF-40 | [Pedidos] Ofrecer al emprendedor «Pedidos recibidos» con filtro por estado y el detalle de cada pedido (nombre y programa del comprador, ítems, total, pago y nota). |
| RF-41 | [Pedidos] Permitir al emprendedor confirmar un pedido pendiente; al confirmar, el sistema descuenta el stock dentro de una transacción (RN-06, RNF-16). |
| RF-42 | [Pedidos] Permitir al emprendedor rechazar un pedido pendiente indicando un motivo. |
| RF-43 | [Pedidos] Permitir al emprendedor marcar un pedido confirmado como «entregado». |
| RF-44 | [Pedidos] Permitir al emprendedor cancelar un pedido confirmado con un motivo; el stock descontado se devuelve. |
| RF-45 | [Reseñas] Permitir al comprador reseñar el emprendimiento de un pedido entregado con una puntuación de 1 a 5 y un comentario opcional, una sola vez por pedido (RN-07). |
| RF-46 | [Reseñas] Calcular y mostrar el promedio de reseñas y su cantidad en la página del emprendimiento, en el catálogo y en el detalle de los productos. |
| RF-47 | [Métricas] Registrar las visitas a la página de un emprendimiento y al detalle de sus productos (RN-08). |
| RF-48 | [Métricas] Ofrecer al emprendedor un panel con visitas, pedidos por estado, ventas en COP y calificación promedio, para los últimos 7, 30 o 90 días o el total. |
| RF-49 | [Métricas] Mostrar en ese panel los productos más vendidos del emprendimiento. |
| RF-50 | [Moderación] Permitir a un estudiante reportar un producto o un emprendimiento ajeno con un motivo, una sola vez por elemento. |
| RF-51 | [Moderación] Ocultar automáticamente del público un producto o emprendimiento que alcance el umbral de reportes (3 por defecto). |
| RF-52 | [Moderación] Ofrecer al administrador un panel de reportes para ver los motivos y decidir reactivar el elemento, eliminar el producto o suspender el emprendimiento. |
| RF-53 | [Administración] Permitir al administrador el CRUD de categorías; no se elimina una categoría con productos o emprendimientos asociados. |
| RF-54 | [Administración] Permitir al administrador marcar y desmarcar emprendimientos como destacados. |
| RF-55 | [Administración] Permitir al administrador suspender y reactivar emprendimientos. |
| RF-56 | [Administración] Permitir al administrador desactivar y reactivar cuentas de usuario. |
| RF-57 | [Administración] Mostrar al administrador estadísticas globales: usuarios registrados, emprendimientos aprobados y pendientes de revisión, productos visibles, pedidos entregados, ventas registradas en COP y reportes pendientes. |

## 3.2 Requisitos No Funcionales
| Código | Requerimiento |
| RNF-01 | Proteger las rutas con los middleware de autenticación y verificación de correo; las de tienda, además, con el middleware emprendedor (emprendimiento aprobado o pausado) y las de administración con verificación de rol. |
| RNF-02 | Almacenar las contraseñas con hash (el algoritmo por defecto de Laravel). |
| RNF-03 | Incluir protección CSRF en todos los formularios que modifican datos. |
| RNF-04 | Validar toda entrada en el servidor con Form Requests, escapar la salida en Blade para evitar XSS y acceder a la base de datos con Eloquent o consultas parametrizadas para evitar inyección SQL. |
| RNF-05 | Autorizar cada acción sobre un recurso mediante Policies en el servidor; ocultar un botón en la vista no se considera control de acceso (RN-04). |
| RNF-06 | Usar MySQL con modelo relacional normalizado hasta 3FN, claves foráneas, restricciones únicas e índices en las columnas de búsqueda y filtro; los valores monetarios se guardan como DECIMAL. |
| RNF-07 | Aplicar soft-delete en usuarios, emprendimientos y productos para conservar el historial de pedidos, reseñas y chats. |
| RNF-08 | La interfaz debe mostrarse sin desplazamiento horizontal desde 360 px de ancho y usar Bootstrap. |
| RNF-09 | Los listados, las búsquedas y la página de inicio deben responder en menos de 2 segundos en el 95 % de las solicitudes con hasta 500 productos y 100 emprendimientos; el panel de métricas, en menos de 3 segundos. |
| RNF-10 | Organizar el código con la arquitectura MVC de Laravel, separando validación (Form Requests), autorización (Policies), lógica de negocio crítica (servicios PedidoService y MetricasService) y acceso a datos (modelos). |
| RNF-11 | Mantener los parámetros de negocio en .env y config/, sin valores fijos en el código. |
| RNF-12 | Guardar las imágenes en el almacenamiento público de Laravel con nombres aleatorios, validando el tipo MIME real y no solo la extensión. |
| RNF-13 | Limitar los intentos de inicio de sesión (5 por minuto), el envío de mensajes (30 por minuto por usuario) y la creación de pedidos (10 por hora por usuario). |
| RNF-14 | Expirar la sesión tras 120 minutos de inactividad (valor configurable). |
| RNF-15 | Registrar quién revisó cada reporte y cada emprendimiento pendiente, y cuándo, como auditoría mínima de la moderación. |
| RNF-16 | Crear y confirmar pedidos dentro de una transacción de base de datos con bloqueo de las filas de producto, para evitar la sobreventa; los precios y totales se calculan siempre en el servidor a partir de la base de datos, nunca desde el formulario. |
| RNF-17 | Mostrar los valores monetarios en COP, con separador de miles y sin decimales (por ejemplo, $ 12.500). |
| RNF-18 | Funcionar en navegadores modernos (Chrome, Firefox y Edge). |
| RNF-19 | Poder desplegarse en un entorno LAMP estándar con PHP 8.2 o superior, sin dependencias complejas adicionales. |
| RNF-20 | Presentar textos y fechas en español y usar la zona horaria configurada en APP_TIMEZONE. |

## 3.3 Otros Requisitos
- Accesibilidad: la interfaz debe ser utilizable desde computador y celular (RNF-08).
- Compatibilidad: funcionar en los navegadores más usados por la comunidad estudiantil (RNF-18).
- Confiabilidad: no deben perderse pedidos, mensajes ni reseñas ante errores; el borrado es lógico y el pedido conserva copia del nombre y precio de cada ítem (RNF-07, RN-06).
- Privacidad: el correo de los usuarios nunca se muestra en páginas públicas. Los datos de contacto de un emprendimiento (WhatsApp, Instagram) se muestran solo si el emprendedor decidió publicarlos. El emprendedor ve del comprador únicamente su nombre y programa.
- Alcance de pagos: la plataforma no cobra ni verifica pagos; las ventas registradas son las declaradas por el emprendedor al marcar los pedidos como entregados (RN-08).
- Localización: fechas, moneda e idioma en español de Colombia.
## 3.4 Reglas de Negocio
Cada requerimiento funcional que use una regla debe interpretarse conforme a lo aquí definido. Los valores numéricos son los predeterminados y se mantienen en configuración (RNF-11).
RN-01 — Correo universitario
- Solo se aceptan correos que terminen en el dominio configurado (UNIVERSITY_EMAIL_DOMAIN).
- El correo debe ser único en el sistema.
- La cuenta queda sin acceso a las funciones de estudiante hasta que el usuario abra el enlace de verificación.
RN-02 — Emprendimientos
- Cada estudiante puede tener un solo emprendimiento; el nombre debe ser único en el sistema.
- Estados: pendiente, aprobado, rechazado, pausado y suspendido. Al crearse queda pendiente (si REQUIRE_APPROVAL es falso, queda aprobado).
- Un emprendimiento aprobado o pausado puede gestionar su tienda (productos, pedidos en curso, chats y métricas); solo el aprobado aparece en el catálogo y recibe pedidos nuevos. El pausado conserva todo, pero deja de ser visible y de recibir pedidos.
- El administrador aprueba o rechaza; el rechazo exige un motivo y el estudiante puede corregir y reenviar (vuelve a pendiente).
- El administrador puede suspender un emprendimiento (por ejemplo, tras revisar reportes); un emprendimiento suspendido no es visible ni opera, y solo el administrador lo reactiva.
- Editar la descripción, el logo, el banner, el rubro o el contacto no requiere nueva aprobación.
RN-03 — Productos y servicios
- Tipos: producto y servicio. El precio es un valor en COP mayor que 0.
- El stock es opcional: vacío significa «sin control de inventario» (típico de los servicios); un número controla las existencias.
- Estados: disponible, agotado e inactivo. Solo un producto disponible puede pedirse. Si el stock llega a 0 el producto pasa a agotado; el emprendedor puede reponerlo y reactivarlo.
- El producto inactivo no se muestra al público; el agotado se muestra con la etiqueta «Agotado» y sin botón de pedido.
- Eliminar un producto es lógico: desaparece del catálogo, pero los pedidos anteriores conservan nombre, precio y cantidad.
RN-04 — Permisos
- Solo el dueño del emprendimiento puede editar el emprendimiento, gestionar sus productos, ver sus pedidos recibidos y sus métricas.
- Solo el comprador puede ver y cancelar (si está pendiente) sus propios pedidos y reseñar sus pedidos entregados.
- El administrador puede ocultar, eliminar o suspender elementos a través del módulo de moderación, pero no editar su contenido.
- Las rutas de administración solo son accesibles para usuarios con rol admin.
- La autorización se valida en el servidor mediante Policies (RNF-05).
RN-05 — Reglas del chat
- Un estudiante no puede iniciar una conversación con su propio emprendimiento.
- No se pueden iniciar conversaciones con emprendimientos pendiente, rechazado, suspendido u ocultos; con uno pausado sí, para que el emprendedor pueda atender consultas.
- Existe una única conversación por pareja (emprendimiento, comprador); si ya existe, el sistema abre esa en lugar de crear otra. Si el comprador empieza desde un producto, ese producto queda como referencia de contexto.
- Solo el comprador y el dueño del emprendimiento pueden ver o escribir en la conversación.
- Los visitantes sin sesión no pueden iniciar conversaciones.
RN-06 — Pedidos
- Un pedido contiene ítems de un solo emprendimiento, con cantidades enteras desde 1; si el producto tiene stock, la cantidad no puede superarlo.
- El comprador no puede pedir a su propio emprendimiento.
- Al crear el pedido, el sistema copia el nombre y el precio actuales de cada ítem y calcula el total; cambios posteriores en el producto no alteran pedidos existentes.
- El pago y la entrega se acuerdan fuera de la plataforma; el comprador solo declara el método (efectivo o transferencia) y puede dejar una nota de entrega.
- El stock se descuenta cuando el emprendedor confirma el pedido, no cuando se solicita. Si al confirmar el stock ya no alcanza, la confirmación se rechaza y el emprendedor debe rechazar o conversar con el comprador.
- Si un pedido confirmado se cancela, el stock se devuelve; el estado entregado es final.
- El rechazo y la cancelación por parte del emprendedor exigen un motivo, visible para el comprador.
| De | A | Quién | Condición o efecto |
| (nuevo) | pendiente | Comprador | Valida disponibilidad y congela precios. |
| pendiente | confirmado | Emprendedor | Descuenta el stock en una transacción. |
| pendiente | rechazado | Emprendedor | Motivo obligatorio. Estado final. |
| pendiente | cancelado | Comprador | Sin efecto en el stock. Estado final. |
| confirmado | entregado | Emprendedor | Habilita la reseña y cuenta como venta. Estado final. |
| confirmado | cancelado | Emprendedor | Motivo obligatorio; devuelve el stock. Estado final. |

RN-07 — Reseñas
- Solo el comprador de un pedido entregado puede reseñar, y únicamente el emprendimiento de ese pedido.
- Máximo una reseña por pedido; la puntuación es un entero de 1 a 5 y el comentario es opcional (hasta 500 caracteres).
- Una reseña no puede editarse ni eliminarse en esta versión.
- Promedio = media aritmética de las puntuaciones recibidas, redondeada a un decimal.
RN-08 — Ventas, visitas y métricas
- Venta = pedido en estado entregado. Valor de la venta = suma de precio_unitario × cantidad de sus ítems.
- Las ventas dependen de lo que declara el emprendedor; el sistema no verifica el pago (ver 8.4).
- Una visita se cuenta cuando una persona distinta del dueño abre la página del emprendimiento o el detalle de uno de sus productos; se registra como máximo una por sesión de navegador, página y día.
- El periodo de una métrica se mide con la fecha de entrega (ventas) o de creación (pedidos y visitas).
RN-09 — Destacados y rankings
- Destacados: emprendimientos marcados por el administrador, visibles y aprobados; se muestran hasta FEATURED_LIMIT (8) en el inicio.
- Más vendidos: productos con más unidades en pedidos entregados en los últimos TOP_SELLING_DAYS (30) días.
- Mejor calificados: emprendimientos con al menos MIN_REVIEWS_FOR_RANKING (3) reseñas, ordenados por promedio y, en empate, por cantidad de reseñas.
- Novedades: los productos visibles más recientes.
RN-10 — Imágenes
- Cada producto tiene entre 1 y 5 imágenes; el emprendimiento tiene un logo obligatorio y un banner opcional.
- Formatos permitidos: jpg, png y webp. Tamaño máximo: 2 MB por imagen.
RN-11 — Reportes y moderación
- Un estudiante no puede reportar su propio producto o emprendimiento y solo puede reportar una vez cada elemento.
- Al llegar a 3 reportes pendientes (REPORTS_THRESHOLD) sobre un producto o emprendimiento, este se oculta del público.
- El administrador revisa los reportes y decide: reactivar (los reportes pasan a «descartado» y dejan de contar) o sancionar —eliminar el producto o suspender el emprendimiento— (los reportes pasan a «aceptado»).
- Se registra qué administrador resolvió y cuándo (RNF-15).
RN-12 — Glosario de términos verificables
| Término | Definición operativa |
| Emprendimiento visible | Estado aprobado, no oculto y no eliminado. Si además no está pausado, aparece en el catálogo y recibe pedidos. |
| Producto visible | Estado disponible o agotado, no oculto ni eliminado, y de un emprendimiento visible y no pausado. |
| Producto pedible | Producto visible en estado disponible (con stock suficiente si controla inventario). |
| Mensaje no leído | Mensaje recibido por el usuario cuyo campo leido_at está vacío. |
| Actualización automática del chat | Consulta al servidor cada 10 segundos (CHAT_POLL_SECONDS) mientras la conversación está abierta. |
| Interfaz responsiva | Se ve sin desplazamiento horizontal desde 360 px de ancho (RNF-08). |
| Respuesta rápida | Menos de 2 segundos en el 95 % de las solicitudes (RNF-09). |
| Reportes pendientes | Reportes de un elemento cuyo estado es pendiente. |

# 4. Requerimientos Funcionales Detallados
A continuación se detalla, en formato entrada-proceso-salida, cada módulo funcional.
### RFD-01 — Acceso y Perfil
| Campo | Detalle |
| Grado de necesidad | Alta |
| Descripción | Registro con correo universitario y verificación, inicio y cierre de sesión, recuperación de contraseña y edición del perfil propio. |
| Entradas | name, email, password, program, avatar |
| Fuente | Formularios Blade de registro, login, recuperación y perfil |
| Salida | Sesión autenticada, correo de verificación, perfil actualizado |
| Destino | Tabla users |
| Restricciones | El correo debe ser del dominio universitario y único (RN-01); las cuentas desactivadas no inician sesión; la contraseña se guarda con hash (RNF-02); límite de 5 intentos de login por minuto (RNF-13). |
| Proceso | El sistema valida los datos, crea el usuario sin verificar y envía el enlace de verificación; al abrirlo, marca email_verified_at y habilita las funciones de estudiante. El login valida credenciales y estado is_active, y regenera la sesión. |
| Efecto colateral | Si falla el envío de correo, el usuario no podrá verificar su cuenta; debe poder solicitar un nuevo enlace. |

### RFD-02 — Emprendimientos
| Campo | Detalle |
| Grado de necesidad | Alta |
| Descripción | Activación del emprendimiento por un estudiante, revisión del administrador, edición, pausa, página pública y listado de emprendimientos. |
| Entradas | nombre, descripcion, categoria_id, logo, banner, whatsapp, instagram; decisión del administrador (aprobar o rechazar con motivo) |
| Fuente | Formulario «Activar mi emprendimiento», panel «Mi tienda» y panel de administración |
| Salida | Emprendimiento creado y en revisión, aprobado o rechazado; página pública; listado filtrado y paginado |
| Destino | Tabla emprendimientos |
| Restricciones | Un emprendimiento por estudiante y nombre único (RN-02); solo los aprobados y no pausados aparecen en el catálogo (RN-12); logo y banner según RN-10. |
| Proceso | El sistema valida los datos, genera el slug, guarda el emprendimiento en estado pendiente y lo muestra en la cola del administrador. Al aprobar, registra quién y cuándo (RNF-15) y habilita la publicación de productos. Al rechazar, guarda el motivo y permite corregir y reenviar. |
| Efecto colateral | Un emprendimiento con datos engañosos podría aprobarse por descuido; por eso existe la suspensión y el reporte de usuarios (RN-11). |

### RFD-03 — Productos y Servicios
| Campo | Detalle |
| Grado de necesidad | Alta |
| Descripción | CRUD de productos y servicios con precio, tipo, stock opcional e imágenes; cambio de estado desde «Mi tienda»; detalle público. |
| Entradas | nombre, descripcion, tipo, categoria_id, precio, stock, imágenes (1 a 5), estado |
| Fuente | Formularios de producto y panel «Mi tienda» |
| Salida | Producto creado, editado, eliminado o con estado cambiado; detalle del producto |
| Destino | Tablas productos, producto_imagenes |
| Restricciones | Solo el dueño de un emprendimiento aprobado o pausado gestiona sus productos (RN-02, RN-04); precio mayor que 0; imágenes según RN-10; reglas de estado y stock de RN-03. |
| Proceso | El sistema valida los datos y las imágenes, guarda los archivos con nombre aleatorio, asocia el producto al emprendimiento del usuario y lo crea disponible. El cambio de estado se hace con una acción desde el panel. Si el stock llega a 0, el estado pasa automáticamente a agotado. |
| Efecto colateral | Una validación débil de imágenes permitiría archivos no deseados en el servidor (RNF-12). |

### RFD-04 — Catálogo y Búsqueda
| Campo | Detalle |
| Grado de necesidad | Alta |
| Descripción | Página de inicio con destacados y rankings; catálogo de productos con búsqueda, filtros, orden y paginación; listado de emprendimientos. |
| Entradas | texto de búsqueda, categoria_id, tipo, precio mínimo y máximo, orden |
| Fuente | Barra de búsqueda, filtros del catálogo y página de inicio |
| Salida | Listado de productos visibles filtrado y paginado (12 por página); secciones del inicio |
| Destino | Consultas sobre productos, emprendimientos, resenas, pedido_items y pedidos |
| Restricciones | Solo productos y emprendimientos visibles (RN-12); rankings según RN-09; respuesta en menos de 2 segundos (RNF-09). |
| Proceso | El sistema combina los filtros activos y la palabra clave (nombre, descripción y emprendimiento) en una única consulta y devuelve la página solicitada. El orden «mejor calificados» y «más vendidos» usa el promedio de reseñas y las unidades de pedidos entregados. |
| Efecto colateral | Búsquedas con LIKE '%texto%' no usan índice; con más datos puede ser necesario un índice FULLTEXT. |

### RFD-05 — Chat
| Campo | Detalle |
| Grado de necesidad | Alta |
| Descripción | Conversaciones entre comprador y emprendimiento, con bandeja, mensajes no leídos y actualización automática cada 10 segundos. |
| Entradas | emprendimiento_id, producto_id (opcional), contenido del mensaje |
| Fuente | Botón «Escribir al emprendimiento», bandeja y pantalla de conversación |
| Salida | Conversación creada o reutilizada, mensajes registrados, contador de no leídos |
| Destino | Tablas conversaciones, mensajes |
| Restricciones | Reglas de RN-05; solo los dos participantes acceden; máximo 1000 caracteres por mensaje; 30 mensajes por minuto (RNF-13). |
| Proceso | Al contactar, el sistema busca la conversación (emprendimiento, comprador) y la crea si no existe. Cada mensaje se guarda con leido_at vacío; al abrir la conversación, los mensajes recibidos se marcan leídos. La vista consulta por mensajes nuevos cada 10 segundos. |
| Efecto colateral | La actualización periódica genera tráfico constante mientras haya conversaciones abiertas; en una versión futura se reemplazaría por WebSockets. |

### RFD-06 — Pedidos
| Campo | Detalle |
| Grado de necesidad | Alta |
| Descripción | Carrito de un emprendimiento, solicitud de pedido, seguimiento por el comprador y gestión por el emprendedor (confirmar, rechazar, entregar, cancelar). |
| Entradas | producto_id y cantidad por ítem, metodo_pago, nota; motivo (rechazo o cancelación) |
| Fuente | Carrito, página «Mis pedidos» y panel «Pedidos recibidos» |
| Salida | Pedido creado con sus ítems, cambios de estado, stock actualizado, historial |
| Destino | Tablas pedidos, pedido_items, productos |
| Restricciones | Reglas y transiciones de RN-06; solo comprador y emprendedor del pedido acceden a él (RN-04); 10 pedidos por hora por usuario (RNF-13). |
| Proceso | Al solicitar, PedidoService abre una transacción, bloquea los productos, valida disponibilidad y cantidades, copia nombre y precio, calcula el total y crea el pedido pendiente. Al confirmar, vuelve a validar el stock, lo descuenta y cambia el estado; si algún producto llega a 0 lo marca agotado. Al entregar, guarda entregado_at y habilita la reseña. |
| Efecto colateral | Dos compradores podrían pedir la última unidad a la vez; la transacción con bloqueo (RNF-16) evita la sobreventa, y el segundo recibe un mensaje claro. |

### RFD-07 — Reseñas
| Campo | Detalle |
| Grado de necesidad | Alta |
| Descripción | Reseña del comprador al emprendimiento tras un pedido entregado, con promedio visible en el emprendimiento, el catálogo y el detalle. |
| Entradas | pedido_id, puntuacion (1 a 5), comentario |
| Fuente | Botón «Reseñar» en «Mis pedidos» (pedidos entregados) |
| Salida | Reseña registrada, promedio y cantidad actualizados |
| Destino | Tabla resenas |
| Restricciones | Solo el comprador de un pedido entregado; una reseña por pedido; no se edita (RN-07). |
| Proceso | El sistema valida el pedido y el comprador, guarda la reseña y el promedio se calcula a partir de las reseñas del emprendimiento. |
| Efecto colateral | Un emprendedor podría fabricar pedidos con una cuenta aliada para inflar su reputación; los controles están en 8.4 (pedido entregado obligatorio, límites de pedidos, reportes y suspensión). |

### RFD-08 — Reconocimiento y Métricas
| Campo | Detalle |
| Grado de necesidad | Alta |
| Descripción | Registro de visitas, panel de métricas del emprendedor y secciones de reconocimiento del inicio (destacados, más vendidos, mejor calificados). |
| Entradas | emprendimiento_id y producto_id de la página visitada; periodo (7, 30, 90 días o total); marca de destacado del administrador |
| Fuente | Páginas públicas del emprendimiento y del producto; panel «Mis métricas»; panel de administración |
| Salida | Visitas registradas; visitas, pedidos por estado, ventas y productos más vendidos; secciones del inicio |
| Destino | Tabla visitas; consultas sobre pedidos, pedido_items y resenas |
| Restricciones | Definiciones de RN-08 y RN-09; el emprendedor solo ve las métricas de su emprendimiento (RN-04); respuesta en menos de 3 segundos (RNF-09). |
| Proceso | Al abrir una página pública, el sistema registra la visita si corresponde (RN-08). MetricasService agrupa las consultas del panel por periodo: cuenta visitas y pedidos, suma el valor de los pedidos entregados y ordena los productos por unidades vendidas. |
| Efecto colateral | Las métricas son tan fiables como los estados de los pedidos; por eso la venta se define solo con pedidos entregados. |

### RFD-09 — Moderación y Administración
| Campo | Detalle |
| Grado de necesidad | Media |
| Descripción | Reportes de productos y emprendimientos, ocultamiento automático, revisión del administrador, suspensión, aprobación, destacados, categorías, cuentas y estadísticas. |
| Entradas | tipo y id del elemento reportado, motivo; decisión del administrador; nombre y descripción de categorías; marca de destacado; estado de cuenta |
| Fuente | Botón «Reportar» en producto y emprendimiento; panel de administración |
| Salida | Reporte registrado, elemento oculto, reactivado, eliminado o suspendido; categorías, destacados y cuentas actualizados; estadísticas |
| Destino | Tablas reportes, productos, emprendimientos, categorias, users |
| Restricciones | Un reporte por usuario y elemento; no se reporta lo propio; solo el administrador accede al panel (RN-04, RN-11); no se elimina una categoría con elementos asociados. |
| Proceso | Al registrar un reporte, el sistema cuenta los pendientes del elemento; si alcanzan el umbral, lo marca como oculto. El administrador revisa los motivos y decide; se guarda quién resolvió y cuándo. |
| Efecto colateral | Reportes coordinados podrían ocultar elementos legítimos; el administrador puede reactivarlos y los reportes descartados dejan de contar. |

# 5. Casos de Uso
Los siguientes diagramas muestran los casos de uso de cada actor. El estudiante hereda los casos de uso del visitante, el emprendedor los del estudiante y el administrador también los del estudiante.
[IMAGEN]
Casos de uso del visitante
[IMAGEN]
Casos de uso del estudiante
[IMAGEN]
Casos de uso del emprendedor
[IMAGEN]
Casos de uso del administrador
## Lista de Casos de Uso por Módulo
Acceso y perfil
- CU01 — Registrarse con correo universitario
- CU02 — Verificar correo
- CU03 — Iniciar sesión
- CU04 — Cerrar sesión
- CU05 — Recuperar contraseña
- CU06 — Editar perfil propio
Emprendimientos
- CU07 — Activar mi emprendimiento
- CU08 — Aprobar o rechazar emprendimientos (administrador)
- CU09 — Editar y pausar mi emprendimiento
- CU10 — Ver emprendimientos y su página pública
Productos y servicios
- CU11 — Crear producto o servicio
- CU12 — Editar producto o servicio
- CU13 — Eliminar producto o servicio
- CU14 — Cambiar estado de un producto
- CU15 — Ver detalle de un producto
Catálogo
- CU16 — Ver la página de inicio
- CU17 — Buscar y filtrar productos
Chat
- CU18 — Iniciar conversación con un emprendimiento
- CU19 — Enviar y leer mensajes
Pedidos
- CU20 — Armar carrito y solicitar pedido
- CU21 — Consultar y cancelar mis pedidos
- CU22 — Confirmar o rechazar pedidos recibidos
- CU23 — Entregar o cancelar un pedido confirmado
Reseñas y métricas
- CU24 — Reseñar un emprendimiento
- CU25 — Consultar mis métricas
Moderación y administración
- CU26 — Reportar producto o emprendimiento
- CU27 — Moderar reportes (administrador)
- CU28 — Gestionar categorías (administrador)
- CU29 — Destacar emprendimientos (administrador)
- CU30 — Suspender o reactivar emprendimientos (administrador)
- CU31 — Gestionar cuentas de usuario (administrador)
- CU32 — Consultar estadísticas globales (administrador)
## Detalle de los Casos de Uso
CU01 — Registrarse con correo universitario
Permite a una persona crear su cuenta con un correo institucional.
Campos: nombre, correo universitario, contraseña y confirmación, programa/facultad.
Validaciones: el correo debe terminar en el dominio configurado y no estar registrado; la contraseña debe cumplir la longitud mínima y coincidir con su confirmación; al crearse, se envía el correo de verificación.
CU02 — Verificar correo
Activa la cuenta mediante el enlace recibido por correo.
Campos: enlace firmado de verificación.
Validaciones: el enlace debe ser válido y no estar vencido; si venció, el usuario puede solicitar uno nuevo; mientras no verifique, no accede a las funciones de estudiante.
CU03 — Iniciar sesión
Campos: correo, contraseña, casilla «recordarme».
Validaciones: las credenciales deben coincidir; la cuenta debe estar activa y verificada; tras 5 intentos fallidos en un minuto se bloquea temporalmente el intento.
CU04 — Cerrar sesión
Campos: ninguno (opción del menú).
Validaciones: la sesión se invalida por completo y se redirige al inicio.
CU05 — Recuperar contraseña
Campos: correo; luego, nueva contraseña y confirmación.
Validaciones: el sistema responde igual exista o no el correo, para no revelar qué cuentas existen; el enlace de restablecimiento caduca.
CU06 — Editar perfil propio
Campos: nombre, programa/facultad, foto.
Validaciones: la foto cumple las reglas de imagen (RN-10); el correo no se edita desde esta pantalla.
CU07 — Activar mi emprendimiento
Permite a un estudiante verificado abrir su tienda.
Campos: nombre, descripción, categoría, logo, banner (opcional), WhatsApp e Instagram (opcionales).
Validaciones: el estudiante no tiene ya un emprendimiento; el nombre es único; la descripción y el logo son obligatorios; el emprendimiento queda pendiente y el estudiante ve su estado en «Mi tienda».
CU08 — Aprobar o rechazar emprendimientos (administrador)
Campos: emprendimiento pendiente, decisión (aprobar o rechazar) y motivo si se rechaza.
Validaciones: solo administradores; el rechazo exige motivo; se registra quién resolvió y cuándo; un rechazado vuelve a la cola cuando el estudiante lo corrige y reenvía.
CU09 — Editar y pausar mi emprendimiento
Campos: descripción, logo, banner, categoría, contacto público; acción pausar o reactivar.
Validaciones: solo el dueño; no requiere nueva aprobación; pausar solo aplica a un emprendimiento aprobado y lo oculta del catálogo sin borrar nada (RN-02).
CU10 — Ver emprendimientos y su página pública
Campos: texto de búsqueda y categoría en el listado; ninguno para abrir una página.
Validaciones: muestra solo emprendimientos visibles; la página incluye productos, promedio de reseñas, reseñas y los datos de contacto que el emprendedor decidió publicar; no muestra el correo; registra la visita (RN-08).
CU11 — Crear producto o servicio
Campos: nombre, descripción, tipo, categoría, precio, stock (opcional), de 1 a 5 imágenes.
Validaciones: solo el dueño de un emprendimiento aprobado o pausado; todos los campos salvo el stock son obligatorios; precio mayor que 0; imágenes según RN-10; se crea disponible.
CU12 — Editar producto o servicio
Campos: los mismos de CU11.
Validaciones: solo el dueño (RN-04); los cambios no alteran pedidos existentes (RN-06).
CU13 — Eliminar producto o servicio
Campos: confirmación del usuario.
Validaciones: solo el dueño; el borrado es lógico y los pedidos anteriores conservan sus datos.
CU14 — Cambiar estado de un producto
Campos: producto y nuevo estado (disponible, agotado o inactivo).
Validaciones: solo el dueño; se hace con una acción desde «Mi tienda»; un producto con stock 0 no puede ponerse disponible sin reponer el stock.
CU15 — Ver detalle de un producto
Campos: ninguno (se abre desde una tarjeta del catálogo).
Validaciones: muestra solo productos visibles; incluye galería, precio en COP, disponibilidad y el emprendimiento con su calificación; el botón «Agregar al carrito» aparece solo si el producto es pedible y el usuario es un estudiante que no es el dueño; registra la visita.
CU16 — Ver la página de inicio
Campos: ninguno.
Validaciones: muestra destacados, más vendidos, mejor calificados y novedades según RN-09; si una sección no tiene datos, se omite.
CU17 — Buscar y filtrar productos
Campos: texto de búsqueda, categoría, tipo, precio mínimo y máximo, orden.
Validaciones: los criterios se combinan; si no hay resultados, se muestra un mensaje claro y la opción de limpiar filtros; 12 resultados por página.
CU18 — Iniciar conversación con un emprendimiento
Campos: emprendimiento, producto de referencia (opcional) y primer mensaje.
Validaciones: reglas de RN-05: no el propio emprendimiento, no emprendimientos no visibles; si ya existe la conversación, se abre la existente.
CU19 — Enviar y leer mensajes
Campos: contenido del mensaje.
Validaciones: no vacío y de hasta 1000 caracteres; solo los participantes acceden; al abrir la conversación los mensajes recibidos pasan a leídos; la vista se actualiza cada 10 segundos.
CU20 — Armar carrito y solicitar pedido
Campos: productos y cantidades, método de pago acordado (efectivo o transferencia), nota de entrega opcional.
Validaciones: el carrito es de un solo emprendimiento y no es el propio; solo productos pedibles; cantidad entera menor o igual al stock; el sistema recalcula precios y total en el servidor; se crea el pedido pendiente.
CU21 — Consultar y cancelar mis pedidos
Campos: pedido seleccionado; acción cancelar.
Validaciones: solo el comprador ve sus pedidos; solo se cancela un pedido pendiente; se muestran el estado y el motivo de rechazo o cancelación si existe; los pedidos entregados muestran el botón «Reseñar».
CU22 — Confirmar o rechazar pedidos recibidos
Campos: pedido pendiente; acción confirmar o rechazar y motivo.
Validaciones: solo el dueño del emprendimiento; confirmar valida el stock y lo descuenta; rechazar exige motivo.
CU23 — Entregar o cancelar un pedido confirmado
Campos: pedido confirmado; acción entregar o cancelar y motivo.
Validaciones: solo el dueño; entregar es definitivo y habilita la reseña; cancelar exige motivo y devuelve el stock.
CU24 — Reseñar un emprendimiento
Campos: puntuación (1 a 5) y comentario opcional.
Validaciones: solo el comprador de un pedido entregado; una reseña por pedido; no se edita; el promedio del emprendimiento se actualiza.
CU25 — Consultar mis métricas
Campos: periodo (7, 30, 90 días o total).
Validaciones: solo el dueño; muestra visitas, pedidos por estado, ventas en COP, calificación promedio y productos más vendidos, calculados al cargar la página.
CU26 — Reportar producto o emprendimiento
Campos: elemento y motivo.
Validaciones: no lo propio; un reporte por usuario y elemento; al alcanzar el umbral, el elemento se oculta.
CU27 — Moderar reportes (administrador)
Campos: elemento reportado y decisión (reactivar, eliminar el producto o suspender el emprendimiento).
Validaciones: solo administradores; se muestran los motivos; se registra quién resolvió y cuándo.
CU28 — Gestionar categorías (administrador)
Campos: nombre, descripción.
Validaciones: nombre único; no se elimina una categoría con productos o emprendimientos asociados.
CU29 — Destacar emprendimientos (administrador)
Campos: emprendimiento y marca de destacado.
Validaciones: solo administradores; solo emprendimientos aprobados y visibles; se muestran hasta FEATURED_LIMIT en el inicio.
CU30 — Suspender o reactivar emprendimientos (administrador)
Campos: emprendimiento y acción, motivo al suspender.
Validaciones: solo administradores; un emprendimiento suspendido no es visible ni opera; los pedidos en curso quedan en su estado y el comprador puede cancelar los pendientes.
CU31 — Gestionar cuentas de usuario (administrador)
Campos: usuario seleccionado, acción (desactivar o reactivar).
Validaciones: solo administradores; un administrador no puede desactivar su propia cuenta; una cuenta desactivada no inicia sesión.
CU32 — Consultar estadísticas globales (administrador)
Campos: ninguno.
Validaciones: muestra usuarios registrados, emprendimientos aprobados y pendientes, productos visibles, pedidos entregados, ventas registradas y reportes pendientes, calculados al cargar la página.
# 6. Historias de Usuario
HU01 — Registro con correo universitario
Como estudiante quiero registrarme con mi correo institucional para demostrar que pertenezco a la comunidad universitaria.
Criterios de aceptación:
- Solo se acepta un correo del dominio universitario; con otro dominio veo un mensaje claro.
- Recibo un correo con un enlace para verificar mi cuenta.
- Hasta que verifique mi correo no puedo pedir, escribir ni activar mi emprendimiento.
HU02 — Inicio de sesión y recuperación de contraseña
Como estudiante quiero iniciar sesión y recuperar mi contraseña si la olvido para no perder el acceso a mi cuenta.
Criterios de aceptación:
- Puedo marcar «recordarme».
- Si mis credenciales son incorrectas, veo un mensaje claro.
- Puedo pedir un enlace de recuperación a mi correo y definir una nueva contraseña.
HU03 — Mi perfil
Como estudiante quiero editar mi nombre, programa y foto para que mis pedidos y mensajes me identifiquen.
Criterios de aceptación:
- Mi correo no aparece en páginas públicas.
- Los emprendedores a quienes les pido solo ven mi nombre y programa.
HU04 — Abrir mi emprendimiento
Como estudiante emprendedor quiero activar mi emprendimiento con su logo y descripción para mostrar lo que vendo a toda la universidad.
Criterios de aceptación:
- Puedo registrar un solo emprendimiento con un nombre único.
- Veo si mi emprendimiento está pendiente, aprobado o rechazado, y el motivo si fue rechazado.
- Puedo corregir y reenviar un emprendimiento rechazado.
HU05 — Aprobar emprendimientos
Como administrador quiero revisar los emprendimientos nuevos para que solo aparezcan tiendas legítimas.
Criterios de aceptación:
- Veo la cola de emprendimientos pendientes con su información.
- Puedo aprobar o rechazar con motivo.
- Queda registrado quién resolvió y cuándo.
HU06 — Dar a conocer mi marca
Como emprendedor quiero que mi emprendimiento tenga una página pública para que más estudiantes me conozcan.
Criterios de aceptación:
- La página muestra logo, descripción, productos, promedio de reseñas y las reseñas.
- Muestro mi WhatsApp o Instagram solo si yo decido publicarlos.
- Puedo pausar mi tienda sin perder mis productos.
HU07 — Publicar un producto o servicio
Como emprendedor quiero publicar mis productos y servicios con fotos y precio para que los compradores sepan qué ofrezco.
Criterios de aceptación:
- Puedo subir de 1 a 5 imágenes (jpg, png o webp de hasta 2 MB).
- Indico tipo, categoría, precio y, si quiero, el stock.
- El producto aparece en el catálogo apenas se crea.
HU08 — Gestionar mi tienda
Como emprendedor quiero editar mis productos y cambiar su estado para mantener el catálogo al día.
Criterios de aceptación:
- Puedo marcar un producto como disponible, agotado o inactivo con una sola acción.
- Un producto sin stock pasa a agotado automáticamente.
- No puedo editar los productos de otros emprendimientos.
HU09 — Encontrar lo que necesito
Como visitante o estudiante quiero buscar y filtrar productos para encontrar rápido lo que necesito.
Criterios de aceptación:
- Puedo buscar por palabra clave y filtrar por categoría, tipo y precio a la vez.
- Puedo ordenar por precio, mejor calificados y más vendidos.
- Si no hay resultados, veo un mensaje y puedo limpiar los filtros.
HU10 — Descubrir emprendimientos
Como visitante quiero ver en el inicio los emprendimientos destacados, los productos más vendidos y los mejor calificados para descubrir buenas opciones.
Criterios de aceptación:
- El inicio muestra destacados, más vendidos, mejor calificados y novedades.
- Veo la calificación de cada emprendimiento.
- Puedo ver todo sin iniciar sesión.
HU11 — Preguntar antes de comprar
Como estudiante quiero escribirle a un emprendimiento para resolver dudas antes de pedir.
Criterios de aceptación:
- No puedo escribirle a mi propio emprendimiento.
- Si ya tengo una conversación con ese emprendimiento, se abre la misma.
- Veo la conversación actualizarse sin recargar la página.
HU12 — Atender a mis clientes
Como emprendedor quiero ver mis conversaciones y cuáles tienen mensajes sin leer para responder a tiempo.
Criterios de aceptación:
- Veo el último mensaje de cada conversación y cuántos no he leído.
- El contador de no leídos se ve en la barra de navegación.
- Al abrir una conversación, sus mensajes quedan marcados como leídos.
HU13 — Hacer un pedido
Como comprador quiero agregar productos al carrito y solicitar el pedido para comprarle a un emprendimiento.
Criterios de aceptación:
- Un carrito solo contiene productos de un emprendimiento.
- Elijo el método de pago (efectivo o transferencia) y puedo dejar una nota de entrega.
- Veo el total y los precios congelados al enviar el pedido.
- No puedo pedir productos agotados ni más unidades que el stock.
HU14 — Seguir mis pedidos
Como comprador quiero ver el estado de mis pedidos para saber cuándo me los entregan.
Criterios de aceptación:
- Veo el historial con el estado de cada pedido y el motivo si fue rechazado o cancelado.
- Puedo cancelar un pedido mientras esté pendiente.
HU15 — Gestionar pedidos recibidos
Como emprendedor quiero confirmar o rechazar los pedidos que recibo para organizar mis ventas.
Criterios de aceptación:
- Veo los pedidos con el nombre y programa del comprador, los ítems, el total y el método de pago.
- Al confirmar, el stock se descuenta; al rechazar, debo indicar un motivo.
- Dos compradores no pueden quedarse con la última unidad al mismo tiempo.
HU16 — Cerrar la venta
Como emprendedor quiero marcar un pedido como entregado para registrar mi venta y habilitar la reseña.
Criterios de aceptación:
- Solo puedo marcar como entregado un pedido confirmado.
- El pedido entregado suma a mis métricas de ventas.
- Puedo cancelar un pedido confirmado con un motivo y el stock se devuelve.
HU17 — Reseñar un emprendimiento
Como comprador quiero calificar al emprendimiento que me entregó mi pedido para ayudar a otros a decidir.
Criterios de aceptación:
- Solo puedo reseñar tras un pedido entregado y una sola vez por pedido.
- Califico de 1 a 5 y puedo agregar un comentario.
- La reseña actualiza el promedio del emprendimiento y no puedo editarla después.
HU18 — Medir mi crecimiento
Como emprendedor quiero ver las visitas, los pedidos y las ventas de mi emprendimiento para saber si estoy creciendo.
Criterios de aceptación:
- Puedo ver los últimos 7, 30 o 90 días, o el total.
- Veo mis productos más vendidos y mi calificación promedio.
- Solo yo veo mis métricas.
HU19 — Reportar contenido
Como estudiante quiero reportar un producto o emprendimiento inapropiado o sospechoso para mantener la plataforma segura.
Criterios de aceptación:
- Debo indicar un motivo.
- No puedo reportar lo mío ni reportar dos veces lo mismo.
HU20 — Moderar contenido
Como administrador quiero revisar lo reportado para decidir si se queda, se elimina o se suspende.
Criterios de aceptación:
- Veo los elementos ocultos con sus reportes y motivos.
- Puedo reactivar, eliminar el producto o suspender el emprendimiento.
- Queda registrado quién resolvió y cuándo.
HU21 — Administrar el catálogo y la comunidad
Como administrador quiero gestionar categorías, destacar emprendimientos y desactivar cuentas para mantener ordenada la plataforma.
Criterios de aceptación:
- Puedo crear, editar y eliminar categorías, pero no eliminar una con elementos asociados.
- Puedo marcar emprendimientos como destacados para que salgan en el inicio.
- Puedo desactivar y reactivar cuentas, pero no la mía.
HU22 — Estadísticas globales
Como administrador quiero ver estadísticas globales para conocer el uso de la plataforma.
Criterios de aceptación:
- Veo usuarios registrados, emprendimientos aprobados y pendientes, productos visibles, pedidos entregados, ventas registradas y reportes pendientes.
# 7. Diagramas de Actividades y de Estados
## 7.1 Registro y verificación del correo
[IMAGEN]
Diagrama de actividades — Registro y verificación
## 7.2 Activación y aprobación del emprendimiento
[IMAGEN]
Diagrama de actividades — Activación del emprendimiento
## 7.3 Del carrito a la reseña
[IMAGEN]
Diagrama de actividades — Pedido y reseña
## 7.4 Estados de un pedido
[IMAGEN]
Diagrama de estados — Ciclo de vida de un pedido
Las transiciones permitidas, quién las ejecuta y su efecto están en la tabla de la regla RN-06.
## 7.5 Reporte y moderación
[IMAGEN]
Diagrama de actividades — Reporte y moderación
# 8. Diseño del Sistema
## 8.1 Arquitectura en Laravel
CampusShop sigue la arquitectura MVC de Laravel. La solicitud entra por las rutas, pasa por los middleware (autenticación, verificación de correo, emprendedor, rol), llega al controlador, que valida con un Form Request, consulta la autorización en una Policy y delega la lógica crítica en un servicio o en los modelos Eloquent; finalmente responde con una vista Blade.
| Capa | Componentes del proyecto |
| Rutas | routes/web.php (públicas, autenticadas, de tienda y de administración); routes/auth.php generado por Breeze. |
| Middleware | auth, verified, throttle, y dos propios: emprendedor (EnsureHasEmprendimiento, exige un emprendimiento aprobado o pausado) y admin (EnsureUserIsAdmin, exige role = admin y is_active). |
| Controladores | Públicos: InicioController, CatalogoController, ProductoController, EmprendimientoController. Comprador: PerfilController, CarritoController, PedidoController, ResenaController, ConversacionController, MensajeController, ReporteController. Emprendedor (Tienda): MiEmprendimientoController, MiTiendaController, PedidoRecibidoController, MetricasController. Administración (Admin): AprobacionController, CategoriaController, DestacadoController, ModeracionController, UsuarioController, EstadisticaController. |
| Form Requests | StoreEmprendimientoRequest, UpdateEmprendimientoRequest, StoreProductoRequest, UpdateProductoRequest, StorePedidoRequest, StoreMensajeRequest, StoreResenaRequest, StoreReporteRequest, UpdatePerfilRequest. |
| Policies | EmprendimientoPolicy, ProductoPolicy (actualizar, eliminar, cambiar estado), PedidoPolicy (ver, cancelar, confirmar, rechazar, entregar), ConversacionPolicy (ver, escribir), ResenaPolicy (crear). |
| Servicios | PedidoService (crear, confirmar, rechazar, entregar y cancelar pedidos dentro de transacciones) y MetricasService (visitas, pedidos, ventas y rankings). |
| Modelos | User, Categoria, Emprendimiento, Producto, ProductoImagen, Conversacion, Mensaje, Pedido, PedidoItem, Resena, Reporte, Visita. |
| Vistas Blade | Layout principal con Bootstrap, parciales (tarjeta de producto, tarjeta de emprendimiento, estrellas de calificación, paginación, filtros) y carpetas por módulo. |
| Configuración | config/campushop.php lee los valores de .env (RNF-11). |
| Datos iniciales | AdminSeeder (primer administrador) y CategoriaSeeder (categorías base, por ejemplo comida, ropa, tecnología, servicios académicos, diseño, belleza y hogar). |

Relaciones principales entre modelos
- User tiene un Emprendimiento (opcional), muchos Pedido como comprador, muchas Conversacion como comprador y muchos Reporte.
- Emprendimiento pertenece a User y a Categoria; tiene muchos Producto, Pedido, Conversacion, Resena y Visita.
- Producto pertenece a Emprendimiento y a Categoria; tiene muchas ProductoImagen.
- Pedido pertenece a un comprador (User) y a un Emprendimiento; tiene muchos PedidoItem y una Resena opcional.
- Reporte apunta a un Producto o a un Emprendimiento mediante una relación polimórfica.
Decisiones de diseño relevantes
- El emprendedor no es un tipo de cuenta aparte: es un User con un Emprendimiento aprobado. Así hay un solo registro, un solo login y un estudiante puede comprar y vender con la misma cuenta.
- Pedidos con copia de datos: pedido_items guarda nombre y precio al momento de la compra; editar o eliminar el producto después no cambia el historial ni las ventas.
- Sin sobreventa: PedidoService confirma el pedido dentro de DB::transaction con lockForUpdate() sobre los productos, valida el stock y lo descuenta antes de guardar el nuevo estado.
- Carrito en sesión: el carrito no se guarda en base de datos; vive en la sesión y se convierte en pedido al enviarlo, por lo que no hay tabla extra.
- Una conversación por emprendimiento y comprador: restricción única en la base de datos y firstOrCreate en el controlador.
- Chat con actualización periódica: la vista consulta GET /conversaciones/{id}/mensajes?despues={id} cada 10 segundos; es más simple que WebSockets y suficiente para la versión base.
- Visibilidad centralizada: los scopes visibles() de Emprendimiento y Producto encapsulan RN-12 y se reutilizan en inicio, catálogo, búsqueda y páginas públicas.
- Rankings calculados por consulta: promedio y ventas se obtienen con withAvg, withCount y agregaciones sobre las tablas existentes; no se duplican contadores que puedan desincronizarse.
- Dominio universitario: una regla de validación personalizada compara el final del correo con config('campushop.university_domain').

## 8.2 Modelo de Datos
[IMAGEN]
Modelo entidad-relación — CampusShop (versión simplificada: claves y campos principales)
El diagrama muestra solo las claves y los campos principales de cada tabla; el detalle completo de columnas está en el diccionario de datos. Las líneas punteadas indican relaciones con users (comprador o autor del reporte); reportes apunta a productos o emprendimientos con una relación polimórfica (reportable_type, reportable_id). Además de estas 12 tablas, Laravel crea sus tablas estándar (password_reset_tokens, sessions, cache, jobs, migrations).

Diccionario de datos
users
| Campo | Tipo | Restricciones / notas |
| id | bigint | PK, autoincremental |
| name | varchar(100) | obligatorio |
| email | varchar(150) | obligatorio, único |
| email_verified_at | timestamp | nulo hasta verificar |
| password | varchar(255) | hash |
| role | enum('user','admin') | por defecto 'user' |
| program | varchar(120) | programa o facultad |
| avatar | varchar(255) | ruta de la foto, nulo |
| is_active | boolean | por defecto verdadero |
| remember_token, created_at, updated_at | — | estándar de Laravel |
| deleted_at | timestamp | soft-delete |

categorias
| Campo | Tipo | Restricciones / notas |
| id | bigint | PK |
| nombre | varchar(80) | obligatorio, único |
| slug | varchar(100) | único |
| descripcion | varchar(255) | nulo |
| created_at, updated_at | timestamp | — |

emprendimientos
| Campo | Tipo | Restricciones / notas |
| id | bigint | PK |
| user_id | bigint | FK → users, único (un emprendimiento por estudiante) |
| categoria_id | bigint | FK → categorias (rubro) |
| nombre | varchar(80) | obligatorio, único |
| slug | varchar(100) | único |
| descripcion | text | obligatorio |
| logo | varchar(255) | obligatorio, ruta |
| banner | varchar(255) | nulo |
| whatsapp | varchar(20) | contacto público opcional, nulo |
| instagram | varchar(60) | contacto público opcional, nulo |
| estado | enum('pendiente','aprobado','rechazado','pausado','suspendido') | por defecto 'pendiente'; índice |
| motivo_rechazo | varchar(300) | nulo |
| revisado_por | bigint | FK → users, nulo |
| revisado_at | timestamp | nulo |
| destacado | boolean | por defecto falso |
| oculta | boolean | por defecto falso (ocultamiento por reportes) |
| created_at, updated_at, deleted_at | timestamp | soft-delete |

productos
| Campo | Tipo | Restricciones / notas |
| id | bigint | PK |
| emprendimiento_id | bigint | FK → emprendimientos |
| categoria_id | bigint | FK → categorias |
| nombre | varchar(120) | obligatorio |
| descripcion | text | obligatorio |
| tipo | enum('producto','servicio') | por defecto 'producto' |
| precio | decimal(10,2) | mayor que 0, en COP |
| stock | int unsigned | nulo = sin control de inventario |
| estado | enum('disponible','agotado','inactivo') | por defecto 'disponible'; índice junto con oculto |
| oculto | boolean | por defecto falso |
| created_at, updated_at, deleted_at | timestamp | soft-delete |

producto_imagenes
| Campo | Tipo | Restricciones / notas |
| id | bigint | PK |
| producto_id | bigint | FK → productos, eliminación en cascada |
| ruta | varchar(255) | archivo en el almacenamiento público |
| orden | tinyint | 1 a 5 |

conversaciones
| Campo | Tipo | Restricciones / notas |
| id | bigint | PK |
| emprendimiento_id | bigint | FK → emprendimientos |
| comprador_id | bigint | FK → users |
| producto_id | bigint | FK → productos, nulo (producto de contexto) |
| created_at, updated_at | timestamp | — |
| — | — | UNIQUE (emprendimiento_id, comprador_id) |

mensajes
| Campo | Tipo | Restricciones / notas |
| id | bigint | PK |
| conversacion_id | bigint | FK → conversaciones, cascada; índice (conversacion_id, id) |
| emisor_id | bigint | FK → users |
| contenido | varchar(1000) | obligatorio |
| leido_at | timestamp | nulo mientras no se lea |
| created_at, updated_at | timestamp | — |

pedidos
| Campo | Tipo | Restricciones / notas |
| id | bigint | PK |
| comprador_id | bigint | FK → users |
| emprendimiento_id | bigint | FK → emprendimientos; índice (emprendimiento_id, estado) |
| estado | enum('pendiente','confirmado','entregado','rechazado','cancelado') | por defecto 'pendiente' |
| metodo_pago | enum('efectivo','transferencia') | acordado por fuera de la plataforma |
| nota | varchar(300) | nota de entrega, nulo |
| motivo | varchar(300) | motivo de rechazo o cancelación, nulo |
| total | decimal(12,2) | suma de los ítems, calculada en el servidor |
| confirmado_at, entregado_at | timestamp | nulos hasta que ocurra |
| created_at, updated_at | timestamp | — |

pedido_items
| Campo | Tipo | Restricciones / notas |
| id | bigint | PK |
| pedido_id | bigint | FK → pedidos, eliminación en cascada |
| producto_id | bigint | FK → productos (incluye eliminados lógicamente) |
| nombre_producto | varchar(120) | copia del nombre al comprar |
| precio_unitario | decimal(10,2) | copia del precio al comprar |
| cantidad | smallint unsigned | mínimo 1 |

resenas
| Campo | Tipo | Restricciones / notas |
| id | bigint | PK |
| pedido_id | bigint | FK → pedidos, único (una reseña por pedido) |
| comprador_id | bigint | FK → users |
| emprendimiento_id | bigint | FK → emprendimientos; índice |
| puntuacion | tinyint | entre 1 y 5 |
| comentario | varchar(500) | nulo |
| created_at | timestamp | — |

reportes
| Campo | Tipo | Restricciones / notas |
| id | bigint | PK |
| user_id | bigint | FK → users (quien reporta) |
| reportable_type | enum('producto','emprendimiento') | tipo de elemento reportado |
| reportable_id | bigint | id del elemento (relación polimórfica) |
| motivo | varchar(300) | obligatorio |
| estado | enum('pendiente','descartado','aceptado') | por defecto 'pendiente' |
| revisado_por | bigint | FK → users, nulo |
| revisado_at | timestamp | nulo |
| — | — | UNIQUE (user_id, reportable_type, reportable_id) |

visitas
| Campo | Tipo | Restricciones / notas |
| id | bigint | PK |
| emprendimiento_id | bigint | FK → emprendimientos |
| producto_id | bigint | FK → productos, nulo si es la página del emprendimiento |
| user_id | bigint | FK → users, nulo si es visitante |
| sesion | varchar(64) | hash de la sesión, para contar una visita por sesión, página y día |
| fecha | date | índice (emprendimiento_id, fecha) |

## 8.3 Rutas Principales
| Método | URI | Acción | Acceso |
| GET | / | Inicio con destacados y rankings | Público |
| GET | /productos, /productos/{id} | Catálogo con filtros y detalle | Público |
| GET | /emprendimientos, /emprendimientos/{slug} | Listado y página pública | Público |
| GET/POST | /register, /login, /forgot-password | Registro, acceso, recuperación (Breeze) | Invitado |
| GET | /email/verify/{id}/{hash} | Verificar correo | Firmado |
| GET/PUT | /perfil | Ver y editar el perfil propio | auth, verified |
| GET/POST | /mi-emprendimiento/crear | Activar emprendimiento | auth, verified |
| PUT/PATCH | /mi-emprendimiento, /mi-emprendimiento/pausa | Editar, pausar y reactivar | auth, verified, emprendedor |
| GET | /mi-tienda | Panel de productos propios | auth, verified, emprendedor |
| POST/PUT/DELETE | /mi-tienda/productos, /mi-tienda/productos/{id} | Crear, editar y eliminar productos | auth, verified, emprendedor, policy |
| PATCH | /mi-tienda/productos/{id}/estado | Disponible / agotado / inactivo | auth, verified, emprendedor, policy |
| GET | /mi-tienda/metricas | Panel de métricas | auth, verified, emprendedor |
| GET | /mi-tienda/pedidos, /mi-tienda/pedidos/{id} | Pedidos recibidos | auth, verified, emprendedor, policy |
| PATCH | /mi-tienda/pedidos/{id}/{accion} | Confirmar, rechazar, entregar o cancelar | auth, verified, emprendedor, policy |
| GET/POST/DELETE | /carrito, /carrito/items | Ver, agregar y quitar ítems del carrito | auth, verified |
| POST | /pedidos | Solicitar pedido | auth, verified, throttle |
| GET | /mis-pedidos, /mis-pedidos/{id} | Pedidos del comprador | auth, verified, policy |
| PATCH | /mis-pedidos/{id}/cancelar | Cancelar pedido pendiente | auth, verified, policy |
| POST | /pedidos/{id}/resena | Reseñar un pedido entregado | auth, verified, policy |
| POST | /emprendimientos/{id}/conversacion | Iniciar o abrir conversación | auth, verified |
| GET | /conversaciones | Bandeja de conversaciones | auth, verified |
| GET/POST | /conversaciones/{id}/mensajes | Leer (con despues) y enviar mensajes | auth, verified, policy, throttle |
| POST | /reportes | Reportar producto o emprendimiento | auth, verified |
| * | /admin/emprendimientos, /admin/categorias, /admin/destacados, /admin/reportes, /admin/usuarios, /admin/estadisticas | Módulo de administración | auth, verified, admin |

## 8.4 Seguridad
| Riesgo | Control aplicado |
| Registro de personas ajenas a la universidad | Validación del dominio del correo y verificación por enlace firmado (RN-01). |
| Robo o adivinación de contraseñas | Hash de contraseñas, límite de 5 intentos por minuto, mensajes que no revelan si el correo existe (RNF-02, RNF-13). |
| Acceso a recursos ajenos (productos, pedidos, chats, métricas) | Policies en el servidor; los pedidos solo los ven su comprador y el dueño del emprendimiento; las conversaciones, solo sus dos participantes (RNF-05). |
| Escalada a funciones de administrador o de tienda | Middleware admin y emprendedor en las rutas correspondientes; el rol y el estado del emprendimiento se guardan solo en el servidor. |
| Manipulación de precios o totales desde el formulario | El formulario envía solo ids y cantidades; el servidor lee los precios de la base de datos, calcula el total y lo congela en pedido_items (RNF-16). |
| Sobreventa de la última unidad | Transacción con bloqueo de filas (lockForUpdate) al confirmar el pedido y validación de stock (RNF-16). |
| Tiendas fraudulentas o engañosas | Aprobación previa del administrador, reportes de usuarios, ocultamiento automático y suspensión (RN-02, RN-11). |
| Reseñas y ventas inflados con cuentas aliadas | Solo se reseña un pedido entregado y una vez; un emprendedor no puede pedirse a sí mismo; límite de 10 pedidos por hora; los rankings exigen un mínimo de 3 reseñas; reportes y suspensión de emprendimientos sospechosos. El riesgo no desaparece del todo mientras no haya pago en línea verificable (ver 2.6). |
| Inyección SQL | Eloquent y consultas parametrizadas; nunca se concatena entrada del usuario en SQL (RNF-04). |
| XSS en nombres, descripciones, reseñas y mensajes | Escape de salida con {{ }} de Blade; no se usa {!! !!} con contenido de usuarios. |
| CSRF | Token CSRF en formularios y en las peticiones de actualización del chat (RNF-03). |
| Archivos maliciosos subidos como imagen | Validación del tipo MIME real, tamaño máximo, nombre aleatorio y almacenamiento fuera de las rutas de código (RNF-12). |
| Spam en el chat y en los pedidos | Límite de 30 mensajes por minuto, de 1000 caracteres por mensaje y de 10 pedidos por hora. |
| Abuso del sistema de reportes | Un reporte por usuario y elemento; reactivación por parte del administrador (RN-11). |
| Exposición de datos personales | El correo no se muestra en páginas públicas; el contacto del emprendimiento es opcional y lo decide el emprendedor; el emprendedor ve del comprador solo nombre y programa (sección 3.3). |
| Pérdida de historial | Soft-delete, copia de datos en los pedidos y claves foráneas que impiden borrar datos con dependencias (RNF-06, RNF-07). |

## 8.5 Parámetros de Configuración
| Variable .env | Valor por defecto | Uso |
| UNIVERSITY_EMAIL_DOMAIN | (dominio de la institución) | Dominio válido para registrarse (RN-01) |
| REQUIRE_APPROVAL | true | Exige aprobación del administrador a los emprendimientos nuevos (RN-02) |
| MAX_IMAGES_PER_PRODUCT | 5 | Máximo de imágenes por producto (RN-10) |
| MAX_IMAGE_SIZE_KB | 2048 | Tamaño máximo por imagen (RN-10) |
| REPORTS_THRESHOLD | 3 | Reportes pendientes para ocultar un elemento (RN-11) |
| CHAT_POLL_SECONDS | 10 | Intervalo de actualización del chat (RF-33) |
| PAGE_SIZE | 12 | Elementos por página (RF-14, RF-23) |
| FEATURED_LIMIT | 8 | Emprendimientos destacados en el inicio (RN-09) |
| TOP_SELLING_DAYS | 30 | Ventana de los más vendidos (RN-09) |
| MIN_REVIEWS_FOR_RANKING | 3 | Reseñas mínimas para el ranking de mejor calificados (RN-09) |
| MAX_ORDERS_PER_HOUR | 10 | Pedidos por usuario y hora (RNF-13) |
| SESSION_LIFETIME | 120 | Minutos de inactividad antes de expirar la sesión (RNF-14) |
| APP_TIMEZONE / APP_LOCALE | America/Bogota / es | Zona horaria e idioma (RNF-20) |

## 8.6 Cálculo de Métricas y Rankings
Todas las métricas se calculan al consultar, a partir de las tablas existentes, para que no puedan desincronizarse. Los periodos usan la fecha indicada en RN-08.
| Métrica | Definición | Origen de los datos |
| Visitas | Número de filas de visitas del emprendimiento (o producto) en el periodo. | visitas |
| Pedidos por estado | Conteo de pedidos del emprendimiento agrupado por estado, según created_at. | pedidos |
| Ventas (COP) | Suma de precio_unitario × cantidad de los ítems de pedidos entregado según entregado_at. | pedidos, pedido_items |
| Productos más vendidos | Unidades (SUM(cantidad)) por producto en pedidos entregado, en orden descendente. | pedido_items, pedidos |
| Calificación promedio | Media de puntuacion de las reseñas del emprendimiento, a un decimal, con su cantidad. | resenas |
| Ranking «mejor calificados» | Emprendimientos visibles con al menos 3 reseñas, por promedio y cantidad. | resenas, emprendimientos |
| Ranking «más vendidos» | Productos visibles con más unidades entregadas en los últimos 30 días. | pedido_items, pedidos, productos |

# 9. Entrevista y Tabulación de Resultados
Esta sección es un ejemplo de cómo documentar el levantamiento de requisitos. Antes de entregar el documento, reemplaza las respuestas con las de una entrevista real a estudiantes emprendedores, a compradores y al docente que haga las veces de cliente.
## 9.1 Entrevista
1. ¿Qué problema busca resolver esta plataforma?
Muchos estudiantes tienen un emprendimiento (comida, ropa, tecnología, diseño, servicios académicos) pero lo promocionan solo por redes sociales y de boca en boca; la plataforma les da una vitrina en la universidad, un lugar donde recibir pedidos y una reputación visible para vender más y ser más reconocidos.
2. ¿Quiénes usarán el sistema?
Estudiantes emprendedores que venden, estudiantes que compran, y un administrador que modera.
3. ¿Cómo se debe verificar que el usuario es estudiante?
Con un correo institucional: solo se acepta el dominio de la universidad y la cuenta se activa al confirmar el enlace enviado al correo.
4. ¿Qué información debe tener cada producto o servicio?
Nombre, descripción, tipo, categoría, precio, fotos (hasta cinco) y, si el emprendedor quiere, las unidades disponibles.
5. ¿Cómo se concreta una venta?
El comprador solicita el pedido en la plataforma, el emprendedor lo confirma y se acuerdan el pago (efectivo o transferencia) y la entrega fuera de la plataforma. El emprendedor marca el pedido como entregado. Los pagos en línea quedan para una versión futura.
6. ¿Cómo se genera confianza entre desconocidos?
Con reseñas de compradores reales: solo quien recibió un pedido puede calificar el emprendimiento, y el promedio se ve en la página, el catálogo y el detalle de los productos.
7. ¿Cómo ayuda la plataforma a que un emprendimiento sea más reconocido?
Con una página propia, destacados en el inicio, rankings de más vendidos y mejor calificados, y un panel de métricas para ver visitas, pedidos y ventas.
8. ¿Qué pasa con las tiendas o productos inapropiados?
Los usuarios pueden reportarlos; si llegan a cierto número de reportes se ocultan y un administrador decide. Además, los emprendimientos nuevos se aprueban antes de ser públicos.
9. ¿Desde qué dispositivo se usará principalmente?
Desde el celular, por lo que la versión web debe verse bien en pantallas pequeñas.
10. ¿Qué se espera como mejora futura?
Pagos en línea, una aplicación móvil, notificaciones por correo y chat en tiempo real.
## 9.2 Tabulación de Resultados
Si aplicas una encuesta corta a tus compañeros, puedes tabular preguntas como las siguientes y presentar los resultados con gráficos de barras o de pastel:
- ¿Tienes o te gustaría tener un emprendimiento? (Sí / No / Tal vez)
- ¿Cómo vendes o promocionas hoy tus productos o servicios? (redes sociales, voz a voz, otro)
- ¿Le comprarías a un emprendimiento de tu universidad? (Sí / No / Tal vez)
- ¿Qué tipo de productos o servicios comprarías con más frecuencia? (comida, ropa y accesorios, tecnología, servicios académicos, diseño, otros)
- ¿Qué tanto confiarías en un emprendimiento con reseñas visibles? (escala de 1 a 5)
- ¿Qué forma de pago preferirías? (efectivo, transferencia, pago en línea)
- ¿Desde qué dispositivo la usarías principalmente? (celular / computador)
## 9.3 Decisiones de Diseño Pendientes de Validación
Mientras no se confirmen con el docente o los usuarios, el documento adopta las siguientes decisiones; los valores numéricos están en configuración, por lo que pueden ajustarse sin cambiar la estructura de los requerimientos.
| Pregunta abierta | Decisión adoptada en esta versión |
| ¿Cómo se concreta la venta? | Opción B: pedido dentro de la plataforma; pago y entrega por fuera. Los pagos en línea (opción C) quedan como mejora futura. |
| ¿Cuántos emprendimientos puede tener un estudiante? | Uno solo en esta versión. |
| ¿Los emprendimientos nuevos requieren aprobación? | Sí, del administrador; se puede desactivar con REQUIRE_APPROVAL. |
| ¿El catálogo es visible sin iniciar sesión? | Sí; chatear, pedir, reseñar y reportar exigen sesión. |
| ¿Qué dominio de correo se acepta? | El de la institución, configurado en UNIVERSITY_EMAIL_DOMAIN. |
| ¿Cuándo se descuenta el stock? | Al confirmar el pedido, no al solicitarlo. |
| ¿Se muestran datos de contacto del emprendimiento? | Solo los que el emprendedor decide publicar (WhatsApp, Instagram); el correo nunca. |
| ¿Se pueden editar las reseñas? | No; una vez enviadas son definitivas. |
| ¿Quién decide los destacados? | El administrador; los rankings de más vendidos y mejor calificados son automáticos. |
| ¿Cuántos reportes ocultan un elemento? | 3 reportes pendientes, configurable. |
| ¿El chat funciona en tiempo real? | Casi: se actualiza cada 10 segundos; los WebSockets quedan como mejora futura. |
| ¿Cuántas imágenes y de qué tamaño? | Hasta 5 por producto, de 2 MB cada una. |

# 10. Manual de Instalación
## 10.1 Requisitos del Sistema
- PHP 8.2 o superior.
- Composer 2.x.
- MySQL 8 (incluido en Laragon).
- Node.js 18+ y npm, para compilar los assets con Vite.
- Visual Studio Code.
- Git (recomendado).
- Un servidor de correo local o de pruebas (por ejemplo Mailpit o Mailtrap) para la verificación y la recuperación de contraseña.
## 10.2 Instalación del Proyecto
- Preparar el entorno. Instalar Laragon, Composer y Git; arrancar Apache y MySQL desde Laragon.
- Crear el proyecto. En la carpeta de proyectos de Laragon: composer create-project laravel/laravel campushop.
- Configurar el entorno. Editar .env: DB_DATABASE=campushop, DB_USERNAME=root, DB_PASSWORD= (vacío en Laragon), APP_LOCALE=es, APP_TIMEZONE=America/Bogota, y los datos del servidor de correo (MAIL_*).
- Parámetros de negocio. Agregar en .env los valores de la sección 8.5 (UNIVERSITY_EMAIL_DOMAIN, REPORTS_THRESHOLD, etc.) y crear config/campushop.php que los lea.
- Crear la base de datos. Crear campushop en HeidiSQL o phpMyAdmin.
- Instalar la autenticación. composer require laravel/breeze --dev y php artisan breeze:install blade. Hacer que el modelo User implemente MustVerifyEmail.
- Migraciones y datos iniciales. php artisan migrate --seed crea las tablas, las categorías base y el primer administrador. Cambiar la contraseña del administrador antes de usar el sistema en producción.
- Enlazar el almacenamiento público. php artisan storage:link, para que las imágenes de logos y productos sean accesibles desde el navegador.
- Compilar los assets. npm install y npm run dev (o npm run build).
- Levantar el servidor. php artisan serve o el dominio automático de Laragon (http://campushop.test).
## 10.3 Pruebas Básicas de Instalación
- Registrar un usuario con un correo del dominio universitario y confirmar que llega el correo de verificación.
- Iniciar sesión con el administrador creado por el seeder y abrir el panel de administración.
- Activar un emprendimiento, aprobarlo desde el panel y publicar un producto con imagen; confirmar que aparece en el catálogo.
## 10.4 Plan de Pruebas
Pruebas funcionales que se recomienda automatizar con PHPUnit o Pest (php artisan test) y repetir de forma manual antes de la entrega.
| ID | Caso de prueba | Resultado esperado |
| P01 | Registro con correo de otro dominio | Se rechaza con mensaje de error (RN-01). |
| P02 | Registro con correo ya existente | Se rechaza por correo duplicado. |
| P03 | Activar emprendimiento sin verificar el correo | Se redirige a la pantalla de verificación (RNF-01). |
| P04 | Inicio de sesión con cuenta desactivada | Se rechaza el acceso (RF-05). |
| P05 | Un estudiante intenta crear un segundo emprendimiento | Se rechaza (RN-02). |
| P06 | Crear un producto con un emprendimiento pendiente | Se rechaza; solo los aprobados publican (RN-02). |
| P07 | Subir una imagen de 3 MB o en formato pdf | Se rechaza por tamaño o formato (RN-10). |
| P08 | Un usuario intenta editar el producto de otro emprendimiento (URL directa) | Responde 403 (RN-04). |
| P09 | Buscar por palabra clave combinada con categoría, tipo y rango de precio | Devuelve solo las coincidencias de todos los criterios. |
| P10 | Ver el catálogo con un producto inactivo y otro agotado | El inactivo no aparece; el agotado aparece sin botón de pedido (RN-03). |
| P11 | Escribirle al propio emprendimiento | Se impide (RN-05). |
| P12 | Iniciar dos veces conversación con el mismo emprendimiento | Se reutiliza la conversación existente. |
| P13 | Un tercer usuario intenta abrir la conversación de otros | Responde 403 (RN-05). |
| P14 | Pedir un producto del propio emprendimiento | Se impide (RN-06). |
| P15 | Pedir más unidades que el stock | Se rechaza con mensaje claro (RN-06). |
| P16 | Agregar al carrito productos de dos emprendimientos distintos | Se impide; el carrito es de un solo emprendimiento (RN-06). |
| P17 | Manipular el precio en el formulario del pedido | Se ignora; el total usa el precio de la base de datos (RNF-16). |
| P18 | Dos compradores piden la última unidad y el emprendedor confirma ambos pedidos | El primero se confirma; el segundo falla por stock insuficiente (RNF-16). |
| P19 | Confirmar un pedido | El stock se descuenta; si llega a 0, el producto pasa a agotado (RF-21). |
| P20 | Cancelar un pedido confirmado | El pedido queda cancelado y el stock se devuelve (RF-44). |
| P21 | Cambiar el precio de un producto después de un pedido | El pedido conserva el precio original (RN-06). |
| P22 | Reseñar un pedido que no está entregado | Se rechaza (RN-07). |
| P23 | Reseñar dos veces el mismo pedido | La segunda reseña se rechaza (RN-07). |
| P24 | Un usuario intenta reseñar el pedido de otro | Responde 403 (RN-07). |
| P25 | Marcar pedidos como entregados y abrir el panel de métricas | Las ventas y los productos más vendidos reflejan solo pedidos entregados (RN-08). |
| P26 | El dueño abre la página de su propio emprendimiento | No se registra visita (RN-08). |
| P27 | Emprendimiento con 2 reseñas frente a otro con 3 | El de 2 no entra al ranking de mejor calificados (RN-09). |
| P28 | Tres usuarios reportan el mismo producto | El producto se oculta del catálogo (RN-11). |
| P29 | Un estudiante intenta abrir /admin/reportes | Responde 403 (RNF-01). |
| P30 | Enviar 31 mensajes en un minuto | El mensaje 31 se rechaza por límite (RNF-13). |

# 11. Manual de Usuario
## 11.1 Descripción General
CampusShop es la tienda virtual de los emprendimientos de la universidad: los estudiantes pueden descubrir productos y servicios de otros estudiantes, escribirles, hacer pedidos y calificarlos; y los estudiantes emprendedores pueden abrir su tienda, publicar lo que venden, atender pedidos y medir su crecimiento.
## 11.2 Funcionalidades por Módulo
Cuenta y perfil
Registro con el correo de la universidad, verificación por enlace, inicio y cierre de sesión, recuperación de contraseña y edición del perfil (nombre, programa y foto).
Explorar y buscar
Sin necesidad de iniciar sesión puedes ver el inicio (destacados, más vendidos y mejor calificados), el catálogo y las páginas de los emprendimientos. Busca por palabra clave y filtra por categoría, tipo y precio; ordena por precio, mejor calificados o más vendidos. Los resultados se muestran de a 12.
Chat
Desde la página de un emprendimiento o de un producto puedes escribirle antes de pedir. La bandeja muestra tus conversaciones y cuáles tienen mensajes sin leer; la conversación se actualiza sola cada pocos segundos.
Pedidos
Agrega productos al carrito (de un solo emprendimiento), elige cómo pagarás (efectivo o transferencia) y envía el pedido. En «Mis pedidos» ves su estado y puedes cancelarlo mientras esté pendiente. El pago y la entrega los acuerdas directamente con el emprendedor.
Reseñas
Cuando recibes tu pedido y el emprendedor lo marca como entregado, puedes calificar el emprendimiento de 1 a 5 y dejar un comentario.
Mi emprendimiento
Activa tu emprendimiento con nombre, descripción, logo y, si quieres, tu WhatsApp o Instagram. Cuando el administrador lo apruebe, desde «Mi tienda» publicas productos y servicios con fotos, precio y stock, cambias su estado (disponible, agotado o inactivo), pausas tu tienda cuando lo necesites y atiendes tus pedidos y conversaciones.
Pedidos recibidos y métricas
En «Pedidos recibidos» confirmas o rechazas cada pedido y lo marcas como entregado cuando lo entregas. En «Mis métricas» ves tus visitas, pedidos, ventas y productos más vendidos de los últimos 7, 30 o 90 días.
Reportes y administración
Cualquier estudiante puede reportar un producto o emprendimiento inapropiado. El administrador aprueba emprendimientos nuevos, revisa los reportes, destaca emprendimientos, gestiona categorías y cuentas, y consulta estadísticas.
## 11.3 Flujo Típico de Uso — Comprador
- Registrarse con el correo universitario y verificar la cuenta con el enlace recibido.
- Explorar el inicio o buscar en el catálogo con los filtros.
- Abrir la página del emprendimiento y leer sus reseñas.
- Escribirle al emprendimiento si hay dudas.
- Agregar productos al carrito y enviar el pedido, eligiendo el método de pago.
- Acordar la entrega con el emprendedor y recibir el pedido.
- Cuando el pedido figure como entregado, dejar una reseña.
- Reportar cualquier producto o emprendimiento sospechoso.
## 11.4 Flujo Típico de Uso — Emprendedor
- Registrarse, verificar la cuenta y activar el emprendimiento; esperar la aprobación.
- Publicar productos y servicios con buenas fotos, precio y stock.
- Compartir el enlace de la página del emprendimiento para recibir visitas.
- Revisar los pedidos recibidos y confirmarlos o rechazarlos con un motivo.
- Responder las conversaciones de los compradores.
- Entregar el pedido, cobrar por fuera de la plataforma y marcarlo como entregado.
- Revisar las métricas y las reseñas para mejorar y mantenerse en los rankings.