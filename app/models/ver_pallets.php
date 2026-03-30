<?php
require_once("../conexion.php");

// Ver estructura de inst_pallet
$sql = "SELECT TOP 5 * FROM inst_pallet";
$stmt = sqlsrv_query($conn, $sql);

echo "=== CAMPOS EN inst_pallet ===\n\n";

if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "id: " . ($row['id'] ?? 'NULL') . "\n";
        echo "cod_pallet: " . ($row['cod_pallet'] ?? 'NULL') . "\n";
        echo "Descrip_pallet: " . ($row['Descrip_pallet'] ?? 'NULL') . "\n";
        echo "id_exportadora: " . ($row['id_exportadora'] ?? 'NULL') . "\n";
        echo "---\n";
    }
} else {
    echo "Error: " . print_r(sqlsrv_errors(), true);
}
?>
