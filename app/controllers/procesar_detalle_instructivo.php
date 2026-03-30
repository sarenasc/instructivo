<?php
require_once("conexion.php");

header('Content-Type: application/json');

// Recibir los datos del formulario
$id_cab_instructivo = $_POST['id_cab_instructivo'] ?? null;
$version = $_POST['version'] ?? null;
$id_embalaje = $_POST['id_embalaje'] ?? null;
$id_calibre = $_POST['id_calibre'] ?? null;
$id_etiqueta = $_POST['id_etiqueta'] ?? null;
$cantidad_pedido = $_POST['cantidad_pedido'] ?? null;
$id_categoria = $_POST['id_categoria'] ?? null;
$id_plu = $_POST['id_plu'] ?? null;
$id_destino = $_POST['id_destino'] ?? null;
$altura_pallet = $_POST['altura_pallet'] ?? null;
$id_pallet = $_POST['id_pallet'] ?? null;
$observacion = $_POST['observacion_detalle'] ?? null;
$numero_pedido = $_POST['numero_pedido'] ?? null;
$var_etiquetada = $_POST['var_etiquetada'] ?? null;

if (!$id_cab_instructivo || !$version || !$id_embalaje || !$id_calibre || !$id_categoria || !$id_plu || !$id_destino || !$id_pallet || !$id_etiqueta) {
  echo json_encode([
    "success" => false,
    "message" => "Faltan campos obligatorios en el detalle"
  ]);
  exit;
}

$sql = "INSERT INTO inst_detalle_instructivo (
          id_cab_instructivo, version, id_embalaje, id_calibre, cantidad_pedido,
          id_categoria, id_plu, id_etiqueta, id_destino, altura_pallet, id_pallet,
          observacion, numero_pedido, var_etiquetada
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

$params = [
  $id_cab_instructivo, $version, $id_embalaje, $id_calibre, $cantidad_pedido,
  $id_categoria, $id_plu, $id_etiqueta, $id_destino , $altura_pallet, $id_pallet,
  $observacion, $numero_pedido, $var_etiquetada
];

$stmt = sqlsrv_prepare($conn, $sql, $params);

if (!$stmt) {
  echo json_encode([
    "success" => false,
    "message" => "Error al preparar la consulta SQL"
  ]);
  exit;
}

if (sqlsrv_execute($stmt)) {
  echo json_encode([
    "success" => true,
    "message" => "Detalle guardado correctamente"
  ]);
} else {
  echo json_encode([
    "success" => false,
    "message" => "Error al guardar detalle"
  ]);
}
