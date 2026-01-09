<?php
include 'conexion.php';

$accion = $_POST['accion'] ?? '';

$cod_exportadora = $_POST['cod_exportadora'] ?? '';
$nombre_exportadora = $_POST['nombre_exportadora'] ?? '';

if (!$cod_exportadora || !$nombre_exportadora) {
    echo "Todos los campos son obligatorios.";
    exit;
}

if ($accion == "guardar") {
    $sql = "INSERT INTO inst_exportadora (cod_exportadora, Nombre_Exportadora) VALUES (?, ?)";
} elseif ($accion == "modificar") {
    $sql = "UPDATE inst_exportadora SET Nombre_Exportadora = ? WHERE cod_exportadora = ?";
} elseif ($accion == "eliminar") {
    $sql = "DELETE FROM inst_exportadora WHERE cod_exportadora = ?";
} else {
    echo "Acción no válida.";
    exit;
}

$params = ($accion == "eliminar") ? [$cod_exportadora] : [ $cod_exportadora, $nombre_exportadora];
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
