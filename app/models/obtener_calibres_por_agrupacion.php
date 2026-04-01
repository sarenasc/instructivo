<?php
require_once("../conexion.php");
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;
if (!$id) { echo json_encode([]); exit; }

$sql = "SELECT id_calibre FROM inst_agrupacion_calibre_detalle WHERE id_agrupacion = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);

$resultado = [];
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $resultado[] = $row;
    }
}
echo json_encode($resultado);
