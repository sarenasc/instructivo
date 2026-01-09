<?php
require_once "conexion.php";
session_start();

if (isset($_POST['inicio'])) {
    $usuario = strtolower(trim($_POST['user']));
    $pass = trim($_POST['pass']);

    // Consulta con parámetros preparados
    $sql = "SELECT [id], [nom_usu], [pass_usu], [id_area], [Nombre], [Apellido]
            FROM [SistGestion].[dbo].[TRA_usuario]
            WHERE nom_usu = ? AND pass_usu = ?";

    $params = array($usuario, $pass);
    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if ($stmt && sqlsrv_execute($stmt)) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        if ($row) {
            // Datos del usuario
            $_SESSION['Nom_Usuario'] = $row['nom_usu'];
            $_SESSION['Nombre'] = $row['Nombre'];
            $_SESSION['Apellido'] = $row['Apellido'];
            $_SESSION['id'] = $row['id'];
           

            header("Location: Inicio.php");
            exit();
        } else {
            // Usuario o clave incorrectos
            header("Location: ../index.php?error=1");
            exit();
        }
    } else {
        // Error al preparar o ejecutar la consulta
        die("Error en la consulta de login.");
    }
}
?>
