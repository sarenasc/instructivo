<?php
require_once("../conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    switch ($accion) {
        case 'guardar':   guardar($conn);   break;
        case 'modificar': modificar($conn); break;
        case 'eliminar':  eliminar($conn);  break;
        default: echo "Acción no válida";
    }
}

function validarCampos() {
    $id_especie     = $_POST['id_especie']     ?? null;
    $id_exportadora = $_POST['id_exportadora'] ?? null;
    $id_categoria   = $_POST['id_categoria']   ?? null;
    $nombre_grupo   = trim($_POST['nombre_grupo'] ?? '');
    $calibres       = $_POST['calibres']       ?? [];

    if (!$id_especie || !$id_exportadora || !$id_categoria || empty($nombre_grupo)) {
        return ["error" => "Especie, exportadora, categoría y nombre de grupo son obligatorios"];
    }
    if (empty($calibres)) {
        return ["error" => "Debe seleccionar al menos un calibre"];
    }
    return [
        "id_especie"     => $id_especie,
        "id_exportadora" => $id_exportadora,
        "id_categoria"   => $id_categoria,
        "nombre_grupo"   => $nombre_grupo,
        "calibres"       => (array)$calibres,
    ];
}

function guardar($conn) {
    $datos = validarCampos();
    if (isset($datos['error'])) { echo "Error: " . $datos['error']; return; }

    // Verificar duplicado
    $check = sqlsrv_fetch_array(
        sqlsrv_query($conn,
            "SELECT COUNT(*) AS total FROM inst_agrupacion_calibre
             WHERE id_especie = ? AND id_exportadora = ? AND id_categoria = ? AND nombre_grupo = ?",
            [$datos['id_especie'], $datos['id_exportadora'], $datos['id_categoria'], $datos['nombre_grupo']]
        ), SQLSRV_FETCH_ASSOC
    );
    if ($check && $check['total'] > 0) {
        echo "Error: Ya existe ese grupo para la combinación seleccionada"; return;
    }

    sqlsrv_begin_transaction($conn);
    try {
        // Insertar cabecera
        $stmt = sqlsrv_query($conn,
            "INSERT INTO inst_agrupacion_calibre (id_especie, id_exportadora, id_categoria, nombre_grupo)
             OUTPUT INSERTED.id VALUES (?, ?, ?, ?)",
            [$datos['id_especie'], $datos['id_exportadora'], $datos['id_categoria'], $datos['nombre_grupo']]
        );
        if (!$stmt) throw new Exception(sqlsrv_errors()[0]['message'] ?? 'Error al insertar');
        sqlsrv_fetch($stmt);
        $id_agrupacion = sqlsrv_get_field($stmt, 0);

        // Insertar detalle de calibres
        foreach ($datos['calibres'] as $id_calibre) {
            $r = sqlsrv_query($conn,
                "INSERT INTO inst_agrupacion_calibre_detalle (id_agrupacion, id_calibre) VALUES (?, ?)",
                [$id_agrupacion, $id_calibre]
            );
            if (!$r) throw new Exception(sqlsrv_errors()[0]['message'] ?? 'Error en detalle');
        }

        sqlsrv_commit($conn);
        echo "Agrupación guardada correctamente";
    } catch (Exception $ex) {
        sqlsrv_rollback($conn);
        echo "Error al guardar: " . $ex->getMessage();
    }
}

function modificar($conn) {
    $id = $_POST['id'] ?? null;
    if (!$id) { echo "Error: ID no válido"; return; }

    $datos = validarCampos();
    if (isset($datos['error'])) { echo "Error: " . $datos['error']; return; }

    // Verificar duplicado excluyendo registro actual
    $check = sqlsrv_fetch_array(
        sqlsrv_query($conn,
            "SELECT COUNT(*) AS total FROM inst_agrupacion_calibre
             WHERE id_especie = ? AND id_exportadora = ? AND id_categoria = ? AND nombre_grupo = ? AND id <> ?",
            [$datos['id_especie'], $datos['id_exportadora'], $datos['id_categoria'], $datos['nombre_grupo'], $id]
        ), SQLSRV_FETCH_ASSOC
    );
    if ($check && $check['total'] > 0) {
        echo "Error: Ya existe ese grupo para la combinación seleccionada"; return;
    }

    sqlsrv_begin_transaction($conn);
    try {
        // Actualizar cabecera
        $r = sqlsrv_query($conn,
            "UPDATE inst_agrupacion_calibre
             SET id_especie = ?, id_exportadora = ?, id_categoria = ?, nombre_grupo = ?
             WHERE id = ?",
            [$datos['id_especie'], $datos['id_exportadora'], $datos['id_categoria'], $datos['nombre_grupo'], $id]
        );
        if (!$r) throw new Exception(sqlsrv_errors()[0]['message'] ?? 'Error al actualizar');

        // Reemplazar detalle (ON DELETE CASCADE elimina los viejos al borrar, pero aquí borramos directamente)
        $rd = sqlsrv_query($conn,
            "DELETE FROM inst_agrupacion_calibre_detalle WHERE id_agrupacion = ?", [$id]
        );
        if (!$rd) throw new Exception(sqlsrv_errors()[0]['message'] ?? 'Error al limpiar detalle');

        foreach ($datos['calibres'] as $id_calibre) {
            $ri = sqlsrv_query($conn,
                "INSERT INTO inst_agrupacion_calibre_detalle (id_agrupacion, id_calibre) VALUES (?, ?)",
                [$id, $id_calibre]
            );
            if (!$ri) throw new Exception(sqlsrv_errors()[0]['message'] ?? 'Error en detalle');
        }

        sqlsrv_commit($conn);
        echo "Agrupación modificada correctamente";
    } catch (Exception $ex) {
        sqlsrv_rollback($conn);
        echo "Error al modificar: " . $ex->getMessage();
    }
}

function eliminar($conn) {
    $id = $_POST['id'] ?? null;
    if (!$id) { echo "Error: ID no válido"; return; }

    // El ON DELETE CASCADE elimina el detalle automáticamente
    $stmt = sqlsrv_query($conn, "DELETE FROM inst_agrupacion_calibre WHERE id = ?", [$id]);
    echo $stmt ? "Agrupación eliminada correctamente"
               : "Error al eliminar: " . (sqlsrv_errors()[0]['message'] ?? 'Desconocido');
}
?>
