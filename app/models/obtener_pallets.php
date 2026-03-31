<?php
require_once("../conexion.php");
header('Content-Type: application/json');

// Obtener parámetro de filtro por exportadora
$id_exportadora = $_GET['id_exportadora'] ?? null;

$sql = "SELECT p.id, p.cod_pallet, p.Descrip_pallet as descrip_pallet,
               p.id_exportadora, ex.Nombre_Exportadora as nombre_exportadora
        FROM inst_pallet p
        LEFT JOIN inst_exportadora ex ON p.id_exportadora = ex.id";

if ($id_exportadora) {
    $sql .= " WHERE p.id_exportadora = $id_exportadora";
}

$sql .= " ORDER BY p.cod_pallet";

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
