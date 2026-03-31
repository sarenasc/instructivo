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
    $codigo         = $_POST['codigo_categoria']  ?? '';
    $nombre         = $_POST['nombre_categoria']  ?? '';
    $id_especie     = $_POST['especie_categoria']  ?? null;
    $id_exportadora = $_POST['exportadora']        ?? null;

    if (empty($codigo) || empty($nombre)) {
        echo "Error: Código y nombre son obligatorios";
        return;
    }

    $checkRow = sqlsrv_fetch_array(
        sqlsrv_query($conn,
            "SELECT COUNT(*) AS total FROM inst_categoria WHERE cod_categoria = ? AND id_especie = ? AND id_exportadora = ?",
            [$codigo, $id_especie, $id_exportadora]
        ),
        SQLSRV_FETCH_ASSOC
    );
    if ($checkRow && $checkRow['total'] > 0) {
        echo "Error: Ya existe una categoría con ese código para la especie y exportadora seleccionadas";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "INSERT INTO inst_categoria (cod_categoria, nombre_categoria, id_especie, id_exportadora) VALUES (?, ?, ?, ?)",
        [$codigo, $nombre, $id_especie ?: null, $id_exportadora ?: null]
    );

    if ($stmt) {
        echo "Categoría guardada correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al guardar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function modificar($conn) {
    $id             = $_POST['id_categoria']      ?? null;
    $codigo         = $_POST['codigo_categoria']  ?? '';
    $nombre         = $_POST['nombre_categoria']  ?? '';
    $id_especie     = $_POST['especie_categoria']  ?? null;
    $id_exportadora = $_POST['exportadora']        ?? null;

    if (empty($id) || empty($codigo) || empty($nombre)) {
        echo "Error: Datos incompletos";
        return;
    }

    $checkRow = sqlsrv_fetch_array(
        sqlsrv_query($conn,
            "SELECT COUNT(*) AS total FROM inst_categoria WHERE cod_categoria = ? AND id_especie = ? AND id_exportadora = ? AND id_categoria <> ?",
            [$codigo, $id_especie, $id_exportadora, $id]
        ),
        SQLSRV_FETCH_ASSOC
    );
    if ($checkRow && $checkRow['total'] > 0) {
        echo "Error: Ya existe una categoría con ese código para la especie y exportadora seleccionadas";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "UPDATE inst_categoria SET cod_categoria = ?, nombre_categoria = ?, id_especie = ?, id_exportadora = ? WHERE id_categoria = ?",
        [$codigo, $nombre, $id_especie ?: null, $id_exportadora ?: null, $id]
    );

    if ($stmt) {
        echo "Categoría modificada correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al modificar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function eliminar($conn) {
    $id = $_POST['id_categoria'] ?? null;

    if (empty($id)) {
        echo "Error: ID no válido";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "DELETE FROM inst_categoria WHERE id_categoria = ?",
        [$id]
    );

    if ($stmt) {
        echo "Categoría eliminada correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al eliminar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}
?>
