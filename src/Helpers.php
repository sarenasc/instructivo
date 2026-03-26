<?php
/**
 * ===========================================
 * FUNCIONES GLOBALES DEL SISTEMA
 * ===========================================
 * Funciones utilitarias para todo el proyecto
 */

/**
 * Redirigir a una URL
 * @param string $url URL de destino
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Retornar JSON response
 * @param mixed $data Datos a retornar
 * @param int $statusCode Código HTTP
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

/**
 * Sanitizar input
 * @param string $data Datos a sanitizar
 * @return string Datos limpios
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validar email
 * @param string $email Email a validar
 * @return bool true si es válido
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Generar UUID
 * @return string UUID único
 */
function generateUUID() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * Escribir en log
 * @param string $message Mensaje a loguear
 * @param string $level Nivel de log (INFO, ERROR, WARNING)
 */
function writeLog($message, $level = 'INFO') {
    $log_dir = __DIR__ . '/../storage/logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . '/app_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] [$level] $message" . PHP_EOL;
    
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

/**
 * Obtener IP del cliente
 * @return string IP address
 */
function getClientIP() {
    $ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    return $ip;
}

/**
 * Formatear fecha para SQL Server
 * @param mixed $date Fecha a formatear
 * @return string Fecha en formato Y-m-d H:i:s
 */
function formatSQLDate($date) {
    if ($date instanceof DateTime) {
        return $date->format('Y-m-d H:i:s');
    }
    return date('Y-m-d H:i:s', strtotime($date));
}

/**
 * Formatear número como moneda
 * @param float $amount Cantidad
 * @param string $currency Símbolo de moneda
 * @return string Cantidad formateada
 */
function formatCurrency($amount, $currency = '$') {
    return $currency . number_format($amount, 2, ',', '.');
}

/**
 * Verificar si es petición AJAX
 * @return bool true si es AJAX
 */
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Obtener valor de POST con default
 * @param string $key Clave del POST
 * @param mixed $default Valor por defecto
 * @return mixed Valor o default
 */
function post($key, $default = null) {
    return $_POST[$key] ?? $default;
}

/**
 * Obtener valor de GET con default
 * @param string $key Clave del GET
 * @param mixed $default Valor por defecto
 * @return mixed Valor o default
 */
function get($key, $default = null) {
    return $_GET[$key] ?? $default;
}

/**
 * Limpiar archivos antiguos del log (más de 30 días)
 */
function cleanOldLogs($days = 30) {
    $log_dir = __DIR__ . '/../storage/logs';
    if (!is_dir($log_dir)) return;
    
    $files = glob($log_dir . '/*.log');
    $cutoff = time() - ($days * 86400);
    
    foreach ($files as $file) {
        if (filemtime($file) < $cutoff) {
            unlink($file);
        }
    }
}
