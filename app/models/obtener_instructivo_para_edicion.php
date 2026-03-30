<?php
// Obtener instructivo completo para edición (cabecera + pedidos + detalle)
require_once("../conexion.php");
header('Content-Type: application/json');

$id_instructivo = isset($_GET['id_instructivo']) ? $_GET['id_instructivo'] : null;
$version = isset($_GET['version']) ? $_GET['version'] : null;

if (!$id_instructivo) {
    echo json_encode(['error' => 'ID de instructivo requerido']);
    exit();
}

// Obtener cabecera (la más reciente con ese id_instructivo)
$sql_cab = "
    SELECT TOP 1 cab.id_instructivo, cab.id_exportadora, exp.nombre_exportadora, 
           cab.id_especie, esp.especie, cab.fecha, cab.turno, cab.observacion
    FROM inst_cab_instructivo cab
    INNER JOIN inst_exportadora exp ON exp.id = cab.id_exportadora
    INNER JOIN especie esp ON esp.id_especie = cab.id_especie
    WHERE cab.id_instructivo = ?
    ORDER BY cab.fecha DESC
";

$params = [$id_instructivo];
$stmt = sqlsrv_query($conn, $sql_cab, $params);
$cabecera = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$cabecera) {
    echo json_encode(['error' => 'Instructivo no encontrado']);
    exit();
}

// Manejar fecha
if (isset($cabecera['fecha']) && is_object($cabecera['fecha'])) {
    $cabecera['fecha'] = $cabecera['fecha']->format('Y-m-d');
}

// Obtener pedidos de la versión seleccionada (o la última si no se especifica)
if (!$version) {
    $sql_max_ver = "SELECT MAX(version) as max_version FROM inst_pedidos WHERE id_instructivo = ?";
    $stmt_ver = sqlsrv_query($conn, $sql_max_ver, [$id_instructivo]);
    $row_ver = sqlsrv_fetch_array($stmt_ver, SQLSRV_FETCH_ASSOC);
    $version = $row_ver['max_version'] ?? 1;
}

$sql_pedidos = "
    SELECT numero_pedido, cantidad, prioridad
    FROM inst_pedidos
    WHERE id_instructivo = ? AND version = ?
    ORDER BY prioridad ASC, numero_pedido ASC
";
$stmt_ped = sqlsrv_query($conn, $sql_pedidos, [$id_instructivo, $version]);
$pedidos = [];
while ($row = sqlsrv_fetch_array($stmt_ped, SQLSRV_FETCH_ASSOC)) {
    $pedidos[] = $row;
}

// Obtener detalle de la versión seleccionada
$sql_detalle = "
    SELECT 
        det.id,
        det.numero_pedido,
        det.cantidad_pedido as cantidad,
        det.id_calibre,
        cal.cod_calibre,
        cal.nombre_calibre,
        det.id_embalaje,
        emb.Codigo_emb as codigo_embalaje,
        emb.Descripcion_Embalaje as nombre_embalaje,
        det.id_categoria,
        cat.cod_categoria,
        cat.nombre_categoria as nombre_categoria,
        det.id_plu,
        plu.cod_plu,
        plu.plu as nombre_plu,
        det.id_etiqueta,
        etiq.Cod_etiqueta as codigo_etiqueta,
        etiq.Nombre_etiqueta as nombre_etiqueta,
        det.id_pallet,
        pal.cod_pallet,
        pal.Descrip_pallet as describ_pallet,
        det.altura_pallet,
        alt.altura,
        alt.cajas,
        det.id_destino,
        des.cod_destino,
        des.nombre_destino,
        det.var_etiquetada,
        det.observacion
    FROM inst_detalle_instructivo det
    LEFT JOIN inst_calibre cal ON cal.id = det.id_calibre
    LEFT JOIN inst_embalaje emb ON emb.id = det.id_embalaje
    LEFT JOIN inst_categoria cat ON cat.id = det.id_categoria
    LEFT JOIN inst_plu plu ON plu.id = det.id_plu
    LEFT JOIN inst_etiqueta etiq ON etiq.id = det.id_etiqueta
    LEFT JOIN inst_pallet pal ON pal.id = det.id_pallet
    LEFT JOIN inst_altura_pallet alt ON alt.id = det.altura_pallet
    LEFT JOIN inst_destino des ON des.id = det.id_destino
    WHERE det.id_cab_instructivo = ? AND det.version = ?
    ORDER BY det.numero_pedido ASC, cal.orden ASC
";
$stmt_det = sqlsrv_query($conn, $sql_detalle, [$id_instructivo, $version]);
$detalle = [];
while ($row = sqlsrv_fetch_array($stmt_det, SQLSRV_FETCH_ASSOC)) {
    // Manejar fecha si viene
    if (isset($row['fecha']) && is_object($row['fecha'])) {
        $row['fecha'] = $row['fecha']->format('Y-m-d');
    }
    $detalle[] = $row;
}

// Agrupar detalle por numero_pedido + configuracion para mostrar calibres juntos
$detalleAgrupado = [];
foreach ($detalle as $det) {
    $key = $det['numero_pedido'] . '-' . $det['id_embalaje'] . '-' . $det['id_categoria'];
    
    if (!isset($detalleAgrupado[$key])) {
        $detalleAgrupado[$key] = [
            'numero_pedido' => $det['numero_pedido'],
            'cantidad' => $det['cantidad'],
            'id_embalaje' => $det['id_embalaje'],
            'embalaje_text' => $det['nombre_embalaje'] ?? '',
            'id_categoria' => $det['id_categoria'],
            'categoria_text' => $det['nombre_categoria'] ?? $det['cod_categoria'] ?? '',
            'id_plu' => $det['id_plu'],
            'plu_text' => $det['nombre_plu'] ?? $det['cod_plu'] ?? '',
            'id_etiqueta' => $det['id_etiqueta'],
            'etiqueta_text' => $det['nombre_etiqueta'] ?? '',
            'id_pallet' => $det['id_pallet'],
            'pallet_text' => $det['describ_pallet'] ?? '',
            'altura_pallet' => $det['altura_pallet'],
            'altura_text' => ($det['altura'] ?? '') . ' cm - ' . ($det['cajas'] ?? '') . ' cajas',
            'id_destino' => $det['id_destino'],
            'destino_text' => $det['nombre_destino'] ?? '',
            'variedad_etiquetada' => $det['var_etiquetada'] ?? '',
            'observacion' => $det['observacion'] ?? '',
            'calibres' => []
        ];
    }
    
    if ($det['id_calibre']) {
        $detalleAgrupado[$key]['calibres'][] = [
            'id' => $det['id_calibre'],
            'texto' => $det['cod_calibre'] . ' - ' . $det['nombre_calibre']
        ];
    }
}

echo json_encode([
    'cabecera' => $cabecera,
    'version_actual' => $version,
    'pedidos' => $pedidos,
    'detalle' => array_values($detalleAgrupado)
]);
