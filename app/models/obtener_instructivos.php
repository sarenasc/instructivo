<?php
//este archivo es para rescatar los instructivo y pasarlos a excel en instructivo/app/Procesos/exportar_instructivo.html
//se diferencia con la s al final de obtener_instructivo que ese es para poder editar uno ya creado
require_once("../conexion.php");
header('Content-Type: application/json');

// Filtros opcionales
$id_exportadora = isset($_GET['id_exportadora']) ? $_GET['id_exportadora'] : null;
$id_especie = isset($_GET['id_especie']) ? $_GET['id_especie'] : null;
$desde = isset($_GET['desde']) ? $_GET['desde'] : null;
$hasta = isset($_GET['hasta']) ? $_GET['hasta'] : null;

$where = [];
$params = [];

if ($id_exportadora) {
    $where[] = "cab.id_exportadora = ?";
    $params[] = $id_exportadora;
}
if ($id_especie) {
    $where[] = "cab.id_especie = ?";
    $params[] = $id_especie;
}
if ($desde) {
    $where[] = "cab.fecha >= ?";
    $params[] = $desde;
}
if ($hasta) {
    $where[] = "cab.fecha <= ?";
    $params[] = $hasta;
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "
  SELECT DISTINCT cab.id_instructivo, cab.id_exportadora, exp.nombre_exportadora, cab.fecha, esp.especie, cab.turno
  FROM inst_cab_instructivo cab
  INNER JOIN inst_exportadora exp ON exp.id = cab.id_exportadora
  INNER JOIN especie esp ON esp.id_especie = cab.id_especie
  $whereClause
  ORDER BY cab.fecha DESC
";

$stmt = sqlsrv_query($conn, $sql, $params);
$data = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
  // Manejar fecha (puede ser objeto DateTime o string)
  $fecha = $row['fecha'];
  if (is_object($fecha) && method_exists($fecha, 'format')) {
    $fecha = $fecha->format('Y-m-d');
  }
  
  $data[] = [
    'id_instructivo' => $row['id_instructivo'],
    'nombre_exportadora' => $row['nombre_exportadora'],
    'fecha' => $fecha,
    'especie' => $row['especie'],
    'turno' => $row['turno'] ?? ''
  ];
}
echo json_encode($data);
