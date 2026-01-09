<?php
// Archivo: Procesos/api_instructivo_combobox.php

require_once "conexion.php";

$tabla = $_GET['tabla'] ?? '';
$id_exportadora =$_GET['id_exportadora'] ?? '';
$id_especie =$_GET['especie'] ?? '';

if (!$tabla || !preg_match('/^[a-z_]+$/i', $tabla)) {
    echo json_encode([]);
    exit;
}

$Nombre_tabla = "inst_" . $tabla;



$sql = "SELECT * FROM $Nombre_tabla";
$params=[];

$tablas_con_exportadora = [ 'etiqueta','pallet'];
$tabla_con_exportadora_especie =['embalaje','categoria',];
$tabla_con_especie =['plu','calibre'];


if ($id_exportadora && in_array($tabla, $tablas_con_exportadora)) {
        $sql .= " where id_exportadora = ? ";
        $params[] = $id_exportadora;
}

if ($id_especie && in_array($tabla, $tabla_con_exportadora_especie)) {
        $sql .= " where id_exportadora = ? and id_especie = ?";
        $params[] = $id_exportadora;
        $params[] = $id_especie;
        
}
if (!$id_exportadora && $id_especie  && in_array($tabla, $tabla_con_especie)) {
        $sql .= " where  id_especie = ?";       
        $params[] = $id_especie;
        
}

$sql .= " ORDER BY 1";
$stmt = sqlsrv_query($conn,$sql,$params);

$options = [];
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $id = array_values($row)[0];
        $text = implode(" - ", array_slice($row, 1));
        $options[] = ["id" => $id, "text" => $text];
    }
}

header('Content-Type: application/json');
echo json_encode($options);
