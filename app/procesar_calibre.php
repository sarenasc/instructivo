<?php
include 'conexion.php';

$accion = $_POST['accion'] ?? '';

$codigo_calibre = $_POST['codigo_calibre'] ?? '';
$nombre_calibre = $_POST['nombre_calibre'] ?? '';
$especie = $_POST['especie'] ?? '';

if (!$codigo_calibre || !$nombre_calibre || !$especie) {
    echo "Todos los campos son obligatorios.";
    exit;
}

if ($accion == "guardar") {
    $sql = "INSERT INTO inst_calibre (cod_calibre, nombre_calibre, id_especie) VALUES (?, ?, ?)";
} elseif ($accion == "modificar") {
    $sql = "UPDATE inst_calibre SET cod_calibre = ? ,nombre_calibre = ?, id_especie = ? WHERE cod_calibre = $codigo_calibre and id_especie = $especie";
} elseif ($accion == "eliminar") {
    $sql = "DELETE FROM inst_calibre WHERE cod_calibre = ? and id_especie = ?";
} else {
    echo "Acción no válida.";
    exit;
}

$params = ($accion == "eliminar") ? [$codigo_calibre, $especie] : [$codigo_calibre, $nombre_calibre, $especie];
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
