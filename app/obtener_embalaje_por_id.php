<?php
require_once("../conexion.php");
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(null);
    exit;
}

$sql = "SELECT * FROM embalaje WHERE id_embalaje = ?";
$stmt = sqlsrv_prepare($conn, $sql);
sqlsrv_execute($stmt, [$id]);

$resultado = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($resultado) {
    echo json_encode($resultado);
} else {
    echo json_encode(null);
}
?>
