<?php
/**
 * ===========================================
 * CONEXIÓN A BASE DE DATOS
 * ===========================================
 * Conexión directa sin variables de entorno (compatible con driver actual)
 */

// Conexión 1: SistGestion (Principal)
$serverName = "192.168.19.4";
$connectionInfo = [
    "Database" => "SistGestion",
    "UID" => "sa",
    "PWD" => "Robin@2021",
    'CharacterSet' => 'UTF-8',
    "ReturnDatesAsStrings" => true
];

$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    error_log("Error conexión SistGestion: " . print_r(sqlsrv_errors(), true));
    die("Error de conexión a la base de datos principal.");
}

// Conexión 2: Facturador_ASanta_Almahue
$connectionInfo2 = [
    "Database" => "Facturador_ASanta_Almahue",
    "UID" => "sa",
    "PWD" => "Robin@2021",
    'CharacterSet' => 'UTF-8',
    "ReturnDatesAsStrings" => true
];

$conn2 = sqlsrv_connect($serverName, $connectionInfo2);

// Conexión 3: DW_Almahue
$connectionInfo3 = [
    "Database" => "DW_Almahue",
    "UID" => "sa",
    "PWD" => "Robin@2021",
    'CharacterSet' => 'UTF-8',
    "ReturnDatesAsStrings" => true
];

$conn3 = sqlsrv_connect($serverName, $connectionInfo3);
