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
    $codigo = $_POST['codigo_embalaje'] ?? '';
    $nombre = $_POST['nombre_embalaje'] ?? '';
    $peso = $_POST['peso_embalaje'] ?? null;
    $id_etiqueta = $_POST['etiqueta'] ?? null;
    $id_especie = $_POST['especie'] ?? null;
    $id_exportadora = $_POST['exportadora'] ?? null;
    
    if (empty($codigo) || empty($nombre)) {
        echo "Error: CÃ³digo y descripciÃ³n son obligatorios";
        return;
    }
    
    $checkSql = "SELECT COUNT(*) as total FROM inst_embalaje WHERE codigo_embalaje = ?";
    $checkStmt = sqlsrv_prepare($conn, $checkSql);
    sqlsrv_execute($checkStmt, [$codigo]);
    $checkRow = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
    
    if ($checkRow['total'] > 0) {
        echo "Error: Ya existe un embalaje con ese cÃ³digo";
        return;
    }
    
    $sql = "INSERT INTO inst_embalaje (codigo_embalaje, nombre_embalaje, peso_embalaje, id_etiqueta, id_especie, id_exportadora) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = sqlsrv_prepare($conn, $sql);
    
    if (sqlsrv_execute($stmt, [$codigo, $nombre, $peso, $id_etiqueta, $id_especie, $id_exportadora])) {
        echo "Embalaje guardado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al guardar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function modificar($conn) {
    $id = $_POST['id_embalaje'] ?? null;
    $codigo = $_POST['codigo_embalaje'] ?? '';
    $nombre = $_POST['nombre_embalaje'] ?? '';
    $peso = $_POST['peso_embalaje'] ?? null;
    $id_etiqueta = $_POST['etiqueta'] ?? null;
    $id_especie = $_POST['especie'] ?? null;
    $id_exportadora = $_POST['exportadora'] ?? null;
    
    if (empty($id) || empty($codigo) || empty($nombre)) {
        echo "Error: Datos incompletos";
        return;
    }
    
    $sql = "UPDATE inst_embalaje SET codigo_embalaje = ?, nombre_embalaje = ?, peso_embalaje = ?, id_etiqueta = ?, id_especie = ?, id_exportadora = ? WHERE id_embalaje = ?";
    $stmt = sqlsrv_prepare($conn, $sql);
    
    if (sqlsrv_execute($stmt, [$codigo, $nombre, $peso, $id_etiqueta, $id_especie, $id_exportadora, $id])) {
        echo "Embalaje modificado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al modificar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function eliminar($conn) {
    $id = $_POST['id_embalaje'] ?? null;
    
    if (empty($id)) {
        echo "Error: ID no vÃ¡lido";
        return;
    }
    
    $sql = "DELETE FROM inst_embalaje WHERE id_embalaje = ?";
    $stmt = sqlsrv_prepare($conn, $sql);
    
    if (sqlsrv_execute($stmt, [$id])) {
        echo "Embalaje eliminado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al eliminar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}
?>

