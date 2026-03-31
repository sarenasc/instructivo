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
    $codigo     = $_POST['codigo_calibre'] ?? '';
    $nombre     = $_POST['nombre_calibre'] ?? '';
    $orden      = $_POST['orden']          ?? null;
    $id_especie = $_POST['especie']        ?? null;

    if (empty($codigo) || empty($nombre)) {
        echo "Error: Código y nombre son obligatorios";
        return;
    }

    $checkRow = sqlsrv_fetch_array(
        sqlsrv_query($conn,
            "SELECT COUNT(*) AS total FROM inst_calibre WHERE cod_calibre = ? AND id_especie = ?",
            [$codigo, $id_especie]
        ),
        SQLSRV_FETCH_ASSOC
    );
    if ($checkRow && $checkRow['total'] > 0) {
        echo "Error: Ya existe un calibre con ese código para la especie seleccionada";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "INSERT INTO inst_calibre (cod_calibre, nombre_calibre, orden, id_especie) VALUES (?, ?, ?, ?)",
        [$codigo, $nombre, $orden ?: null, $id_especie ?: null]
    );

    if ($stmt) {
        echo "Calibre guardado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al guardar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function modificar($conn) {
    $id         = $_POST['id_calibre']     ?? null;
    $codigo     = $_POST['codigo_calibre'] ?? '';
    $nombre     = $_POST['nombre_calibre'] ?? '';
    $orden      = $_POST['orden']          ?? null;
    $id_especie = $_POST['especie']        ?? null;

    if (empty($id) || empty($codigo) || empty($nombre)) {
        echo "Error: Datos incompletos";
        return;
    }

    $checkRow = sqlsrv_fetch_array(
        sqlsrv_query($conn,
            "SELECT COUNT(*) AS total FROM inst_calibre WHERE cod_calibre = ? AND id_especie = ? AND id <> ?",
            [$codigo, $id_especie, $id]
        ),
        SQLSRV_FETCH_ASSOC
    );
    if ($checkRow && $checkRow['total'] > 0) {
        echo "Error: Ya existe un calibre con ese código para la especie seleccionada";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "UPDATE inst_calibre SET cod_calibre = ?, nombre_calibre = ?, orden = ?, id_especie = ? WHERE id = ?",
        [$codigo, $nombre, $orden ?: null, $id_especie ?: null, $id]
    );

    if ($stmt) {
        echo "Calibre modificado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al modificar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function eliminar($conn) {
    $id = $_POST['id_calibre'] ?? null;

    if (empty($id)) {
        echo "Error: ID no válido";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "DELETE FROM inst_calibre WHERE id = ?",
        [$id]
    );

    if ($stmt) {
        echo "Calibre eliminado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al eliminar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}
?>
