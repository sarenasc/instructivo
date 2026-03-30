<?php
include '../conexion.php';

$sql = "SELECT id, Nombre_Exportadora, cod_exportadora FROM inst_exportadora";
$stmt = sqlsrv_query($conn, $sql);

$exportadoras = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $exportadoras[] = $row;
}

echo json_encode($exportadoras);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
