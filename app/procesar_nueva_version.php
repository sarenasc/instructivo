<?php
require_once("conexion.php");
header('Content-Type: application/json');

// Leer los datos enviados en JSON
$input = json_decode(file_get_contents("php://input"), true);

$idInstructivo = isset($input["id_instructivo"]) ? intval($input["id_instructivo"]) : 0;
$detalles = isset($input["detalle"]) ? $input["detalle"] : [];

if ($idInstructivo === 0 || !is_array($detalles) || empty($detalles)) {
    echo json_encode(["success" => false, "message" => "Datos inválidos"]);
    exit;
}

// Obtener la versión actual más alta
$sqlVersion = "SELECT MAX(version) as max_version FROM inst_detalle_instructivo WHERE id_cab_instructivo = ?";
$stmtVersion = sqlsrv_query($conn, $sqlVersion, [$idInstructivo]);

if (!$stmtVersion) {
    echo json_encode(["success" => false, "message" => "Error al obtener versión", "detalle" => sqlsrv_errors()]);
    exit;
}

$row = sqlsrv_fetch_array($stmtVersion, SQLSRV_FETCH_ASSOC);
$nuevaVersion = ($row && $row['max_version']) ? $row['max_version'] + 1 : 1;

// Insertar cada línea del detalle con la nueva versión
$sqlInsert = "INSERT INTO inst_detalle_instructivo 
(id_cab_instructivo
           ,version
           ,id_embalaje
           ,id_calibre
           ,id_etiqueta
           ,cantidad_pedido
           ,id_categoria
           ,id_plu
           ,id_destino
           ,altura_pallet
           ,id_pallet
           ,observacion
           ,numero_pedido
           ,var_etiquetada)
VALUES (?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?,?,?)";

foreach ($detalles as $detalle) {
    $params = [
        $idInstructivo,
        $nuevaVersion,
        $detalle["id_embalaje"] ?? null,
        $detalle["id_calibre"] ?? null,
        $detalle["id_etiqueta"] ?? null,
        $detalle["cantidad_pedido"] ?? null,
        $detalle["id_categoria"] ?? null,
        $detalle["id_plu"] ?? null,
        $detalle["id_destino"] ?? null,
        $detalle["altura_pallet"] ?? null,
        $detalle["id_pallet"] ?? null,
        $detalle["observacion"] ?? null,
        $detalle["numero_pedido"] ?? null,
        $detalle["var_etiquetada"] ?? null
    ];

    $stmtInsert = sqlsrv_query($conn, $sqlInsert, $params);

    if (!$stmtInsert) {
        echo json_encode(["success" => false, "message" => "Error al guardar detalle", "detalle" => sqlsrv_errors()]);
        exit;
    }
}

// Respuesta final
echo json_encode(["success" => true, "message" => "Nueva versión guardada", "nueva_version" => $nuevaVersion]);
