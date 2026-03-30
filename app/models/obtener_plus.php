<?php
require_once("../conexion.php");
header('Content-Type: application/json');

// Obtener parámetro de filtro por especie
$id_especie = $_GET['id_especie'] ?? null;

$sql = "SELECT p.id, p.cod_plu as codigo_plu, p.plu as nombre_plu, 
               p.id_especie, e.especie
        FROM inst_plu p
        LEFT JOIN especie e ON p.id_especie = e.id_especie";

if ($id_especie) {
    $sql .= " WHERE p.id_especie = $id_especie";
}

$sql .= " ORDER BY p.cod_plu";

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
