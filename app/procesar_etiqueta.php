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
    $codigo = $_POST['codigo_etiqueta'] ?? '';
    $nombre = $_POST['nombre_etiqueta'] ?? '';
    $id_exportadora = $_POST['exportadora'] ?? null;
    
    if (empty($codigo) || empty($nombre)) {
        echo "Error: Código y nombre son obligatorios";
        return;
    }
    
    $checkSql = "SELECT COUNT(*) as total FROM etiqueta WHERE codigo_etiqueta = '$codigo'";
    $checkResult = sqlsrv_query($conn, $checkSql);
    $checkRow = sqlsrv_fetch_array($checkResult, SQLSRV_FETCH_ASSOC);
    
    if ($checkRow['total'] > 0) {
        echo "Error: Ya existe una etiqueta con ese código";
        return;
    }
    
    $sql = "INSERT INTO etiqueta (codigo_etiqueta, nombre_etiqueta, id_exportadora) VALUES ('$codigo', '$nombre', " . ($id_exportadora ?: 'NULL') . ")";
    
    if (sqlsrv_query($conn, $sql)) {
        echo "Etiqueta guardada correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al guardar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function modificar($conn) {
    $id = $_POST['id_etiqueta'] ?? null;
    $codigo = $_POST['codigo_etiqueta'] ?? '';
    $nombre = $_POST['nombre_etiqueta'] ?? '';
    $id_exportadora = $_POST['exportadora'] ?? null;
    
    if (empty($id) || empty($codigo) || empty($nombre)) {
        echo "Error: Datos incompletos";
        return;
    }
    
    $sql = "UPDATE etiqueta SET codigo_etiqueta = '$codigo', nombre_etiqueta = '$nombre', id_exportadora = " . ($id_exportadora ?: 'NULL') . " WHERE id_etiqueta = $id";
    
    if (sqlsrv_query($conn, $sql)) {
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
    
    $sql = "DELETE FROM etiqueta WHERE id_etiqueta = $id";
    
    if (sqlsrv_query($conn, $sql)) {
        echo "Etiqueta eliminada correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al eliminar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}
?>
