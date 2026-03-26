<?php
require_once("../conexion.php");

header('Content-Type: application/json');

$sql = "SELECT c.id_calibre, c.codigo_calibre, c.nombre_calibre, c.id_especie, e.especie
        FROM calibre c
        LEFT JOIN especie e ON c.id_especie = e.id_especie
        ORDER BY c.codigo_calibre";

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
