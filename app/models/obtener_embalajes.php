<?php
require_once("../conexion.php");
header('Content-Type: application/json');

// Obtener parámetros de filtro
$id_especie = $_GET['id_especie'] ?? null;
$id_exportadora = $_GET['id_exportadora'] ?? null;

$sql = "SELECT id, Codigo_emb as codigo_embalaje, Descripcion_Embalaje as nombre_embalaje, Peso_Embalaje as peso_embalaje, id_etiqueta, id_especie, id_exportadora, tipo, sellado 
        FROM inst_embalaje";

$conditions = [];
if ($id_especie) {
    $conditions[] = "id_especie = $id_especie";
}
if ($id_exportadora) {
    $conditions[] = "id_exportadora = $id_exportadora";
}

if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY Codigo_emb";

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
