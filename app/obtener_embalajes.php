<?php
require_once("../conexion.php");
header('Content-Type: application/json');

$sql = "SELECT * FROM embalaje ORDER BY codigo_embalaje";
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
