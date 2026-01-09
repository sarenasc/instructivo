<?php
include 'conexion.php';

header('Content-Type: application/json');

$sql = "SELECT id_especie, codigo_especie as 'Codigo Especie', especie as 'Especie' FROM SistGestion.dbo.especie";
$stmt = sqlsrv_query($conn, $sql);

$especies = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $especies[] = $row;
}

echo json_encode($especies);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
