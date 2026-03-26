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
    $codigo = $_POST['codigo_categoria'] ?? '';
    $nombre = $_POST['nombre_categoria'] ?? '';
    $id_especie = $_POST['especie_categoria'] ?? null;
    $id_exportadora = $_POST['exportadora'] ?? null;
    
    if (empty($codigo) || empty($nombre)) {
        echo "Error: Código y nombre son obligatorios";
        return;
    }
    
    $checkSql = "SELECT COUNT(*) as total FROM inst_categoria WHERE cod_categoria = '$codigo'";
    $checkResult = sqlsrv_query($conn, $checkSql);
    $checkRow = sqlsrv_fetch_array($checkResult, SQLSRV_FETCH_ASSOC);
    
    if ($checkRow['total'] > 0) {
        echo "Error: Ya existe una categoría con ese código";
        return;
    }
    
    $sql = "INSERT INTO inst_categoria (cod_categoria, nombre_categoria, id_especie, id_exportadora) VALUES ('$codigo', '$nombre', " . ($id_especie ?: 'NULL') . ", " . ($id_exportadora ?: 'NULL') . ")";
    
    if (sqlsrv_query($conn, $sql)) {
        echo "Categoría guardada correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al guardar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function modificar($conn) {
    $id = $_POST['id_categoria'] ?? null;
    $codigo = $_POST['codigo_categoria'] ?? '';
    $nombre = $_POST['nombre_categoria'] ?? '';
    $id_especie = $_POST['especie_categoria'] ?? null;
    $id_exportadora = $_POST['exportadora'] ?? null;
    
    if (empty($id) || empty($codigo) || empty($nombre)) {
        echo "Error: Datos incompletos";
        return;
    }
    
    $sql = "UPDATE inst_categoria SET cod_categoria = '$codigo', nombre_categoria = '$nombre', id_especie = " . ($id_especie ?: 'NULL') . ", id_exportadora = " . ($id_exportadora ?: 'NULL') . " WHERE id_categoria = $id";
    
    if (sqlsrv_query($conn, $sql)) {
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
    
    $sql = "DELETE FROM inst_categoria WHERE id_categoria = $id";
    
    if (sqlsrv_query($conn, $sql)) {
        echo "Categoría eliminada correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al eliminar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}
?>
