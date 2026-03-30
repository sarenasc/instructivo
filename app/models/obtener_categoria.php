<?php
require_once("../conexion.php");
header('Content-Type: application/json');

// Obtener parámetros de filtro
$id_especie = $_GET['id_especie'] ?? null;
$id_exportadora = $_GET['id_exportadora'] ?? null;

$sql = "SELECT c.id, c.cod_categoria as codigo_categoria, c.nombre_categoria as nombre_categoria, 
               c.id_especie, e.especie, 
               c.id_exportadora, ex.Nombre_Exportadora as nombre_exportadora
        FROM inst_categoria c
        LEFT JOIN especie e ON c.id_especie = e.id_especie
        LEFT JOIN inst_exportadora ex ON c.id_exportadora = ex.id";

$conditions = [];
if ($id_especie) {
    $conditions[] = "c.id_especie = $id_especie";
}
if ($id_exportadora) {
    $conditions[] = "c.id_exportadora = $id_exportadora";
}

if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY c.cod_categoria";

$stmt = sqlsrv_query($conn, $sql);

$resultados = [];
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        foreach ($row as $key => $value) {
            if (is_object($value) && method_exists($value, 'format')) {
                $row[$key] = $value->format('Y-m-d');
            }
        }
        $resultados[] = $row;
    }
    echo json_encode($resultados);
} else {
    echo json_encode([]);
}
?>
