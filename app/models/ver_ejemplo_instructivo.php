<?php
require_once(__DIR__ . '/../conexion.php');

header('Content-Type: application/json; charset=utf-8');

// Ver algunos instructivos existentes con su detalle
$sql = "
SELECT TOP 5 
    cab.id_instructivo,
    cab.fecha,
    exp.Nombre_Exportadora,
    esp.especie,
    cab.turno,
    cab.observacion
FROM inst_cab_instructivo cab
INNER JOIN inst_exportadora exp ON exp.id = cab.id_exportadora
INNER JOIN especie esp ON esp.id_especie = cab.id_especie
ORDER BY cab.id_instructivo DESC
";

$stmt = sqlsrv_query($conn, $sql);
$cabeceras = [];

if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        if ($row['fecha'] instanceof DateTime) {
            $row['fecha'] = $row['fecha']->format('d/m/Y');
        }
        $cabeceras[] = $row;
    }
}

// Ver detalle de un instructivo específico (el más reciente)
if (count($cabeceras) > 0) {
    $id_instructivo = $cabeceras[0]['id_instructivo'];
    
    $sql_detalle = "
    SELECT 
        det.id,
        det.version,
        det.id_calibre,
        cal.cod_calibre,
        det.id_embalaje,
        emb.Codigo_emb,
        det.cantidad_pedido,
        det.id_categoria,
        cat.cod_categoria,
        det.id_plu,
        plu.plu,
        det.numero_pedido,
        det.id_etiqueta,
        etq.Nombre_etiqueta
    FROM inst_detalle_instructivo det
    LEFT JOIN inst_calibre cal ON cal.id = det.id_calibre
    LEFT JOIN inst_embalaje emb ON emb.id = det.id_embalaje
    LEFT JOIN inst_categoria cat ON cat.id = det.id_categoria
    LEFT JOIN inst_plu plu ON plu.id = det.id_plu
    LEFT JOIN inst_etiqueta etq ON etq.id = det.id_etiqueta
    WHERE det.id_cab_instructivo = $id_instructivo
    ORDER BY det.id
    ";
    
    $stmt_detalle = sqlsrv_query($conn, $sql_detalle);
    $detalle = [];
    
    if ($stmt_detalle) {
        while ($row = sqlsrv_fetch_array($stmt_detalle, SQLSRV_FETCH_ASSOC)) {
            $detalle[] = $row;
        }
    }
    
    // Ver pedidos
    $sql_pedidos = "
    SELECT 
        id_pedido,
        version,
        numero_pedido,
        cantidad,
        prioridad
    FROM inst_pedidos
    WHERE id_instructivo = $id_instructivo
    ORDER BY numero_pedido
    ";
    
    $stmt_pedidos = sqlsrv_query($conn, $sql_pedidos);
    $pedidos = [];
    
    if ($stmt_pedidos) {
        while ($row = sqlsrv_fetch_array($stmt_pedidos, SQLSRV_FETCH_ASSOC)) {
            $pedidos[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'cabeceras' => $cabeceras,
        'detalle_ejemplo' => [
            'id_instructivo' => $id_instructivo,
            'detalle' => $detalle,
            'pedidos' => $pedidos
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['success' => false, 'message' => 'No hay instructivos']);
}
?>
