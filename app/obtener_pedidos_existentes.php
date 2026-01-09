<?php
include 'conexion.php';

$id = $_GET['id_instructivo'];
$version = $_GET['version'];

$sql = "SELECT id_cab_instructivo,
version,
numero_pedido,
e.Codigo_emb
FROM [SistGestion].[dbo].[inst_detalle_instructivo] i
inner join inst_embalaje e on e.id = i.id_embalaje
WHERE id_cab_instructivo = ? AND version = ?
group by numero_pedido,id_cab_instructivo,version,
numero_pedido,
e.Codigo_emb,
id_embalaje";
$stmt = sqlsrv_query($conn, $sql, [$id, $version]);

$data = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
  $data[] = [
    'numero' => $row['numero_pedido'],
  ];
}

echo json_encode(['success' => true, 'data' => $data]);
