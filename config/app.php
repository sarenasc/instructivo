<?php
/**
 * ===========================================
 * CONFIGURACIÓN GENERAL DEL SISTEMA
 * ===========================================
 * Punto central de configuración para toda la aplicación
 */

// ===========================================
// RUTAS DEL SISTEMA
// ===========================================
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('SRC_PATH', ROOT_PATH . '/src');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('LOGS_PATH', STORAGE_PATH . '/logs');
define('UPLOADS_PATH', STORAGE_PATH . '/uploads');

// ===========================================
// URLS DEL SISTEMA
// ===========================================
define('APP_URL', $_ENV['APP_URL'] ?? 'http://192.168.19.4/instructivo');
define('ASSETS_URL', APP_URL . '/app/assets');

// ===========================================
// CONFIGURACIÓN DE LA APLICACIÓN
// ===========================================
define('APP_NAME', 'Sistema de Instructivos - AgroIndustrial Almahue');
define('APP_VERSION', '2.0.0');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));

// ===========================================
// CONFIGURACIÓN DE SESIÓN
// ===========================================
define('SESSION_LIFETIME', (int)($_ENV['PHP_SESSION_LIFETIME'] ?? 3600));
define('SESSION_NAME', 'INSTRUCTIVO_SESSION');

// ===========================================
// CONFIGURACIÓN DE SEGURIDAD
// ===========================================
define('PASSWORD_HASH_COST', 12);
define('CSRF_TOKEN_NAME', 'csrf_token');

// ===========================================
// CONFIGURACIÓN DE BASE DE DATOS
// ===========================================
define('DB_SERVER', $_ENV['DB_SERVER'] ?? '192.168.19.4');
define('DB_DATABASE', $_ENV['DB_DATABASE'] ?? 'SistGestion');
define('DB_USER', $_ENV['DB_USER'] ?? 'sa');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');

// ===========================================
// CONFIGURACIÓN DE ARCHIVOS
// ===========================================
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['pdf', 'xlsx', 'xls', 'doc', 'docx', 'jpg', 'jpeg', 'png']);

// ===========================================
// CONFIGURACIÓN DE LOGS
// ===========================================
define('LOG_LEVEL', APP_DEBUG ? 'DEBUG' : 'ERROR');
define('LOG_RETENTION_DAYS', 30);

// ===========================================
// TIMEZONE
// ===========================================
date_default_timezone_set('America/Santiago'); // Santiago, Chile (GMT-3)

// ===========================================
// INICIALIZACIÓN
// ===========================================

// Crear directorios si no existen
$directories = [
    STORAGE_PATH,
    LOGS_PATH,
    UPLOADS_PATH,
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Limpiar logs antiguos (una vez por día)
$last_clean = file_get_contents(STORAGE_PATH . '/.last_log_clean') ?: '0';
if (time() - (int)$last_clean > 86400) {
    cleanOldLogs(LOG_RETENTION_DAYS);
    file_put_contents(STORAGE_PATH . '/.last_log_clean', time());
}

// Manejo de errores
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Log de errores de PHP
ini_set('log_errors', 1);
ini_set('error_log', LOGS_PATH . '/php_errors.log');

// ===========================================
// AUTOLOAD DE FUNCIONES
// ===========================================
require_once SRC_PATH . '/Helpers.php';
