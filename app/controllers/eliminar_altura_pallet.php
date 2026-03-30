<?php
include 'conexion.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];

$sql = "DELETE FROM inst_altura_pallet WHERE id = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);

echo json_encode(['mensaje' => $stmt ? 'Eliminado correctamente' : 'Error al eliminar']);
