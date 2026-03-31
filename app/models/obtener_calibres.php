<?php
require_once("../conexion.php");

header('Content-Type: application/json');

// Obtener parámetro de filtro por especie
$id_especie = $_GET['id_especie'] ?? null;

$sql = "SELECT c.id, c.cod_calibre, c.nombre_calibre, c.orden, c.id_especie, e.especie
        FROM inst_calibre c
        LEFT JOIN especie e ON c.id_especie = e.id_especie";

if ($id_especie) {
    $sql .= " WHERE c.id_especie = $id_especie";
}

$sql .= " ORDER BY c.orden, c.cod_calibre";

$stmt = sqlsrv_query($conn, $sql);

$calibres = [];

if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $calibres[] = $row;
    }
    echo json_encode($calibres);
} else {
    echo json_encode([]);
}
?>
