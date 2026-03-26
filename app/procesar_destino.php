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
    
    $checkSql = "SELECT COUNT(*) as total FROM destino WHERE codigo_destino = '$codigo'";
    $checkResult = sqlsrv_query($conn, $checkSql);
    $checkRow = sqlsrv_fetch_array($checkResult, SQLSRV_FETCH_ASSOC);
    
    if ($checkRow['total'] > 0) {
        echo "Error: Ya existe un destino con ese código";
        return;
    }
    
    $sql = "INSERT INTO destino (codigo_destino, nombre_destino) VALUES ('$codigo', '$nombre')";
    
    if (sqlsrv_query($conn, $sql)) {
        echo "Destino guardado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al guardar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function modificar($conn) {
    $id = $_POST['id_destino'] ?? null;
    $codigo = $_POST['codigo_destino'] ?? '';
    $nombre = $_POST['nombre_destino'] ?? '';
    
    if (empty($id) || empty($codigo) || empty($nombre)) {
        echo "Error: Datos incompletos";
        return;
    }
    
    $sql = "UPDATE destino SET codigo_destino = '$codigo', nombre_destino = '$nombre' WHERE id_destino = $id";
    
    if (sqlsrv_query($conn, $sql)) {
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
    
    $sql = "DELETE FROM destino WHERE id_destino = $id";
    
    if (sqlsrv_query($conn, $sql)) {
        echo "Destino eliminado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al eliminar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}
?>
