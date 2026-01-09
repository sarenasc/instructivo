<?php
include 'conexion.php';

$accion = $_POST['accion'] ?? '';

$codigo_embalaje = $_POST['codigo_embalaje'] ?? '';
$nombre_embalaje = $_POST['nombre_embalaje'] ?? '';
$peso_embalaje = $_POST['peso_embalaje'] ?? '';
$etiqueta = $_POST['etiqueta'] ?? '';
$especie = $_POST['especie'] ?? '';
$exportadora = $_POST['exportadora'] ?? '';

if (!$codigo_embalaje || !$nombre_embalaje || !$etiqueta || !$especie || !$peso_embalaje) {
    echo "Todos los campos son obligatorios.";
    exit;
}

if ($accion == "guardar") {
    $sql = "INSERT INTO inst_embalaje
            (Codigo_emb
           ,Descripcion_Embalaje
           ,Peso_Embalaje
           ,id_etiqueta
           ,id_especie
           ,id_exportadora) VALUES (?, ?, ?, ?, ?, ?)";
} elseif ($accion == "modificar") {
    $sql = "UPDATE inst_embalaje SET Codigo_emb = ? ,Descripcion_Embalaje = ?, id_etiqueta = ?, id_especie = ?, Peso_embalaje = ?, id_exportadora = ? WHERE Codigo_emb = $codigo_calibre and id_especie = $especie";
} elseif ($accion == "eliminar") {
    $sql = "DELETE FROM inst_embalaje WHERE Codigo_emb = ? and id_especie = ?";
} else {
    echo "Acción no válida.";
    exit;
}

$params = ($accion == "eliminar") ? [$codigo_embalaje, $especie] : [$codigo_embalaje, $nombre_embalaje, $peso_embalaje, $etiqueta, $especie, $exportadora];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    // Muestra errores detallados
    die(print_r(sqlsrv_errors(), true));
}

if ($stmt) {
    echo ucfirst($accion) . " realizado con éxito.";
} else {
    echo "Error en la operación.";
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
