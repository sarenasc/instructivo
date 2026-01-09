<?php
include 'conexion.php';

$accion = $_POST['accion'] ?? '';

$codigo_destino = $_POST['codigo_destino'] ?? '';
$nombre_destino = $_POST['nombre_destino'] ?? '';


if (!$codigo_destino || !$nombre_destino ) {
    echo "Todos los campos son obligatorios.";
    exit;
}

if ($accion == "guardar") {
    $sql = "INSERT INTO inst_destino (cod_destino, nombre_destino) VALUES (?, ?)";
} elseif ($accion == "modificar") {
    $sql = "UPDATE inst_destino SET cod_destino = ? ,nombre_destino = ? WHERE cod_destino = $codigo_destino";
} elseif ($accion == "eliminar") {
    $sql = "DELETE FROM inst_destino WHERE cod_destino = ? ";
} else {
    echo "Acción no válida.";
    exit;
}

$params = ($accion == "eliminar") ? [$codigo_destino] : [$codigo_destino, $nombre_destino];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    // Muestra errores detallados
    die(print_r(sqlsrv_errors(), true));
}

if ($stmt) {
    echo ucfirst($accion) . " realizado con éxito.";
} else {
    echo "Error en la operación.";
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
