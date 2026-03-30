<?php
require_once("../conexion.php");

header('Content-Type: text/html; charset=utf-8');

// Leer JSON del body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo "Error: No se recibieron datos válidos";
    exit;
}

$cabecera = $data['cabecera'] ?? null;
$pedidos = $data['pedidos'] ?? [];
$detalle = $data['detalle'] ?? [];

// Validaciones básicas
if (!$cabecera || !is_array($cabecera)) {
    echo "Error: Faltan datos de la cabecera o no es un array válido";
    exit;
}

if (empty($pedidos) || !is_array($pedidos)) {
    echo "Error: Debe agregar al menos un pedido";
    exit;
}

if (empty($detalle) || !is_array($detalle)) {
    echo "Error: Debe agregar al menos un calibre al detalle";
    exit;
}

// Iniciar transacción
sqlsrv_begin_transaction($conn);

try {
    // 1. INSERTAR CABECERA
    $id_exportadora = (int)$cabecera['id_exportadora'];
    $id_especie = (int)$cabecera['id_especie'];
    $turno = sqlsrv_escape_string($cabecera['turno']);
    $fecha = $cabecera['fecha'];
    $observacion = sqlsrv_escape_string($cabecera['observacion'] ?? '');

    // 1. INSERTAR CABECERA
    $sql_cabecera = "
        INSERT INTO inst_cab_instructivo (fecha, id_exportadora, id_especie, turno, observacion)
        VALUES ('$fecha', $id_exportadora, $id_especie, N'$turno', N'$observacion');
    ";

    $stmt = sqlsrv_query($conn, $sql_cabecera);
    if (!$stmt) {
        throw new Exception("Error al insertar cabecera: " . print_r(sqlsrv_errors(), true));
    }

    // Obtener el ID insertado
    $stmt_id = sqlsrv_query($conn, "SELECT SCOPE_IDENTITY() as id_instructivo");
    $row = sqlsrv_fetch_array($stmt_id, SQLSRV_FETCH_ASSOC);
    
    if (!$row || !isset($row['id_instructivo'])) {
        throw new Exception("No se pudo obtener el ID del instructivo insertado");
    }
    
    $id_instructivo = (int)$row['id_instructivo'];
    $version = 1; // Siempre es 1 para nuevos instructivos

    // 2. INSERTAR PEDIDOS
    foreach ($pedidos as $pedido) {
        $numero_pedido = (int)$pedido['numero'];
        $cantidad = sqlsrv_escape_string($pedido['cantidad']);
        $prioridad = (int)$pedido['prioridad'];

        $sql_pedido = "
            INSERT INTO inst_pedidos (id_instructivo, version, numero_pedido, cantidad, prioridad)
            VALUES ($id_instructivo, $version, $numero_pedido, N'$cantidad', $prioridad);
        ";

        $result = sqlsrv_query($conn, $sql_pedido);
        if (!$result) {
            throw new Exception("Error al insertar pedido $numero_pedido: " . print_r(sqlsrv_errors(), true));
        }
    }

    // 3. INSERTAR DETALLE
    foreach ($detalle as $det) {
        $id_calibre = $det['id_calibre'] ? (int)$det['id_calibre'] : 'NULL';
        $numero_pedido = (int)$det['numero_pedido'];
        $cantidad_pedido = sqlsrv_escape_string($det['cantidad'] ?? '0');
        $id_embalaje = $det['id_embalaje'] ? (int)$det['id_embalaje'] : 'NULL';
        $id_categoria = $det['id_categoria'] ? (int)$det['id_categoria'] : 'NULL';
        $id_plu = $det['id_plu'] ? (int)$det['id_plu'] : 'NULL';
        $id_etiqueta = $det['id_etiqueta'] ? (int)$det['id_etiqueta'] : 'NULL';
        $id_pallet = $det['id_pallet'] ? (int)$det['id_pallet'] : 'NULL';
        $altura_pallet = $det['id_altura'] ? (int)$det['id_altura'] : 'NULL';
        $id_destino = $det['id_destino'] ? (int)$det['id_destino'] : 'NULL';
        $var_etiquetada = sqlsrv_escape_string($det['variedad_etiquetada'] ?? '');
        $observacion_det = sqlsrv_escape_string($det['observacion'] ?? '');

        $sql_detalle = "
            INSERT INTO inst_detalle_instructivo (
                id_cab_instructivo, version, id_calibre, numero_pedido, cantidad_pedido,
                id_embalaje, id_categoria, id_plu, id_etiqueta, id_pallet, altura_pallet,
                id_destino, var_etiquetada, observacion
            )
            VALUES (
                $id_instructivo, $version, $id_calibre, $numero_pedido, N'$cantidad_pedido',
                $id_embalaje, $id_categoria, $id_plu, $id_etiqueta, $id_pallet, $altura_pallet,
                $id_destino, N'$var_etiquetada', N'$observacion_det'
            );
        ";

        $result = sqlsrv_query($conn, $sql_detalle);
        if (!$result) {
            throw new Exception("Error al insertar detalle calibre $id_calibre: " . print_r(sqlsrv_errors(), true));
        }
    }

    // Confirmar transacción
    sqlsrv_commit($conn);

    echo "✅ Instructivo creado exitosamente (ID: $id_instructivo, Versión: $version)";

} catch (Exception $e) {
    // Revertir transacción en caso de error
    sqlsrv_rollback($conn);
    echo "❌ Error: " . $e->getMessage();
}

// Función para escapar strings (compatible con SQL Server)
function sqlsrv_escape_string($str) {
    if ($str === null) return '';
    return str_replace("'", "''", $str);
}
?>
