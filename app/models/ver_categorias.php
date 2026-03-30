<?php
require_once("../conexion.php");

// Verificar si hay datos en inst_categoria
$sql = "SELECT TOP 10 * FROM inst_categoria";
$stmt = sqlsrv_query($conn, $sql);

echo "=== CATEGORÍAS EN BD ===\n\n";

if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "ID: " . $row['id'] . "\n";
        echo "cod_categoria: " . ($row['cod_categoria'] ?? 'NULL') . "\n";
        echo "nombre_categoria: " . ($row['nombre_categoria'] ?? 'NULL') . "\n";
        echo "id_especie: " . ($row['id_especie'] ?? 'NULL') . "\n";
        echo "id_exportadora: " . ($row['id_exportadora'] ?? 'NULL') . "\n";
        echo "---\n";
    }
} else {
    echo "Error: " . print_r(sqlsrv_errors(), true);
}

// Contar total
$sql_count = "SELECT COUNT(*) as total FROM inst_categoria";
$stmt_count = sqlsrv_query($conn, $sql_count);
$row_count = sqlsrv_fetch_array($stmt_count, SQLSRV_FETCH_ASSOC);
echo "\nTotal categorías: " . $row_count['total'] . "\n";
?>
