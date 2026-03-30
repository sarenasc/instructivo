<?php
require_once "../app/conexion.php"; // Ajusta si está en otra ruta

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];
$nombre = $data['nombre'] ?? null;

$sql = "UPDATE especie SET nombre = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$result = $stmt->execute([$nombre, $id]);

echo json_encode(['success' => $result]);
?>
