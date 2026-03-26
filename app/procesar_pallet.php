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
    $codigo = $_POST['cod_pallet'] ?? '';
    $descripcion = $_POST['descrip_pallet'] ?? '';
    $id_exportadora = $_POST['id_exportadora'] ?? null;
    
    if (empty($codigo) || empty($descripcion)) {
        echo "Error: Código y descripción son obligatorios";
        return;
    }
    
    $checkSql = "SELECT COUNT(*) as total FROM pallet WHERE cod_pallet = '$codigo'";
    $checkResult = sqlsrv_query($conn, $checkSql);
    $checkRow = sqlsrv_fetch_array($checkResult, SQLSRV_FETCH_ASSOC);
    
    if ($checkRow['total'] > 0) {
        echo "Error: Ya existe un pallet con ese código";
        return;
    }
    
    $sql = "INSERT INTO pallet (cod_pallet, descrip_pallet, id_exportadora) VALUES ('$codigo', '$descripcion', " . ($id_exportadora ?: 'NULL') . ")";
    
    if (sqlsrv_query($conn, $sql)) {
        echo "Pallet guardado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al guardar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function modificar($conn) {
    $id = $_POST['id_pallet'] ?? null;
    $codigo = $_POST['cod_pallet'] ?? '';
    $descripcion = $_POST['descrip_pallet'] ?? '';
    $id_exportadora = $_POST['id_exportadora'] ?? null;
    
    if (empty($id) || empty($codigo) || empty($descripcion)) {
        echo "Error: Datos incompletos";
        return;
    }
    
    $sql = "UPDATE pallet SET cod_pallet = '$codigo', descrip_pallet = '$descripcion', id_exportadora = " . ($id_exportadora ?: 'NULL') . " WHERE id_pallet = $id";
    
    if (sqlsrv_query($conn, $sql)) {
        echo "Pallet modificado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al modificar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function eliminar($conn) {
    $id = $_POST['id_pallet'] ?? null;
    
    if (empty($id)) {
        echo "Error: ID no válido";
        return;
    }
    
    $sql = "DELETE FROM pallet WHERE id_pallet = $id";
    
    if (sqlsrv_query($conn, $sql)) {
        echo "Pallet eliminado correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al eliminar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}
?>
