<?php
/**
 * ===========================================
 * PÁGINA DE ERROR
 * ===========================================
 * Manejo centralizado de errores
 */

require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/../config/auth.php';
session_start();

$errores = [
    '1' => 'Usuario o contraseña incorrectos',
    '2' => 'Por favor completa todos los campos',
    '3' => 'Debes iniciar sesión para acceder',
    '4' => 'Tu sesión ha expirado, por favor inicia sesión nuevamente',
    '99' => 'Error interno del servidor',
];

$error_code = $_GET['error'] ?? '3';
$mensaje = $errores[$error_code] ?? 'Error desconocido';

// Limpiar mensaje de sesión si existe
if (isset($_SESSION['error_message'])) {
    $mensaje = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Instructivos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error-container {
            min-height: 80vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .error-card {
            max-width: 500px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container error-container">
        <div class="card error-card p-4 shadow-lg">
            <div class="text-center">
                <div class="mb-3">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <h3 class="text-danger mb-3">¡Error!</h3>
                <p class="lead mb-4"><?= htmlspecialchars($mensaje) ?></p>
                <a href="index.php" class="btn btn-primary btn-block">
                    Volver al Inicio
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
