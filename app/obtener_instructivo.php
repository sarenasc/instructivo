<?php
require_once("conexion.php");

header('Content-Type: application/json');

$sql = "SELECT i.id_instructivo, i.fecha, e.id ,e.Nombre_Exportadora, es.id_especie,es.especie
  FROM inst_cab_instructivo i
  inner join inst_exportadora as e on e.id = i.id_exportadora
  inner join especie as es on es.id_especie = i.id_especie
  group by i.id_instructivo, e.Nombre_Exportadora, es.especie,e.id,es.id_especie,i.fecha
  order by i.id_instructivo desc";
$stmt = sqlsrv_query($conn, $sql);

$instructivos = [];

if ($stmt) {
  while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $instructivos[] = $row;
  }
  echo json_encode(["success" => true, "data" => $instructivos]);
} else {
  echo json_encode(["success" => false, "message" => "Error al obtener instructivos"]);
}  
