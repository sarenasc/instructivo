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
$cabecera = $data['cabecera'] ?? null;
$pedidos = $data['pedidos'] ?? [];
$detalle = $data['detalle'] ?? [];

if (!$id_instructivo || !$cabecera) {
    echo json_encode(['error' => 'Datos incompletos']);
    exit();
}

try {
    // Obtener la versión máxima actual desde la BD
    $sqlMaxVer = "SELECT MAX(version) as max_version FROM inst_detalle_instructivo WHERE id_cab_instructivo = ?";
    $stmtMaxVer = sqlsrv_query($conn, $sqlMaxVer, [$id_instructivo]);
    if (!$stmtMaxVer) {
        throw new Exception('Error al obtener versión máxima');
    }
    $rowMaxVer = sqlsrv_fetch_array($stmtMaxVer, SQLSRV_FETCH_ASSOC);
    $nueva_version = (int)($rowMaxVer['max_version'] ?? 0) + 1;

    // Iniciar transacción
    sqlsrv_begin_transaction($conn);
    
    // 1. Actualizar cabecera existente
    $sql_cab = "
        UPDATE inst_cab_instructivo
        SET id_exportadora = ?, id_especie = ?, fecha = ?, turno = ?, observacion = ?
        WHERE id_instructivo = ?
    ";
    $params_cab = [
        $cabecera['id_exportadora'],
        $cabecera['id_especie'],
        $cabecera['fecha'],
        $cabecera['turno'],
        $cabecera['observacion'] ?? '',
        $id_instructivo
    ];
    $r_cab = sqlsrv_query($conn, $sql_cab, $params_cab);
    if ($r_cab === false) {
        $err = sqlsrv_errors();
        throw new Exception('Error al actualizar cabecera: ' . ($err ? $err[0]['message'] : 'desconocido'));
    }
    
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
            $r = sqlsrv_query($conn, $sql_ped, $params_ped);
            if ($r === false) {
                $err = sqlsrv_errors();
                throw new Exception('Error en pedido: ' . ($err ? $err[0]['message'] : 'desconocido'));
            }
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
            $calibres = $det['calibres'] ?? [];

            // Normalizar altura_pallet: si viene null o vacío, dejar null
            $altura = isset($det['altura_pallet']) && $det['altura_pallet'] !== '' ? (int)$det['altura_pallet'] : null;

            if (!empty($calibres)) {
                foreach ($calibres as $calibre) {
                    $id_calibre = isset($calibre['id']) && $calibre['id'] !== '' ? (int)$calibre['id'] : null;
                    $params_det = [
                        $id_instructivo,
                        $nueva_version,
                        $det['numero_pedido'],
                        $det['cantidad'],
                        $id_calibre,
                        $det['id_embalaje']  ?? null,
                        $det['id_categoria'] ?? null,
                        $det['id_plu']       ?? null,
                        $det['id_etiqueta']  ?? null,
                        $det['id_pallet']    ?? null,
                        $altura,
                        $det['id_destino']   ?? null,
                        $det['variedad_etiquetada'] ?? '',
                        $det['observacion']  ?? ''
                    ];
                    $r = sqlsrv_query($conn, $sql_det, $params_det);
                    if ($r === false) {
                        $err = sqlsrv_errors();
                        throw new Exception('Error en detalle (calibre ' . $id_calibre . '): ' . ($err ? $err[0]['message'] : 'desconocido'));
                    }
                }
            } else {
                $params_det = [
                    $id_instructivo,
                    $nueva_version,
                    $det['numero_pedido'],
                    $det['cantidad'],
                    null,
                    $det['id_embalaje']  ?? null,
                    $det['id_categoria'] ?? null,
                    $det['id_plu']       ?? null,
                    $det['id_etiqueta']  ?? null,
                    $det['id_pallet']    ?? null,
                    $altura,
                    $det['id_destino']   ?? null,
                    $det['variedad_etiquetada'] ?? '',
                    $det['observacion']  ?? ''
                ];
                $r = sqlsrv_query($conn, $sql_det, $params_det);
                if ($r === false) {
                    $err = sqlsrv_errors();
                    throw new Exception('Error en detalle sin calibre: ' . ($err ? $err[0]['message'] : 'desconocido'));
                }
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
