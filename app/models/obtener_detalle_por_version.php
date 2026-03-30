<?php
require_once("../conexion.php");
header('Content-Type: application/json');

// Validar entrada
$idInstructivo = isset($_GET['id_instructivo']) ? intval($_GET['id_instructivo']) : 0;
$version = isset($_GET['version']) ? intval($_GET['version']) : 0;


if ($idInstructivo === 0 || $version === 0) {
    echo json_encode(["success" => false, "message" => "Parámetros inválidos"]);
    exit;
}

// Consulta de detalles
$sql = "SELECT
        i.id,
        i.id_cab_instructivo,
        cab.turno,
        i.id_embalaje,
        i.version,
        e.codigo_emb as embalaje,
        i.id_calibre,
        c.nombre_calibre as calibre,
        c.cod_calibre,
        i.id_etiqueta,
        et.Nombre_etiqueta,
        i.cantidad_pedido,
        i.id_categoria,
        ca.nombre_categoria as categoria,
        i.id_plu,
        pl.plu,
        i.id_destino,
        d.nombre_destino,
        i.altura_pallet,
        ISNULL(CAST(ap.altura AS VARCHAR) + ' cm - ' + CAST(ap.cajas AS VARCHAR) + ' cajas', '') as altura_label,
        i.id_pallet,
        p.Descrip_pallet,
        i.observacion,
        i.numero_pedido,
        i.var_etiquetada
    FROM inst_detalle_instructivo i
    INNER JOIN inst_cab_instructivo cab ON cab.id_instructivo = i.id_cab_instructivo
    INNER JOIN inst_embalaje e         ON e.id  = i.id_embalaje
    INNER JOIN inst_calibre c          ON c.id  = i.id_calibre
    INNER JOIN inst_categoria ca       ON ca.id = i.id_categoria
    INNER JOIN inst_plu pl             ON pl.id = i.id_plu
    INNER JOIN inst_destino d          ON d.id  = i.id_destino
    INNER JOIN inst_pallet p           ON p.id  = i.id_pallet
    INNER JOIN inst_etiqueta et        ON et.id = i.id_etiqueta
    LEFT  JOIN inst_altura_pallet ap   ON ap.id = i.altura_pallet
    WHERE i.id_cab_instructivo = ? AND i.version = ?
    ORDER BY i.numero_pedido ASC, c.orden ASC";
$params = [$idInstructivo, $version];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Error al obtener detalles", "detalle" => sqlsrv_errors()]);
    exit;
}

$datos = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    foreach ($row as $key => $value) {
        if ($value instanceof DateTime) {
            $row[$key] = $value->format('Y-m-d H:i:s');
        } elseif (is_object($value)) {
            $row[$key] = (string)$value;
        }
    }
    $datos[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $datos
]);
