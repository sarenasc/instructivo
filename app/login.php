<?php
/**
 * ===========================================
 * LOGIN DE USUARIOS
 * ===========================================
 * Autenticación de usuarios
 */

require_once __DIR__ . '/conexion.php';

session_start();

// Verificar si viene del formulario
if (isset($_POST['inicio'])) {
    $usuario = strtolower(trim($_POST['user']));
    $pass = trim($_POST['pass']);
    
    // Validación básica
    if (empty($usuario) || empty($pass)) {
        header("Location: ../index.php?error=2");
        exit();
    }
    
    // Consulta directa (sin prepared statements por compatibilidad con el driver)
    $usuario_safe = str_replace("'", "''", $usuario); // Escape básico
    
    $sql = "SELECT [id], [nom_usu], [pass_usu], [id_area], [Nombre], [Apellido]
            FROM [SistGestion].[dbo].[TRA_usuario]
            WHERE nom_usu = '$usuario_safe'";
    
    $stmt = sqlsrv_query($conn, $sql);
    
    if (!$stmt) {
        error_log("Error en consulta login: " . print_r(sqlsrv_errors(), true));
        header("Location: ../index.php?error=99");
        exit();
    }
    
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    
    // Verificar si existe el usuario
    if (!$row) {
        header("Location: ../index.php?error=1");
        exit();
    }
    
    // Verificar contraseña (TEXTO PLANO)
    $password_db = $row['pass_usu'];
    
    if ($pass !== $password_db) {
        header("Location: ../index.php?error=1");
        exit();
    }
    
    // ===========================================
    // LOGIN EXITOSO - Crear sesión
    // ===========================================
    session_regenerate_id(true);
    
    $_SESSION['id'] = $row['id'];
    $_SESSION['Nom_Usuario'] = $row['nom_usu'];
    $_SESSION['Nombre'] = $row['Nombre'] ?? '';
    $_SESSION['Apellido'] = $row['Apellido'] ?? '';
    $_SESSION['id_area'] = $row['id_area'] ?? null;
    $_SESSION['login_time'] = time();
    
    error_log("Login exitoso: {$row['nom_usu']} (ID: {$row['id']})");
    
    header("Location: /instructivo/app/inicio.php");
    exit();
}

header("Location: ../index.php");
exit();
