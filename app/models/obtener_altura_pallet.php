<?php
require_once("../conexion.php");
header('Content-Type: application/json');

// Obtener parámetro de filtro por embalaje
$id_embalaje = $_GET['id_embalaje'] ?? null;

$sql = "SELECT a.id AS id_altura_pallet, a.id_embalaje, a.altura, a.cajas,
               e.Codigo_emb AS codigo_embalaje, e.Descripcion_Embalaje AS nombre_embalaje,
               es.especie
        FROM inst_altura_pallet a
        LEFT JOIN inst_embalaje e  ON a.id_embalaje = e.id
        LEFT JOIN especie        es ON e.id_especie  = es.id_especie";

$params = [];
if ($id_embalaje) {
    $sql .= " WHERE a.id_embalaje = ?";
    $params[] = (int)$id_embalaje;
}

$sql .= " ORDER BY a.altura";

$stmt = sqlsrv_query($conn, $sql, $params ?: null);

$resultados = [];
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $resultados[] = $row;
    }
    echo json_encode($resultados);
} else {
    echo json_encode([]);
}
?>
