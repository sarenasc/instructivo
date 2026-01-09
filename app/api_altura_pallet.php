<?php
include 'conexion.php';

header('Content-Type: application/json');

$sql = "SELECT ap.id, 
em.Codigo_emb as 'Codigo Embalaje',
ap.altura,
ap.cajas
FROM inst_altura_pallet ap
INNER JOIN inst_embalaje em ON em.id = ap.id_embalaje";
$stmt = sqlsrv_query($conn, $sql);

$especies = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $especies[] = $row;
}

echo json_encode($especies);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>