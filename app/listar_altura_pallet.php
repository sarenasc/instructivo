<?php
include 'conexion.php';
header('Content-Type: application/json');

$sql = "
  SELECT a.id, a.altura, a.cajas, a.id_embalaje, e.codigo_emb AS embalaje
  FROM inst_altura_pallet a
  JOIN inst_embalaje e ON a.id_embalaje = e.id
";

$stmt = sqlsrv_query($conn, $sql);
$datos = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
  $datos[] = $row;
}

echo json_encode($datos);

