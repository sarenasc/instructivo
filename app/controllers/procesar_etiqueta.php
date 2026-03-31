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
    $codigo         = $_POST['codigo_etiqueta'] ?? '';
    $nombre         = $_POST['nombre_etiqueta'] ?? '';
    $id_exportadora = $_POST['exportadora']     ?? null;

    if (empty($codigo) || empty($nombre)) {
        echo "Error: Código y nombre son obligatorios";
        return;
    }

    $checkRow = sqlsrv_fetch_array(
        sqlsrv_query($conn,
            "SELECT COUNT(*) AS total FROM inst_etiqueta WHERE Cod_etiqueta = ? AND id_exportadora = ?",
            [$codigo, $id_exportadora]
        ),
        SQLSRV_FETCH_ASSOC
    );
    if ($checkRow && $checkRow['total'] > 0) {
        echo "Error: Ya existe una etiqueta con ese código para la exportadora seleccionada";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "INSERT INTO inst_etiqueta (Cod_etiqueta, Nombre_etiqueta, id_exportadora) VALUES (?, ?, ?)",
        [$codigo, $nombre, $id_exportadora ?: null]
    );

    if ($stmt) {
        echo "Etiqueta guardada correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al guardar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function modificar($conn) {
    $id             = $_POST['id_etiqueta']     ?? null;
    $codigo         = $_POST['codigo_etiqueta'] ?? '';
    $nombre         = $_POST['nombre_etiqueta'] ?? '';
    $id_exportadora = $_POST['exportadora']     ?? null;

    if (empty($id) || empty($codigo) || empty($nombre)) {
        echo "Error: Datos incompletos";
        return;
    }

    $checkRow = sqlsrv_fetch_array(
        sqlsrv_query($conn,
            "SELECT COUNT(*) AS total FROM inst_etiqueta WHERE Cod_etiqueta = ? AND id_exportadora = ? AND id <> ?",
            [$codigo, $id_exportadora, $id]
        ),
        SQLSRV_FETCH_ASSOC
    );
    if ($checkRow && $checkRow['total'] > 0) {
        echo "Error: Ya existe una etiqueta con ese código para la exportadora seleccionada";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "UPDATE inst_etiqueta SET Cod_etiqueta = ?, Nombre_etiqueta = ?, id_exportadora = ? WHERE id = ?",
        [$codigo, $nombre, $id_exportadora ?: null, $id]
    );

    if ($stmt) {
        echo "Etiqueta modificada correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al modificar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function eliminar($conn) {
    $id = $_POST['id_etiqueta'] ?? null;

    if (empty($id)) {
        echo "Error: ID no válido";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "DELETE FROM inst_etiqueta WHERE id = ?",
        [$id]
    );

    if ($stmt) {
        echo "Etiqueta eliminada correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al eliminar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}
?>
