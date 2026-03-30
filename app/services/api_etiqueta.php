<?php
include '../conexion.php';

header('Content-Type: application/json');

$sql = "SELECT e.id,
e.Nombre_etiqueta,
x.Nombre_exportadora
  FROM [SistGestion].[dbo].[inst_etiqueta] e
  inner join inst_exportadora as x on e.id_exportadora = x.id";
$stmt = sqlsrv_query($conn, $sql);

$etiqueta = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $etiqueta[] = $row;
}

echo json_encode($etiqueta);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
