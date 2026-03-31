<?php
require_once("../conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'guardar':
            guardar($conn);
            break;
        case 'modificar':
            modificar($conn);
            break;
        case 'eliminar':
            eliminar($conn);
            break;
        default:
            echo "Acción no válida";
    }
}

function guardar($conn) {
    $codigo         = $_POST['cod_pallet']      ?? '';
    $descripcion    = $_POST['descrip_pallet']  ?? '';
    $id_exportadora = $_POST['id_exportadora']  ?? null;

    if (empty($codigo) || empty($descripcion)) {
        echo "Error: Código y descripción son obligatorios";
        return;
    }

    $checkRow = sqlsrv_fetch_array(
        sqlsrv_query($conn,
            "SELECT COUNT(*) AS total FROM inst_pallet WHERE cod_pallet = ? AND id_exportadora = ?",
            [$codigo, $id_exportadora]
        ),
        SQLSRV_FETCH_ASSOC
    );
    if ($checkRow && $checkRow['total'] > 0) {
        echo "Error: Ya existe un pallet con ese código para la exportadora seleccionada";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "INSERT INTO inst_pallet (cod_pallet, descrip_pallet, id_exportadora) VALUES (?, ?, ?)",
        [$codigo, $descripcion, $id_exportadora ?: null]
    );

    if ($stmt) {
        echo "Pallet guardado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al guardar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function modificar($conn) {
    $id             = $_POST['id_pallet']       ?? null;
    $codigo         = $_POST['cod_pallet']      ?? '';
    $descripcion    = $_POST['descrip_pallet']  ?? '';
    $id_exportadora = $_POST['id_exportadora']  ?? null;

    if (empty($id) || empty($codigo) || empty($descripcion)) {
        echo "Error: Datos incompletos";
        return;
    }

    $checkRow = sqlsrv_fetch_array(
        sqlsrv_query($conn,
            "SELECT COUNT(*) AS total FROM inst_pallet WHERE cod_pallet = ? AND id_exportadora = ? AND id_pallet <> ?",
            [$codigo, $id_exportadora, $id]
        ),
        SQLSRV_FETCH_ASSOC
    );
    if ($checkRow && $checkRow['total'] > 0) {
        echo "Error: Ya existe un pallet con ese código para la exportadora seleccionada";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "UPDATE inst_pallet SET cod_pallet = ?, descrip_pallet = ?, id_exportadora = ? WHERE id_pallet = ?",
        [$codigo, $descripcion, $id_exportadora ?: null, $id]
    );

    if ($stmt) {
        echo "Pallet modificado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al modificar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function eliminar($conn) {
    $id = $_POST['id_pallet'] ?? null;

    if (empty($id)) {
        echo "Error: ID no válido";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "DELETE FROM inst_pallet WHERE id_pallet = ?",
        [$id]
    );

    if ($stmt) {
        echo "Pallet eliminado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al eliminar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}
?>
