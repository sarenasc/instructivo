<?php
require_once("../conexion.php");
header('Content-Type: application/json');

$sql = "SELECT p.id_pallet, p.cod_pallet, p.descrip_pallet, 
               p.id_exportadora, ex.nombre_exportadora
        FROM pallet p
        LEFT JOIN exportadora ex ON p.id_exportadora = ex.id_exportadora
        ORDER BY p.cod_pallet";

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
