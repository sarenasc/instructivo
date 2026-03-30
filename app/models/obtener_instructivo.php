<?php
require_once("../conexion.php");

header('Content-Type: application/json');

$id_exportadora = $_GET['id_exportadora'] ?? null;

// Construir consulta con filtro opcional
$sql = "SELECT i.id_instructivo, i.fecha, i.id_exportadora, e.Nombre_Exportadora, es.id_especie, es.especie
  FROM inst_cab_instructivo i
  INNER JOIN inst_exportadora e ON e.id = i.id_exportadora
  INNER JOIN especie es ON es.id_especie = i.id_especie
  WHERE 1=1";

$params = [];

if ($id_exportadora) {
  $sql .= " AND i.id_exportadora = ?";
  $params[] = $id_exportadora;
}

$sql .= " ORDER BY i.id_instructivo DESC";

$stmt = sqlsrv_query($conn, $sql, $params);

$instructivos = [];

if ($stmt) {
  while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    // Formatear fecha correctamente
    if ($row['fecha'] instanceof DateTime) {
      $row['fecha_formateada'] = $row['fecha']->format('d/m/Y');
      $row['fecha_raw'] = $row['fecha']->format('Y-m-d');
    } elseif ($row['fecha']) {
      // Si es string, intentar convertir
      try {
        $date = new DateTime($row['fecha']);
        $row['fecha_formateada'] = $date->format('d/m/Y');
        $row['fecha_raw'] = $date->format('Y-m-d');
      } catch (Exception $e) {
        $row['fecha_formateada'] = 'Sin fecha';
        $row['fecha_raw'] = null;
      }
    } else {
      $row['fecha_formateada'] = 'Sin fecha';
      $row['fecha_raw'] = null;
    }
    $instructivos[] = $row;
  }
  echo json_encode(["success" => true, "data" => $instructivos]);
} else {
  echo json_encode(["success" => false, "message" => "Error al obtener instructivos"]);
}  
