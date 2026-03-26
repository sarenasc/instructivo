<?php
require_once("../conexion.php");
header('Content-Type: application/json');

$sql = "SELECT p.id_plu, p.codigo_plu, p.nombre_plu, 
               p.id_especie, e.especie
        FROM plu p
        LEFT JOIN especie e ON p.id_especie = e.id_especie
        ORDER BY p.codigo_plu";

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
