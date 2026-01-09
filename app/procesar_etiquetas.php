<?php
include 'conexion.php';

$accion = $_POST['accion'] ?? '';

$codigo_categoria = $_POST['codigo_etiqueta'] ?? '';
$nombre_categoria = $_POST['nombre_etiqueta'] ?? '';
$especie_categoria = $_POST['exportadora'] ?? '';

if (!$codigo_categoria || !$nombre_categoria || !$especie_categoria) {
    echo "Todos los campos son obligatorios.";
    exit;
}

if ($accion == "guardar") {
    $sql = "INSERT INTO inst_etiqueta (Cod_etiqueta, Nombre_etiqueta, id_exportadora) VALUES (?, ?, ?)";
} elseif ($accion == "modificar") {
    $sql = "UPDATE inst_etiqueta SET Cod_etiqueta = ?, Nombre_Etiqueta = ?, id_exportadora = ? WHERE Cod_etiqueta = $codigo_categoria";
} elseif ($accion == "eliminar") {
    $sql = "DELETE FROM inst_etiqueta WHERE Cod_etiqueta = ? ";
} else {
    echo "Acción no válida.";
    exit;
}

$params = ($accion == "eliminar") ? [$codigo_categoria, $especie_categoria] : [$codigo_categoria, $nombre_categoria, $especie_categoria];
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
