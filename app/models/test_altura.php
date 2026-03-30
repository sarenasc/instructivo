<?php
require_once("../conexion.php");
header('Content-Type: application/json');

// Probar con un id_embalaje específico
$id_embalaje = $_GET['id_embalaje'] ?? null;

echo "=== ID EMBALAJE RECIBIDO: $id_embalaje ===\n\n";

if (!$id_embalaje) {
    echo "ERROR: No se recibió id_embalaje\n";
    echo "URL de prueba: obtener_altura_pallet.php?id_embalaje=2\n";
    exit;
}

$sql = "SELECT a.id, a.id_embalaje, a.altura, a.cajas, e.Codigo_emb as codigo_embalaje
        FROM inst_altura_pallet a
        LEFT JOIN inst_embalaje e ON a.id_embalaje = e.id
        WHERE a.id_embalaje = $id_embalaje
        ORDER BY a.altura";

echo "SQL: $sql\n\n";

$stmt = sqlsrv_query($conn, $sql);

if (!$stmt) {
    echo "ERROR SQL: " . print_r(sqlsrv_errors(), true);
    exit;
}

$resultados = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $resultados[] = $row;
}

echo "RESULTADOS (" . count($resultados) . "):\n";
echo json_encode($resultados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
