<?php
// Guardar nueva versión de instructivo existente
require_once("../conexion.php");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'Datos inválidos']);
    exit();
}

$id_instructivo = $data['id_instructivo'] ?? null;
$version_anterior = $data['version_anterior'] ?? 1;
$nueva_version = $version_anterior + 1;

$cabecera = $data['cabecera'] ?? null;
$pedidos = $data['pedidos'] ?? [];
$detalle = $data['detalle'] ?? [];

if (!$id_instructivo || !$cabecera) {
    echo json_encode(['error' => 'Datos incompletos']);
    exit();
}

try {
    // Iniciar transacción
    sqlsrv_begin_transaction($conn);
    
    // 1. Insertar nueva cabecera (mismo id_instructivo, nueva fecha)
    $sql_cab = "
        INSERT INTO inst_cab_instructivo (id_instructivo, id_exportadora, id_especie, fecha, turno, observacion)
        VALUES (?, ?, ?, ?, ?, ?)
    ";
    $params_cab = [
        $id_instructivo,
        $cabecera['id_exportadora'],
        $cabecera['id_especie'],
        $cabecera['fecha'],
        $cabecera['turno'],
        $cabecera['observacion'] ?? ''
    ];
    sqlsrv_query($conn, $sql_cab, $params_cab);
    
    // 2. Insertar pedidos con nueva versión
    if (!empty($pedidos)) {
        $sql_ped = "
            INSERT INTO inst_pedidos (id_instructivo, version, numero_pedido, cantidad, prioridad)
            VALUES (?, ?, ?, ?, ?)
        ";
        foreach ($pedidos as $pedido) {
            $params_ped = [
                $id_instructivo,
                $nueva_version,
                $pedido['numero_pedido'],
                $pedido['cantidad'],
                $pedido['prioridad'] ?? 1
            ];
            sqlsrv_query($conn, $sql_ped, $params_ped);
        }
    }
    
    // 3. Insertar detalle con nueva versión
    if (!empty($detalle)) {
        $sql_det = "
            INSERT INTO inst_detalle_instructivo (
                id_cab_instructivo, version, numero_pedido, cantidad_pedido,
                id_calibre, id_embalaje, id_categoria, id_plu, id_etiqueta,
                id_pallet, altura_pallet, id_destino, var_etiquetada, observacion
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        foreach ($detalle as $det) {
            // Si hay múltiples calibres, crear un registro por cada uno
            if (!empty($det['calibres'])) {
                foreach ($det['calibres'] as $calibre) {
                    $params_det = [
                        $id_instructivo,
                        $nueva_version,
                        $det['numero_pedido'],
                        $det['cantidad'],
                        $calibre['id'] ?? null,
                        $det['id_embalaje'] ?? null,
                        $det['id_categoria'] ?? null,
                        $det['id_plu'] ?? null,
                        $det['id_etiqueta'] ?? null,
                        $det['id_pallet'] ?? null,
                        $det['altura_pallet'] ?? null,
                        $det['id_destino'] ?? null,
                        $det['variedad_etiquetada'] ?? '',
                        $det['observacion'] ?? ''
                    ];
                    sqlsrv_query($conn, $sql_det, $params_det);
                }
            } else {
                // Sin calibres específicos
                $params_det = [
                    $id_instructivo,
                    $nueva_version,
                    $det['numero_pedido'],
                    $det['cantidad'],
                    null,
                    $det['id_embalaje'] ?? null,
                    $det['id_categoria'] ?? null,
                    $det['id_plu'] ?? null,
                    $det['id_etiqueta'] ?? null,
                    $det['id_pallet'] ?? null,
                    $det['altura_pallet'] ?? null,
                    $det['id_destino'] ?? null,
                    $det['variedad_etiquetada'] ?? '',
                    $det['observacion'] ?? ''
                ];
                sqlsrv_query($conn, $sql_det, $params_det);
            }
        }
    }
    
    // Confirmar transacción
    sqlsrv_commit($conn);
    
    echo json_encode([
        'success' => true,
        'message' => 'Versión ' . $nueva_version . ' creada exitosamente',
        'nueva_version' => $nueva_version,
        'id_instructivo' => $id_instructivo
    ]);
    
} catch (Exception $e) {
    // Rollback en caso de error
    sqlsrv_rollback($conn);
    echo json_encode([
        'error' => 'Error al guardar: ' . $e->getMessage()
    ]);
}
