<?php
include 'conexion.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$id_embalaje = $data['id_embalaje'];
$altura = $data['altura'];
$cajas = $data['cajas'];

if ($id) {
    $sql = "UPDATE inst_altura_pallet SET id_embalaje=?, altura=?, cajas=? WHERE id=?";
    $params = [$id_embalaje, $altura, $cajas, $id];
} else {
    $sql = "INSERT INTO inst_altura_pallet (id_embalaje, altura, cajas) VALUES (?, ?, ?)";
    $params = [$id_embalaje, $altura, $cajas];
}

$stmt = sqlsrv_query($conn, $sql, $params);
echo json_encode(['mensaje' => $stmt ? 'Guardado correctamente' : 'Error al guardar']);
