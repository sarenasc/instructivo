<?php
/**
 * ===========================================
 * VERIFICACIÓN DE SESIÓN
 * ===========================================
 * Incluir este archivo al inicio de CADA página protegida
 * 
 * Uso:
 *   require_once __DIR__ . '/config/require_auth.php';
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['id']) || !isset($_SESSION['Nom_Usuario'])) {
    // No está logueado - redirigir al login
    header("Location: ../index.php?error=3"); // Error: no autorizado
    exit();
}

// Verificar timeout de sesión (1 hora por defecto)
$session_lifetime = isset($_ENV['PHP_SESSION_LIFETIME']) 
    ? (int)$_ENV['PHP_SESSION_LIFETIME'] 
    : 3600;

if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $session_lifetime) {
    // Sesión expirada
    session_destroy();
    header("Location: ../index.php?error=4"); // Error: sesión expirada
    exit();
}

// Actualizar tiempo de sesión
$_SESSION['login_time'] = time();

// ===========================================
// FUNCIONES DE UTILIDAD
// ===========================================

/**
 * Obtener usuario actual
 * @return array Datos del usuario
 */
function getUsuarioActual() {
    return [
        'id' => $_SESSION['id'],
        'nom_usu' => $_SESSION['Nom_Usuario'],
        'nombre' => $_SESSION['Nombre'] ?? '',
        'apellido' => $_SESSION['Apellido'] ?? '',
        'id_area' => $_SESSION['id_area'] ?? null,
    ];
}

/**
 * Verificar token CSRF
 * @param string $token Token a verificar
 * @return bool true si es válido
 */
function verifyCSRF($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generar nuevo token CSRF
 * @return string Token
 */
function renewCSRFToken() {
    require_once __DIR__ . '/auth.php';
    $_SESSION['csrf_token'] = generateCSRFToken();
    return $_SESSION['csrf_token'];
}

/**
 * Redirigir con mensaje de error
 * @param string $mensaje Mensaje de error
 */
function redirectWithError($mensaje) {
    $_SESSION['error_message'] = $mensaje;
    header("Location: error.php");
    exit();
}
