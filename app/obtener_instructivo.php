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
    // Formatear fecha correctamente
    if ($row['fecha'] instanceof DateTime) {
      $row['fecha_formateada'] = $row['fecha']->format('d/m/Y');
    } elseif ($row['fecha']) {
      // Si es string, intentar convertir
      try {
        $date = new DateTime($row['fecha']);
        $row['fecha_formateada'] = $date->format('d/m/Y');
      } catch (Exception $e) {
        $row['fecha_formateada'] = 'Sin fecha';
      }
    } else {
      $row['fecha_formateada'] = 'Sin fecha';
    }
    $instructivos[] = $row;
  }
  echo json_encode(["success" => true, "data" => $instructivos]);
} else {
  echo json_encode(["success" => false, "message" => "Error al obtener instructivos"]);
}  
