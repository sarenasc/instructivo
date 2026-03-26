<?php
/**
 * ===========================================
 * CONFIGURACIÓN CENTRALIZADA DE BASE DE DATOS
 * ===========================================
 * Este archivo carga las variables de entorno y establece
 * las conexiones a las bases de datos.
 * 
 * Uso:
 *   require_once __DIR__ . '/config/database.php';
 *   // Usa $conn, $conn2, $conn3 para las diferentes BD
 */

// Cargar variables de entorno
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Cargar .env desde la raíz del proyecto
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// ===========================================
// CONEXIÓN 1: SistGestion (Principal)
// ===========================================
$serverName = $_ENV['DB_SERVER'];
$connectionInfo = [
    "Database" => $_ENV['DB_DATABASE'],
    "UID" => $_ENV['DB_USER'],
    "PWD" => $_ENV['DB_PASSWORD'],
    'CharacterSet' => $_ENV['PHP_CHARSET'] ?? 'UTF-8'
];

$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    error_log("Error conexión SistGestion: " . print_r(sqlsrv_errors(), true));
    die("Error de conexión a la base de datos principal.");
}

// ===========================================
// CONEXIÓN 2: Facturador_ASanta_Almahue
// ===========================================
$serverName2 = $_ENV['DB_SERVER'];
$connectionInfo2 = [
    "Database" => $_ENV['DB_DATABASE_FACTURADOR'],
    "UID" => $_ENV['DB_USER'],
    "PWD" => $_ENV['DB_PASSWORD'],
    'CharacterSet' => $_ENV['PHP_CHARSET'] ?? 'UTF-8'
];

$conn2 = sqlsrv_connect($serverName2, $connectionInfo2);

if (!$conn2) {
    error_log("Error conexión Facturador: " . print_r(sqlsrv_errors(), true));
    // No morimos, esta BD puede ser opcional
}

// ===========================================
// CONEXIÓN 3: DW_Almahue
// ===========================================
$serverName3 = $_ENV['DB_SERVER'];
$connectionInfo3 = [
    "Database" => $_ENV['DB_DATABASE_DW'],
    "UID" => $_ENV['DB_USER'],
    "PWD" => $_ENV['DB_PASSWORD'],
    'CharacterSet' => $_ENV['PHP_CHARSET'] ?? 'UTF-8'
];

$conn3 = sqlsrv_connect($serverName3, $connectionInfo3);

if (!$conn3) {
    error_log("Error conexión DW: " . print_r(sqlsrv_errors(), true));
    // No morimos, esta BD puede ser opcional
}

// ===========================================
// FUNCIÓN PARA CERRAR CONEXIONES
// ===========================================
function cerrarConexiones() {
    global $conn, $conn2, $conn3;
    if ($conn) sqlsrv_close($conn);
    if ($conn2) sqlsrv_close($conn2);
    if ($conn3) sqlsrv_close($conn3);
}

// Registrar shutdown para cerrar conexiones automáticamente
register_shutdown_function('cerrarConexiones');
