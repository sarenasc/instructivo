<?php
require_once("../conexion.php");
header('Content-Type: application/json');

$sql = "SELECT e.id_etiqueta, e.codigo_etiqueta, e.nombre_etiqueta, 
               e.id_exportadora, ex.nombre_exportadora
        FROM etiqueta e
        LEFT JOIN exportadora ex ON e.id_exportadora = ex.id_exportadora
        ORDER BY e.codigo_etiqueta";

$stmt = sqlsrv_query($conn, $sql);

$resultados = [];
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Convertir DateTime a string si es necesario
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
