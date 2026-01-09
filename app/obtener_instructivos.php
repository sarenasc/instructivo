<?php
//este archivo es para rescatar los instructivo y pasarlos a excel en instructivo/app/Procesos/exportar_instructivo.html
//se diferencia con la s al final de obtener_instructivo que ese es para poder editar uno ya creado
include 'conexion.php';

$sql = "
  SELECT cab.id_instructivo, cab.id_exportadora, exp.nombre_exportadora, cab.fecha,esp.especie
  FROM inst_cab_instructivo cab
  INNER JOIN inst_exportadora exp ON exp.id = cab.id_exportadora
  inner join especie esp on esp.id_especie = cab.id_especie
  ORDER BY cab.fecha DESC
";

$stmt = sqlsrv_query($conn, $sql);
$data = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
  $data[] = [
    'id_instructivo' => $row['id_instructivo'],
    'nombre_exportadora' => $row['nombre_exportadora'],
    'fecha' => $row['fecha']->format('Y-m-d'),
    'especie' => $row['especie']
  ];
}
echo json_encode($data);
