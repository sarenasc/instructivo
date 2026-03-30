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
            echo "AcciÃ³n no vÃ¡lida";
    }
}

function guardar($conn) {
    $codigo        = $_POST['codigo_embalaje'] ?? '';
    $nombre        = $_POST['nombre_embalaje'] ?? '';
    $peso          = $_POST['peso_embalaje']   ?? null;
    $id_etiqueta   = $_POST['etiqueta']        ?? null;
    $id_especie    = $_POST['especie']          ?? null;
    $id_exportadora= $_POST['exportadora']     ?? null;

    if (empty($codigo) || empty($nombre)) {
        echo "Error: Código y descripción son obligatorios";
        return;
    }

    $checkRow = sqlsrv_fetch_array(
        sqlsrv_query($conn, "SELECT COUNT(*) AS total FROM inst_embalaje WHERE Codigo_emb = ?", [$codigo]),
        SQLSRV_FETCH_ASSOC
    );
    if ($checkRow && $checkRow['total'] > 0) {
        echo "Error: Ya existe un embalaje con ese código";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "INSERT INTO inst_embalaje (Codigo_emb, Descripcion_Embalaje, Peso_Embalaje, id_etiqueta, id_especie, id_exportadora)
         VALUES (?, ?, ?, ?, ?, ?)",
        [$codigo, $nombre, $peso, $id_etiqueta, $id_especie, $id_exportadora]
    );

    if ($stmt) {
        echo "Embalaje guardado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al guardar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function modificar($conn) {
    $id            = $_POST['id_embalaje']     ?? null;
    $codigo        = $_POST['codigo_embalaje'] ?? '';
    $nombre        = $_POST['nombre_embalaje'] ?? '';
    $peso          = $_POST['peso_embalaje']   ?? null;
    $id_etiqueta   = $_POST['etiqueta']        ?? null;
    $id_especie    = $_POST['especie']          ?? null;
    $id_exportadora= $_POST['exportadora']     ?? null;

    if (empty($id) || empty($codigo) || empty($nombre)) {
        echo "Error: Datos incompletos";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "UPDATE inst_embalaje
         SET Codigo_emb = ?, Descripcion_Embalaje = ?, Peso_Embalaje = ?,
             id_etiqueta = ?, id_especie = ?, id_exportadora = ?
         WHERE id = ?",
        [$codigo, $nombre, $peso, $id_etiqueta, $id_especie, $id_exportadora, $id]
    );

    if ($stmt) {
        echo "Embalaje modificado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al modificar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function eliminar($conn) {
    $id = $_POST['id_embalaje'] ?? null;

    if (empty($id)) {
        echo "Error: ID no válido";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "DELETE FROM inst_embalaje WHERE id = ?",
        [$id]
    );

    if ($stmt) {
        echo "Embalaje eliminado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al eliminar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}
?>

