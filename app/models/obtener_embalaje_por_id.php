<?php
require_once("../conexion.php");
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(null);
    exit;
}

$sql = "SELECT id, Codigo_emb as codigo_embalaje, Descripcion_Embalaje as nombre_embalaje,
               Peso_Embalaje as peso_embalaje, id_etiqueta, id_especie, id_exportadora
        FROM inst_embalaje WHERE id = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);

$resultado = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($resultado) {
    echo json_encode($resultado);
} else {
    echo json_encode(null);
}
?>
