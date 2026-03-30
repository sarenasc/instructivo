<?php
require_once("../conexion.php");
header('Content-Type: application/json');

// Obtener parámetro de filtro por exportadora
$id_exportadora = $_GET['id_exportadora'] ?? null;

$sql = "SELECT e.id, e.Cod_etiqueta as codigo_etiqueta, e.Nombre_etiqueta as nombre_etiqueta, 
               e.id_exportadora, ex.Nombre_Exportadora as nombre_exportadora
        FROM inst_etiqueta e
        LEFT JOIN inst_exportadora ex ON e.id_exportadora = ex.id";

if ($id_exportadora) {
    $sql .= " WHERE e.id_exportadora = $id_exportadora";
}

$sql .= " ORDER BY e.Cod_etiqueta";

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
