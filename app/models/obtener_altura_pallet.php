<?php
require_once("../conexion.php");
header('Content-Type: application/json');

// Obtener parámetro de filtro por embalaje
$id_embalaje = $_GET['id_embalaje'] ?? null;

$sql = "SELECT a.id, a.id_embalaje, a.altura, a.cajas, e.Codigo_emb as codigo_embalaje
        FROM inst_altura_pallet a
        LEFT JOIN inst_embalaje e ON a.id_embalaje = e.id";

if ($id_embalaje) {
    $sql .= " WHERE a.id_embalaje = $id_embalaje";
}

$sql .= " ORDER BY a.altura";

$stmt = sqlsrv_query($conn, $sql);

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
