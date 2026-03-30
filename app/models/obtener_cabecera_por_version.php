<?php
// php/obtener_cabecera_por_version.php

header('Content-Type: application/json');
include 'conexion.php';

$id_instructivo = $_GET['id_instructivo'] ?? null;
$version = $_GET['version'] ?? null;

if (!$id_instructivo || !$version) {
  echo json_encode(['error' => 'Faltan parámetros']);
  exit;
}

// Obtener cabecera relacionada con la versión del detalle
$sql = "
  SELECT TOP 1 cab.*, e.Nombre_Exportadora, es.especie
  FROM inst_cab_instructivo cab
  JOIN inst_detalle_instructivo det
    ON cab.id_instructivo = det.id_cab_instructivo
     JOIN inst_exportadora AS e on e.id = cab.id_exportadora
 join especie as es on es.id_especie = cab.id_especie
  WHERE cab.id_instructivo = ? AND det.version = ?
";

$params = [$id_instructivo, $version];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
  echo json_encode([
    'exportadora_id' => $row['id_exportadora'],
    'exportadora' => $row['Nombre_Exportadora'],
    'especie_id'     => $row['id_especie'],
    'especie' => $row['especie'],
    'turno'       => $row['turno'],
    'observacion' => $row['observacion'],
    'version'     => $version
  ]);
} else {
  echo json_encode(['error' => 'No encontrado']);
}
