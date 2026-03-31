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
    $id_embalaje = $_POST['id_embalaje'] ?? null;
    $altura      = $_POST['altura']      ?? null;
    $cajas       = $_POST['cajas']       ?? null;

    if (empty($id_embalaje) || empty($altura) || empty($cajas)) {
        echo "Error: Todos los campos son obligatorios";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "INSERT INTO inst_altura_pallet (id_embalaje, altura, cajas) VALUES (?, ?, ?)",
        [$id_embalaje, $altura, $cajas]
    );

    if ($stmt) {
        echo "Altura de pallet guardada correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al guardar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function modificar($conn) {
    $id          = $_POST['id_altura_pallet'] ?? null;
    $id_embalaje = $_POST['id_embalaje']      ?? null;
    $altura      = $_POST['altura']           ?? null;
    $cajas       = $_POST['cajas']            ?? null;

    if (empty($id) || empty($id_embalaje) || empty($altura) || empty($cajas)) {
        echo "Error: Datos incompletos";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "UPDATE inst_altura_pallet SET id_embalaje = ?, altura = ?, cajas = ? WHERE id_altura_pallet = ?",
        [$id_embalaje, $altura, $cajas, $id]
    );

    if ($stmt) {
        echo "Altura de pallet modificada correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al modificar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}

function eliminar($conn) {
    $id = $_POST['id_altura_pallet'] ?? null;

    if (empty($id)) {
        echo "Error: ID no válido";
        return;
    }

    $stmt = sqlsrv_query($conn,
        "DELETE FROM inst_altura_pallet WHERE id_altura_pallet = ?",
        [$id]
    );

    if ($stmt) {
        echo "Altura de pallet eliminada correctamente";
    } else {
        $errores = sqlsrv_errors();
        echo "Error al eliminar: " . ($errores ? $errores[0]['message'] : 'Desconocido');
    }
}
?>
