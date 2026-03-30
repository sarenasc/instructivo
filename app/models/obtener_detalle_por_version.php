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
$sql = " SELECT 
        i.id,
		i.id_cab_instructivo,
        cab.turno,
       i.id_embalaje,
	   i.version,
	   e.codigo_emb as embalaje,
       i.id_calibre,
	   c.nombre_calibre as calibre,
	   i.id_etiqueta,
	   et.Nombre_etiqueta,
       i.cantidad_pedido,
       i.id_categoria,
	   ca.nombre_categoria as categoria,
       i.id_plu,
	   pl.plu,
        i.id,
		i.id_destino,
		d.nombre_destino,
        i.altura_pallet,
        i.id_pallet,
		p.Descrip_pallet,
        i.observacion,
        i.numero_pedido,
        i.var_etiquetada
    FROM inst_detalle_instructivo i    
    inner join inst_cab_instructivo as cab on cab.id_instructivo = i.id_cab_instructivo
	inner join inst_embalaje as e on e.id = i.id_embalaje
	inner join inst_calibre as c on c.id = i.id_calibre
	inner join inst_categoria as ca on ca.id = i.id_categoria
	inner join inst_plu as pl on pl.id = i.id_plu
	inner join inst_destino as d on d.id=i.id_destino
	inner join inst_pallet as p on p.id = i.id_pallet
	inner join inst_etiqueta as et on et.id = i.id_etiqueta
where id_cab_instructivo = ? AND version = ?
order by i.numero_pedido ASC";
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
