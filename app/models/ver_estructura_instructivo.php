<?php
require_once(__DIR__ . '/../conexion.php');

$sql = "SELECT COLUMN_NAME, DATA_TYPE 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = 'inst_cab_instructivo' 
        ORDER BY ORDINAL_POSITION";

$stmt = sqlsrv_query($conn, $sql);

echo "=== ESTRUCTURA DE inst_cab_instructivo ===\n\n";

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo $row['COLUMN_NAME'] . " | " . $row['DATA_TYPE'] . "\n";
}

// Buscar tablas relacionadas con instructivo
echo "\n\n=== TABLAS RELACIONADAS CON INSTRUCTIVO ===\n\n";

$sql2 = "SELECT TABLE_NAME 
         FROM INFORMATION_SCHEMA.TABLES 
         WHERE TABLE_NAME LIKE '%instructivo%' 
         OR TABLE_NAME LIKE '%detalle%'
         ORDER BY TABLE_NAME";

$stmt2 = sqlsrv_query($conn, $sql2);

while ($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
    echo $row['TABLE_NAME'] . "\n";
}

// Estructura de inst_detalle_instructivo
echo "\n\n=== ESTRUCTURA DE inst_detalle_instructivo ===\n\n";

$sql3 = "SELECT COLUMN_NAME, DATA_TYPE 
         FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_NAME = 'inst_detalle_instructivo' 
         ORDER BY ORDINAL_POSITION";

$stmt3 = sqlsrv_query($conn, $sql3);

while ($row = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC)) {
    echo $row['COLUMN_NAME'] . " | " . $row['DATA_TYPE'] . "\n";
}

// Estructura de inst_pedidos
echo "\n\n=== ESTRUCTURA DE inst_pedidos ===\n\n";

$sql4 = "SELECT COLUMN_NAME, DATA_TYPE 
         FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_NAME = 'inst_pedidos' 
         ORDER BY ORDINAL_POSITION";

$stmt4 = sqlsrv_query($conn, $sql4);

while ($row = sqlsrv_fetch_array($stmt4, SQLSRV_FETCH_ASSOC)) {
    echo $row['COLUMN_NAME'] . " | " . $row['DATA_TYPE'] . "\n";
}
?>
