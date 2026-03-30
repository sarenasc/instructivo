<?php
require_once("../conexion.php");
header('Content-Type: application/json');

// Obtener parámetros de filtro
$id_especie = $_GET['id_especie'] ?? null;
$id_exportadora = $_GET['id_exportadora'] ?? null;

$sql = "SELECT e.id,
               e.Codigo_emb         AS codigo_embalaje,
               e.Descripcion_Embalaje AS nombre_embalaje,
               e.Peso_Embalaje      AS peso_embalaje,
               e.id_etiqueta,
               e.id_especie,
               e.id_exportadora,
               et.Nombre_etiqueta   AS nombre_etiqueta,
               es.especie,
               ex.Nombre_Exportadora
        FROM inst_embalaje e
        LEFT JOIN inst_etiqueta   et ON et.id        = e.id_etiqueta
        LEFT JOIN especie          es ON es.id_especie = e.id_especie
        LEFT JOIN inst_exportadora ex ON ex.id        = e.id_exportadora";

$conditions = [];
$params = [];
if ($id_especie) {
    $conditions[] = "e.id_especie = ?";
    $params[] = $id_especie;
}
if ($id_exportadora) {
    $conditions[] = "e.id_exportadora = ?";
    $params[] = $id_exportadora;
}

if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY e.Codigo_emb";

$stmt = sqlsrv_query($conn, $sql, count($params) ? $params : []);

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
