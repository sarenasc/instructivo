<?php
require_once("conexion.php");
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$idInstructivoOriginal = $data['id_instructivo'] ?? null;
$versionOriginal       = $data['version']        ?? null;
$nuevaFecha            = $data['fecha']           ?? null;
$turno                 = $data['turno']           ?? null;
$detalles              = $data['detalles']        ?? null;

if (!$idInstructivoOriginal || !$versionOriginal || !$nuevaFecha || !$turno) {
    echo json_encode(["success" => false, "message" => "Faltan parámetros"]);
    exit;
}

if (empty($detalles)) {
    echo json_encode(["success" => false, "message" => "El detalle no puede estar vacío"]);
    exit;
}

// Obtener cabecera original
$stmtCab = sqlsrv_query($conn,
    "SELECT * FROM inst_cab_instructivo WHERE id_instructivo = ?",
    [$idInstructivoOriginal]
);
$cabecera = sqlsrv_fetch_array($stmtCab, SQLSRV_FETCH_ASSOC);

if (!$cabecera) {
    echo json_encode(["success" => false, "message" => "No se encontró la cabecera"]);
    exit;
}

// Insertar nueva cabecera
$stmtInsertCab = sqlsrv_query($conn,
    "INSERT INTO inst_cab_instructivo (id_exportadora, id_especie, fecha, observacion, turno)
     VALUES (?, ?, ?, ?, ?)",
    [
        $cabecera['id_exportadora'],
        $cabecera['id_especie'],
        $nuevaFecha,
        $cabecera['observacion'],
        $turno
    ]
);

if (!$stmtInsertCab) {
    echo json_encode(["success" => false, "message" => "Error al insertar cabecera", "detalle" => sqlsrv_errors()]);
    exit;
}

// Obtener nuevo ID
$rowNewId = sqlsrv_fetch_array(
    sqlsrv_query($conn, "SELECT TOP 1 id_instructivo AS new_id FROM inst_cab_instructivo ORDER BY id_instructivo DESC"),
    SQLSRV_FETCH_ASSOC
);
$nuevoId = $rowNewId['new_id'] ?? null;

if (!$nuevoId) {
    echo json_encode(["success" => false, "message" => "No se pudo obtener el nuevo ID de instructivo"]);
    exit;
}

// Insertar solo las filas enviadas desde el frontend (con posibles cambios de pedido)
$sqlDet = "INSERT INTO inst_detalle_instructivo (
    id_cab_instructivo, version, numero_pedido, var_etiquetada,
    id_embalaje, id_etiqueta, id_destino, id_plu,
    id_categoria, id_pallet, altura_pallet, observacion,
    id_calibre, cantidad_pedido
) VALUES (?, 1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

foreach ($detalles as $fila) {
    $params = [
        $nuevoId,
        $fila['numero_pedido']  ?? null,
        $fila['var_etiquetada'] ?? null,
        $fila['id_embalaje']    ?? null,
        $fila['id_etiqueta']    ?? null,
        $fila['id_destino']     ?? null,
        $fila['id_plu']         ?? null,
        $fila['id_categoria']   ?? null,
        $fila['id_pallet']      ?? null,
        $fila['altura_pallet']  ?? null,
        $fila['observacion']    ?? null,
        $fila['id_calibre']     ?? null,
        $fila['cantidad_pedido']?? null,
    ];

    $stmt = sqlsrv_query($conn, $sqlDet, $params);
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error al insertar detalle", "detalle" => sqlsrv_errors()]);
        exit;
    }
}

echo json_encode(["success" => true, "message" => "Instructivo copiado exitosamente", "nuevo_id" => $nuevoId]);
sqlsrv_close($conn);
?>
