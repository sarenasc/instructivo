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
    $codigo = $_POST['cod_exportadora'] ?? '';
    $nombre = $_POST['nombre_exportadora'] ?? '';
    
    if (empty($codigo) || empty($nombre)) {
        echo "Error: CÃ³digo y nombre son obligatorios";
        return;
    }
    
    $checkSql = "SELECT COUNT(*) as total FROM inst_exportadora WHERE cod_exportadora = '$codigo'";
    $checkResult = sqlsrv_query($conn, $checkSql);
    $checkRow = sqlsrv_fetch_array($checkResult, SQLSRV_FETCH_ASSOC);
    
    if ($checkRow['total'] > 0) {
        echo "Error: Ya existe una exportadora con ese cÃ³digo";
        return;
    }
    
    $sql = "INSERT INTO inst_exportadora (cod_exportadora, nombre_exportadora) VALUES ('$codigo', '$nombre')";
    
    if (sqlsrv_query($conn, $sql)) {
        echo "Exportadora guardada correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al guardar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function modificar($conn) {
    $id = $_POST['id_exportadora'] ?? null;
    $codigo = $_POST['cod_exportadora'] ?? '';
    $nombre = $_POST['nombre_exportadora'] ?? '';
    
    if (empty($id) || empty($codigo) || empty($nombre)) {
        echo "Error: Datos incompletos";
        return;
    }
    
    $sql = "UPDATE inst_exportadora SET cod_exportadora = '$codigo', nombre_exportadora = '$nombre' WHERE id_exportadora = $id";
    
    if (sqlsrv_query($conn, $sql)) {
        echo "Exportadora modificada correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al modificar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function eliminar($conn) {
    $id = $_POST['id_exportadora'] ?? null;
    
    if (empty($id)) {
        echo "Error: ID no vÃ¡lido";
        return;
    }
    
    $sql = "DELETE FROM inst_exportadora WHERE id_exportadora = $id";
    
    if (sqlsrv_query($conn, $sql)) {
        echo "Exportadora eliminada correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al eliminar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}
?>

