<?php
// Archivo: Procesos/procesar_instructivo.php

include_once("conexion.php");

// Leer datos desde POST
$fecha          = $_POST['fecha'] ?? null;
$id_exportadora = $_POST['id_exportadora'] ?? null;
$id_especie     = $_POST['especie'] ?? null;
$turno          = $_POST['turno'] ?? null;
$observacion    = $_POST['observacion'] ?? null;
$version        = $_POST['version'] ?? null;
$id_instructivo = $_POST['id_instructivo'] ?? null; // Solo si es edición

// Validación mínima
if (!$fecha || !$id_exportadora || !$id_especie || !$turno || !$version) {
    echo json_encode(["success" => false, "message" => "Faltan datos obligatorios."]);
    exit;
}

if ($id_instructivo) {
    // Modo edición: actualizar instructivo existente
    $sql = "UPDATE inst_cab_instructivo
            SET fecha = ?, id_exportadora = ?, id_especie = ?, turno = ?, observacion = ?
            WHERE id_instructivo = ?";
    $params = [$fecha, $id_exportadora, $id_especie, $turno, $observacion, $id_instructivo];
} else {
    // Modo nuevo: insertar nuevo instructivo
    $sql = "INSERT INTO inst_cab_instructivo (fecha, id_exportadora, id_especie, turno, observacion)
            OUTPUT INSERTED.id_instructivo
            VALUES (?, ?, ?, ?, ?)";
    $params = [$fecha, $id_exportadora, $id_especie, $turno, $observacion];
}

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt) {
    if (!$id_instructivo) {
        sqlsrv_fetch($stmt);
        $id_instructivo = sqlsrv_get_field($stmt, 0);
    }
    echo json_encode([
        "success" => true,
        "message" => "Cabecera guardada correctamente",
        "id_instructivo" => $id_instructivo,
        "version" => $version
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Error al guardar cabecera."]);
}
