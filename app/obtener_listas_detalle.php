<?php
require_once 'conexion.php'; 

$id_exportadora = $_GET['id_exportadora'] ?? null;
$id_especie = $_GET['id_especie'] ?? null;

function obtenerListaFiltrada($conn, $tabla, $filtros = []) {
  $sql = "SELECT * FROM $tabla WHERE 1=1";
  $params = [];

  if (isset($filtros['id_exportadora'])) {
    $sql .= " AND id_exportadora = ?";
    $params[] = $filtros['id_exportadora'];
  }

  if (isset($filtros['id_especie'])) {
    $sql .= " AND id_especie = ?";
    $params[] = $filtros['id_especie'];
  }

  $stmt = sqlsrv_query($conn, $sql, $params);
  $resultado = [];

  while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $resultado[] = $row;
  }

  return $resultado;
}

function obtenerAlturas($conn) {
  $sql = "SELECT * FROM inst_altura_pallet";
  $stmt = sqlsrv_query($conn, $sql);
  $resultado = [];

  while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $resultado[] =[
    "id" => $row['id'],
    "id_embalaje" => $row['id_embalaje'],
    "altura" => $row['altura'],
    "caja" => $row['cajas']
    
    ];
  }

  return $resultado;
}

// Construir el array completo
$resultado = [
  "embalajes" => obtenerListaFiltrada($conn, 'inst_embalaje', [
    'id_exportadora' => $id_exportadora,
    'id_especie' => $id_especie
  ]),
  "categorias" => obtenerListaFiltrada($conn, 'inst_categoria', [
    'id_exportadora' => $id_exportadora,
    'id_especie' => $id_especie
  ]),
  "etiqueta" => obtenerListaFiltrada($conn, 'inst_etiqueta', [
    'id_exportadora' => $id_exportadora
  ]),
  "pallets" => obtenerListaFiltrada($conn, 'inst_pallet', [
    'id_exportadora' => $id_exportadora
  ]),
  "calibres" => obtenerListaFiltrada($conn, 'inst_calibre', [
    'id_especie' => $id_especie
  ]),
  "plus" => obtenerListaFiltrada($conn, 'inst_plu', [
    'id_especie' => $id_especie
  ]),
  "destinos" => obtenerListaFiltrada($conn, 'inst_destino'),
  "altura" => obtenerAlturas($conn) 
];

echo json_encode([
  "success" => true,
  "data" => $resultado
]);

