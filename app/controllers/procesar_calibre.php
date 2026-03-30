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
    $codigo = $_POST['cod_calibre'] ?? '';
    $nombre = $_POST['nombre_calibre'] ?? '';
    $id_especie = $_POST['especie'] ?? null;
    
    if (empty($codigo) || empty($nombre)) {
        echo "Error: CÃ³digo y nombre son obligatorios";
        return;
    }
    
    $checkSql = "SELECT COUNT(*) as total FROM inst_calibre WHERE cod_calibre = '$codigo'";
    $checkResult = sqlsrv_query($conn, $checkSql);
    $checkRow = sqlsrv_fetch_array($checkResult, SQLSRV_FETCH_ASSOC);
    
    if ($checkRow['total'] > 0) {
        echo "Error: Ya existe un calibre con ese cÃ³digo";
        return;
    }
    
    $sql = "INSERT INTO inst_calibre (cod_calibre, nombre_calibre, id_especie) VALUES ('$codigo', '$nombre', " . ($id_especie ?: 'NULL') . ")";
    
    if (sqlsrv_query($conn, $sql)) {
        echo "Calibre guardado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al guardar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function modificar($conn) {
    $id = $_POST['id_calibre'] ?? null;
    $codigo = $_POST['cod_calibre'] ?? '';
    $nombre = $_POST['nombre_calibre'] ?? '';
    $id_especie = $_POST['especie'] ?? null;
    
    if (empty($id) || empty($codigo) || empty($nombre)) {
        echo "Error: Datos incompletos";
        return;
    }
    
    $sql = "UPDATE calibre SET cod_calibre = '$codigo', nombre_calibre = '$nombre', id_especie = " . ($id_especie ?: 'NULL') . " WHERE id_calibre = $id";
    
    if (sqlsrv_query($conn, $sql)) {
        echo "Calibre modificado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al modificar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function eliminar($conn) {
    $id = $_POST['id_calibre'] ?? null;
    
    if (empty($id)) {
        echo "Error: ID no vÃ¡lido";
        return;
    }
    
    $sql = "DELETE FROM inst_calibre WHERE id_calibre = $id";
    
    if (sqlsrv_query($conn, $sql)) {
        echo "Calibre eliminado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al eliminar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}
?>



