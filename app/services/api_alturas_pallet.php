<?php
// archivo: api/get_alturas_pallet.php
include '../app/conexion.php'; // conexiÃ³n a SQL Server

$embalajeId = $_GET['id_embalaje'] ?? null;

if ($embalajeId) {
    $sql = "SELECT id, altura, cajas FROM inst_altura_pallet WHERE id_embalaje = ?";
    $stmt = sqlsrv_prepare($conn, $sql, [$embalajeId]);
    sqlsrv_execute($stmt);

    $data = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $data[] = $row;
    }

    echo json_encode($data);
} else {
    echo json_encode([]);
}
