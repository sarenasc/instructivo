<?php
include 'conexion.php';

header('Content-Type: application/json');

$sql = "SELECT  em.id, 
em.Codigo_emb as 'Codigo Embalaje',
em.Descripcion_Embalaje as 'Nombre Embalaje',
em.Peso_Embalaje as 'Peso Embalaje',
et.Nombre_etiqueta as Etiqueta,
es.especie as 'Especie',
exp.Nombre_Exportadora as 'Nombre Exportadora'
FROM inst_embalaje em
inner join inst_etiqueta et ON et.id = em.id_etiqueta
inner join especie es ON es.id_especie = em.id_especie
inner join inst_exportadora exp on exp.id = em.id_exportadora";
$stmt = sqlsrv_query($conn, $sql);

$especies = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $especies[] = $row;
}

echo json_encode($especies);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>