<?php
require_once("../conexion.php");

// Ver estructura exacta de inst_detalle_instructivo
$sql = "SELECT COLUMN_NAME, DATA_TYPE 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = 'inst_detalle_instructivo' 
        ORDER BY ORDINAL_POSITION";

$stmt = sqlsrv_query($conn, $sql);

echo "=== CAMPOS EN inst_detalle_instructivo ===\n\n";

if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo $row['COLUMN_NAME'] . " | " . $row['DATA_TYPE'] . "\n";
    }
} else {
    echo "Error: " . print_r(sqlsrv_errors(), true);
}
?>
