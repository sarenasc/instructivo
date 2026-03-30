<?php
//este archivo es para rescatar  las versiones  y pasarlos a excel en instructivo/app/Procesos/exportar_instructivo.html
//se diferencia con la s al final de obtener_instructivo que ese es para poder editar uno ya creado
require_once("../conexion.php");
header('Content-Type: application/json');

$id = $_GET['id_instructivo'] ?? null;
if (!$id) {
  echo json_encode([]);
  exit;
}

$sql = "SELECT DISTINCT version FROM inst_detalle_instructivo WHERE id_cab_instructivo = ? ORDER BY version";
$stmt = sqlsrv_query($conn, $sql, [$id]);
$versiones = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
  $versiones[] = ['version' => $row['version']];
}
echo json_encode($versiones);
