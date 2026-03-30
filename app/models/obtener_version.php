<?php
require_once("conexion.php");

header('Content-Type: application/json');

$id_instructivo = $_GET['id_instructivo'] ?? null;

if (!$id_instructivo) {
  echo json_encode(["success" => false, "message" => "Falta el parámetro id_instructivo"]);
  exit;
}

$sql = "SELECT version FROM inst_detalle_instructivo WHERE id_cab_instructivo = ? ORDER BY version DESC";
$params = [$id_instructivo];
$stmt = sqlsrv_query($conn, $sql, $params);

$versiones = [];

if ($stmt) {
  while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $versiones[] = $row;
  }
  echo json_encode(["success" => true, "data" => $versiones]);
} else {
  echo json_encode(["success" => false, "message" => "Error al obtener versiones"]);
}
