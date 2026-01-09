<?php
include 'conexion.php';

$accion = $_POST['accion'] ?? '';

$cod_pallet = $_POST['cod_pallet'] ?? '';
$descrip_pallet = $_POST['descrip_pallet'] ?? '';
$id_exportadora = $_POST['id_exportadora'] ?? '';

if (!$cod_pallet || !$descrip_pallet || !$id_exportadora) {
    echo "Todos los campos son obligatorios.";
    exit;
}

if ($accion == "guardar") {
    $sql = "INSERT INTO inst_pallet (cod_pallet, Descrip_pallet, id_exportadora) VALUES (?, ?, ?)";
} elseif ($accion == "modificar") {
    $sql = "UPDATE inst_pallet SET cod_pallet = ?, descrip_pallet = ?, id_exportadora = ? WHERE cod_pallet = $cod_pallet";
} elseif ($accion == "eliminar") {
    $sql = "DELETE FROM inst_pallet WHERE cod_pallet = ?";
} else {
    echo "Acción no válida.";
    exit;
}

$params = ($accion == "eliminar") ? [$cod_pallet] : [$cod_pallet,$descrip_pallet, $id_exportadora];
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
