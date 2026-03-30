<?php
require_once("../conexion.php");
header('Content-Type: application/json');

// Ver estructura de los datos
$exportadora = $_GET['id_exportadora'] ?? null;
$especie = $_GET['id_especie'] ?? null;

echo "=== EMBALAJES ===\n";
$sql_emb = "SELECT TOP 3 * FROM inst_embalaje";
if ($exportadora) {
    $sql_emb .= " WHERE id_exportadora = $exportadora";
}
$stmt_emb = sqlsrv_query($conn, $sql_emb);
if ($stmt_emb) {
    while ($row = sqlsrv_fetch_array($stmt_emb, SQLSRV_FETCH_ASSOC)) {
        print_r($row);
    }
}

echo "\n=== ETIQUETAS ===\n";
$sql_etq = "SELECT TOP 3 * FROM inst_etiqueta";
if ($exportadora) {
    $sql_etq .= " WHERE id_exportadora = $exportadora";
}
$stmt_etq = sqlsrv_query($conn, $sql_etq);
if ($stmt_etq) {
    while ($row = sqlsrv_fetch_array($stmt_etq, SQLSRV_FETCH_ASSOC)) {
        print_r($row);
    }
}

echo "\n=== PLU ===\n";
$sql_plu = "SELECT TOP 3 * FROM inst_plu";
if ($especie) {
    $sql_plu .= " WHERE id_especie = $especie";
}
$stmt_plu = sqlsrv_query($conn, $sql_plu);
if ($stmt_plu) {
    while ($row = sqlsrv_fetch_array($stmt_plu, SQLSRV_FETCH_ASSOC)) {
        print_r($row);
    }
}
?>
