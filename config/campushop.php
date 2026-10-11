<?php

/**
 * Parámetros de negocio de CampusShop (SRS sección 8.5 - RNF-11).
 * Todos los valores vienen de .env y no están fijos en el código.
 */
return [
    // Dominio universitario permitido para el registro (RN-01)
    'university_domain' => env('UNIVERSITY_EMAIL_DOMAIN', 'unimisionpaz.edu.co'),

    // ¿Exigir aprobación del administrador a los emprendimientos nuevos? (RN-02)
    'require_approval' => (bool) env('REQUIRE_APPROVAL', true),

    // Máximo de imágenes por producto (RN-10)
    'max_images_per_product' => (int) env('MAX_IMAGES_PER_PRODUCT', 5),

    // Tamaño máximo por imagen en KB (RN-10)
    'max_image_size_kb' => (int) env('MAX_IMAGE_SIZE_KB', 2048),

    // Reportes pendientes que ocultan un elemento del público (RN-11)
    'reports_threshold' => (int) env('REPORTS_THRESHOLD', 3),

    // Intervalo de actualización del chat en segundos (RF-33)
    'chat_poll_seconds' => (int) env('CHAT_POLL_SECONDS', 10),

    // Elementos por página (RF-14, RF-23)
    'page_size' => (int) env('PAGE_SIZE', 12),

    // Emprendimientos destacados mostrados en el inicio (RN-09)
    'featured_limit' => (int) env('FEATURED_LIMIT', 8),

    // Ventana en días de los productos más vendidos (RN-09)
    'top_selling_days' => (int) env('TOP_SELLING_DAYS', 30),

    // Reseñas mínimas para entrar al ranking de mejor calificados (RN-09)
    'min_reviews_for_ranking' => (int) env('MIN_REVIEWS_FOR_RANKING', 3),

    // Pedidos por usuario y hora (RNF-13)
    'max_orders_per_hour' => (int) env('MAX_ORDERS_PER_HOUR', 10),

    // Mensajes por usuario y minuto (RNF-13)
    'max_messages_per_minute' => (int) env('MAX_MESSAGES_PER_MINUTE', 30),

    // Intentos de inicio de sesión por minuto (RNF-13)
    'max_login_attempts' => (int) env('MAX_LOGIN_ATTEMPTS', 5),
];