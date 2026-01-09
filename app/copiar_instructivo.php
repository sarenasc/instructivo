<?php
require_once("conexion.php");
header('Content-Type: application/json');

// Validar entrada
$data = json_decode(file_get_contents("php://input"), true);
$idInstructivoOriginal = $data['id_instructivo'] ?? null;
$versionOriginal = $data['version'] ?? null;
$nuevaFecha = $data['fecha'] ?? null;
$turno =$data['turno'] ?? null;

if (!$idInstructivoOriginal || !$versionOriginal || !$nuevaFecha) {
    echo json_encode(["success" => false, "message" => "Faltan parámetros"]);
    exit;
}

// Obtener cabecera original
$sqlCab = "SELECT * FROM inst_cab_instructivo WHERE id_instructivo = ?";
$stmtCab = sqlsrv_query($conn, $sqlCab, [$idInstructivoOriginal]);
$cabecera = sqlsrv_fetch_array($stmtCab, SQLSRV_FETCH_ASSOC);

if (!$cabecera) {
    echo json_encode(["success" => false, "message" => "No se encontró la cabecera"]);
    exit;
}

// Insertar nueva cabecera con nueva fecha
$sqlInsertCab = "INSERT INTO inst_cab_instructivo (id_exportadora, id_especie, fecha, observacion,turno)
                 VALUES (?, ?, ?, ?,?)";
$paramsCab = [
    $cabecera['id_exportadora'],
    $cabecera['id_especie'],
    $nuevaFecha,
    $cabecera['observacion'],
    $turno
];

$stmtInsertCab = sqlsrv_query($conn, $sqlInsertCab, $paramsCab);

if (!$stmtInsertCab) {
    echo json_encode(["success" => false, "message" => "Error al insertar cabecera", "detalle" => sqlsrv_errors()]);
    exit;
}


// Obtener el nuevo ID de instructivo
$sqlGetNewId = "SELECT top 1 id_instructivo AS new_id from inst_cab_instructivo order by id_instructivo desc";
$stmtNewId = sqlsrv_query($conn, $sqlGetNewId);
$rowNewId = sqlsrv_fetch_array($stmtNewId, SQLSRV_FETCH_ASSOC);
$nuevoIdInstructivo = $rowNewId['new_id'];

if (!$nuevoIdInstructivo) {
    echo json_encode(["success" => false, "message" => "No se pudo obtener el nuevo ID de instructivo"]);
    exit;
}



// Obtener detalles originales
$sqlDet = "SELECT * FROM inst_detalle_instructivo WHERE id_cab_instructivo = ? AND version = ?";
$stmtDet = sqlsrv_query($conn, $sqlDet, [$idInstructivoOriginal, $versionOriginal]);

if (!$stmtDet) {
    echo json_encode(["success" => false, "message" => "Error al obtener detalles", "detalle" => sqlsrv_errors()]);
    exit;
}

// Insertar detalles con nueva versión
while ($detalle = sqlsrv_fetch_array($stmtDet, SQLSRV_FETCH_ASSOC)) {
    $sqlInsertDet = "INSERT INTO inst_detalle_instructivo (
        id_cab_instructivo, version, numero_pedido, var_etiquetada, id_embalaje, id_etiqueta,
        id_destino, id_plu, id_categoria, id_pallet, altura_pallet, observacion, id_calibre, cantidad_pedido
    ) VALUES (?, 1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $paramsDet = [
        $nuevoIdInstructivo,
        $detalle['numero_pedido'],
        $detalle['var_etiquetada'],
        $detalle['id_embalaje'],
        $detalle['id_etiqueta'],
        $detalle['id_destino'],
        $detalle['id_plu'],
        $detalle['id_categoria'],
        $detalle['id_pallet'],
        $detalle['altura_pallet'],
        $detalle['observacion'],
        $detalle['id_calibre'],
        $detalle['cantidad_pedido']
    ];

    $stmtInsertDet = sqlsrv_query($conn, $sqlInsertDet, $paramsDet);

    if (!$stmtInsertDet) {
        echo json_encode(["success" => false, "message" => "Error al insertar detalle", "detalle" => sqlsrv_errors()]);
        exit;
    }
}

echo json_encode(["success" => true, "message" => "Instructivo copiado exitosamente", "nuevo_id" => $nuevoIdInstructivo]);

sqlsrv_close($conn);
?>
