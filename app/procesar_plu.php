<?php
include 'conexion.php';

$accion = $_POST['accion'] ?? '';

$codigo_plu = $_POST['codigo_plu'] ?? '';
$nombre_plu = $_POST['nombre_plu'] ?? '';
$especie = $_POST['especie'] ?? '';

if (!$codigo_plu || !$nombre_plu || !$especie) {
    echo "Todos los campos son obligatorios.";
    exit;
}

if ($accion == "guardar") {
    $sql = "INSERT INTO inst_plu (cod_plu, plu, id_especie) VALUES (?, ?, ?)";
} elseif ($accion == "modificar") {
    $sql = "UPDATE inst_plu SET cod_plu = ? ,plu = ?, id_especie = ? WHERE cod_plu = $codigo_calibre and id_especie = $especie";
} elseif ($accion == "eliminar") {
    $sql = "DELETE FROM inst_plu WHERE cod_plu = ? and id_especie = ?";
} else {
    echo "Acción no válida.";
    exit;
}

$params = ($accion == "eliminar") ? [$codigo_plu, $especie] : [$codigo_plu, $nombre_plu, $especie];
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
