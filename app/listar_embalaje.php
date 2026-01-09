<?php
include 'conexion.php';
header('Content-Type: application/json');

$sql = "SELECT id, Codigo_emb, Descripcion_Embalaje FROM [SistGestion].[dbo].[inst_embalaje]";
$stmt = sqlsrv_query($conn, $sql);
$embalajes = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
  $embalajes[] = [
    'id' => $row['id'],
    'embalaje' => $row['Codigo_emb'] . " - " . $row['Descripcion_Embalaje']
  ];
}

echo json_encode($embalajes);
