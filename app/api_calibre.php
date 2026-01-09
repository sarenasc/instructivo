<?php
include 'conexion.php';

header('Content-Type: application/json');

$sql = "Select c.cod_calibre as 'Codigo Calibre',
nombre_calibre as 'Calibre',
e.especie as Especie 
from inst_calibre c
inner join especie e ON e.id_especie = c.id_especie";
$stmt = sqlsrv_query($conn, $sql);

$especies = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $especies[] = $row;
}

echo json_encode($especies);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
