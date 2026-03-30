<?php
// API para obtener exportadoras
require_once("../conexion.php");
header('Content-Type: application/json');

$sql = "SELECT id, cod_exportadora, Nombre_Exportadora FROM inst_exportadora ORDER BY Nombre_Exportadora ASC";
$stmt = sqlsrv_query($conn, $sql);

$data = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $data[] = [
        'id' => $row['id'],
        'cod_exportadora' => $row['cod_exportadora'],
        'nombre_exportadora' => $row['Nombre_Exportadora']
    ];
}

echo json_encode($data);
