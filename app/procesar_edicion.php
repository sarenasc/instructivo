<?php
include '../app/conexion.php';
$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$nombre = $data['nombre']; // adaptar según campos

$sql = "UPDATE embalaje SET nombre = ? WHERE id = ?";
$stmt = sqlsrv_prepare($conn, $sql, [$nombre, $id]);

if (sqlsrv_execute($stmt)) {
  echo json_encode(['ok' => true]);
} else {
  echo json_encode(['ok' => false]);
}
