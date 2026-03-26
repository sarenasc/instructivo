<?php
require_once("../conexion.php");
header('Content-Type: application/json');

$sql = "SELECT a.*, e.nombre_embalaje as nombre_embalaje 
        FROM inst_altura_pallet a
        LEFT JOIN embalaje e ON a.id_embalaje = e.id_embalaje
        ORDER BY a.id_altura_pallet";
$stmt = sqlsrv_query($conn, $sql);

$resultados = [];
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $resultados[] = $row;
    }
    echo json_encode($resultados);
} else {
    echo json_encode([]);
}
?>
