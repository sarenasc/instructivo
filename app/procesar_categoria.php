<?php
include 'conexion.php';

$accion = $_POST['accion'] ?? '';

$codigo_categoria = $_POST['codigo_categoria'] ?? '';
$nombre_categoria = $_POST['nombre_categoria'] ?? '';
$especie_categoria = $_POST['especie_categoria'] ?? '';
$exportadora_categoria = $_POST['exportadora'] ?? '';

if (!$codigo_categoria || !$nombre_categoria || !$especie_categoria || !$exportadora_categoria) {
    echo "Todos los campos son obligatorios.";
    exit;
}

if ($accion == "guardar") {
    $sql = "INSERT INTO inst_categoria (cod_categoria, nombre_categoria, id_especie, id_exportadora) VALUES (?, ?, ?, ?)";
} elseif ($accion == "modificar") {
    $sql = "UPDATE inst_categoria SET cod_categoria = ?, nombre_categoria = ?, id_especie = ?, id_exportadora = ? WHERE cod_categoria = $codigo_categoria and id_especie = $especie_categoria";
} elseif ($accion == "eliminar") {
    $sql = "DELETE FROM inst_categoria WHERE cod_categoria = ? and id_especie = ?";
} else {
    echo "Acción no válida.";
    exit;
}

$params = ($accion == "eliminar") ? [$codigo_categoria, $especie_categoria] : [$codigo_categoria, $nombre_categoria, $especie_categoria, $exportadora_categoria];
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
