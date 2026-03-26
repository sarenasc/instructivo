<?php
/**
 * ===========================================
 * HEADER COMPLETO - COMPONENTE COMPARTIDO
 * ===========================================
 * Incluye: DOCTYPE, head, navbar
 * 
 * Uso:
 *   <?php require_once __DIR__ . '/../includes/header.php'; ?>
 */

// Verificar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php?error=3");
    exit();
}

$titulo_pagina = $titulo_pagina ?? 'Instructivo - AgroIndustrial Almahue';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_pagina ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/instructivo/app/assets/css/styles.css">
</head>
<body>

<?php require_once __DIR__ . '/menu.php'; ?>
