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
    $codigo = $_POST['codigo_destino'] ?? '';
    $nombre = $_POST['nombre_destino'] ?? '';

    if (empty($codigo) || empty($nombre)) {
        echo "Error: Código y nombre son obligatorios";
        return;
    }

    $checkRow = sqlsrv_fetch_array(
        sqlsrv_query($conn, "SELECT COUNT(*) AS total FROM inst_destino WHERE cod_destino = ?", [$codigo]),
        SQLSRV_FETCH_ASSOC
    );
    if ($checkRow && $checkRow['total'] > 0) {
        echo "Error: Ya existe un destino con ese código";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "INSERT INTO inst_destino (cod_destino, nombre_destino) VALUES (?, ?)",
        [$codigo, $nombre]
    );

    if ($stmt) {
        echo "Destino guardado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al guardar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function modificar($conn) {
    $id     = $_POST['id_destino']      ?? null;
    $codigo = $_POST['codigo_destino']  ?? '';
    $nombre = $_POST['nombre_destino']  ?? '';

    if (empty($id) || empty($codigo) || empty($nombre)) {
        echo "Error: Datos incompletos";
        return;
    }

    $checkRow = sqlsrv_fetch_array(
        sqlsrv_query($conn, "SELECT COUNT(*) AS total FROM inst_destino WHERE cod_destino = ? AND id <> ?", [$codigo, $id]),
        SQLSRV_FETCH_ASSOC
    );
    if ($checkRow && $checkRow['total'] > 0) {
        echo "Error: Ya existe un destino con ese código";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "UPDATE inst_destino SET cod_destino = ?, nombre_destino = ? WHERE id = ?",
        [$codigo, $nombre, $id]
    );

    if ($stmt) {
        echo "Destino modificado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al modificar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function eliminar($conn) {
    $id = $_POST['id_destino'] ?? null;

    if (empty($id)) {
        echo "Error: ID no válido";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "DELETE FROM inst_destino WHERE id_destino = ?",
        [$id]
    );

    if ($stmt) {
        echo "Destino eliminado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al eliminar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}
?>
