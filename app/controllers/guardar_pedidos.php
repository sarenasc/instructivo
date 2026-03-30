<?php
include 'conexion.php';
$id = $_POST['id_instructivo'];
$version = $_POST['version'];
$pedidos = json_decode($_POST['pedidos'], true);

foreach ($pedidos as $p) {
  $sql = "INSERT INTO INST_PEDIDOS (id_instructivo, version, numero_pedido, cantidad, prioridad) VALUES (?, ?, ?, ?,?)";
  sqlsrv_query($conn, $sql, [$id, $version, $p['numero'], $p['cantidad'], $p['prioridad']]);
}

echo json_encode(['success' => true]);
