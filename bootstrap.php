<?php
/**
 * ===========================================
 * BOOTSTRAP DE LA APLICACIÓN
 * ===========================================
 * Este archivo inicializa toda la aplicación
 * Incluirlo al inicio de cada script PHP
 * 
 * Uso:
 *   require_once __DIR__ . '/bootstrap.php';
 */

// ===========================================
// 1. CARGAR VARIABLES DE ENTORNO
// ===========================================
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// ===========================================
// 2. CONFIGURACIÓN GENERAL
// ===========================================
require_once __DIR__ . '/app.php';

// ===========================================
// 3. CONEXIÓN A BASE DE DATOS
// ===========================================
require_once __DIR__ . '/database.php';

// ===========================================
// 4. FUNCIONES DE AUTENTICACIÓN
// ===========================================
require_once __DIR__ . '/auth.php';

// ===========================================
// 5. INICIALIZAR SESIÓN
// ===========================================
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Cambiar a 1 si usas HTTPS
    session_name(SESSION_NAME);
    session_start();
}

// ===========================================
// 6. MANEJO DE ERRORES GLOBAL
// ===========================================
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    $error_message = "[$severity] $message in $file on line $line";
    writeLog($error_message, 'ERROR');
    
    if (APP_DEBUG) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    
    return true;
});

set_exception_handler(function($exception) {
    writeLog("Unhandled exception: " . $exception->getMessage(), 'CRITICAL');
    
    if (APP_DEBUG) {
        echo "<pre>";
        echo "Exception: " . $exception->getMessage() . "\n";
        echo "File: " . $exception->getFile() . "\n";
        echo "Line: " . $exception->getLine() . "\n";
        echo "\nStack trace:\n" . $exception->getTraceAsString();
        echo "</pre>";
    } else {
        http_response_code(500);
        echo "Error interno del servidor. Por favor contacta al administrador.";
    }
});

// ===========================================
// 7. REGISTER SHUTDOWN FUNCTION
// ===========================================
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        writeLog("Fatal error: " . $error['message'] . " in " . $error['file'] . " on line " . $error['line'], 'FATAL');
    }
    
    // Cerrar conexiones a BD
    if (function_exists('cerrarConexiones')) {
        cerrarConexiones();
    }
});

// ===========================================
// 8. LOG DE INICIO (solo en debug)
// ===========================================
if (APP_DEBUG) {
    writeLog("Application started - Environment: " . APP_ENV, 'INFO');
}
