<?php
require_once("../conexion.php");
header('Content-Type: application/json');

$sql = "SELECT c.id_categoria, c.cod_categoria as codigo_categoria, c.nombre_categoria, 
               c.id_especie, e.especie, 
               c.id_exportadora, ex.nombre_exportadora
        FROM inst_categoria c
        LEFT JOIN especie e ON c.id_especie = e.id_especie
        LEFT JOIN exportadora ex ON c.id_exportadora = ex.id_exportadora
        ORDER BY c.codigo_categoria";

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
