<?php
include 'conexion.php';
$id = $_GET['id_instructivo'];
$version = $_GET['version'];

$sql = "SELECT DISTINCT numero_pedido FROM inst_detalle_instructivo WHERE id_instructivo = ? and version =?";
$stmt = sqlsrv_query($conn, $sql, [$id,$version]);
$data = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
  $data[] = ['numero_pedido' => $row['numero_pedido']];
}
echo json_encode(['success' => true, 'data' => $data]);
