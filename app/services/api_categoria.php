<?php
include '../conexion.php';

header('Content-Type: application/json');

$sql = "select c.id,
c.cod_categoria as 'Codigo Categoria',
c.nombre_categoria as 'Nombre Categoria',
e.especie as Especie,
exp.Nombre_Exportadora as 'Nombre Exportadora'
from inst_categoria c
inner join especie e ON e.id_especie = c.id_especie
inner join inst_exportadora EXP ON exp.id = c.id_exportadora";
$stmt = sqlsrv_query($conn, $sql);

$especies = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $especies[] = $row;
}

echo json_encode($especies);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
