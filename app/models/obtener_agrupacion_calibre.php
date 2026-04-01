<?php
require_once("../conexion.php");
header('Content-Type: application/json');

$sql = "SELECT ag.id,
               ag.id_especie,   e.especie          AS nombre_especie,
               ag.id_exportadora, ex.Nombre_Exportadora AS nombre_exportadora,
               ag.id_categoria, cat.nombre_categoria,
               ag.nombre_grupo,
               STUFF((
                   SELECT ', ' + c2.nombre_calibre
                   FROM inst_agrupacion_calibre_detalle agd2
                   JOIN inst_calibre c2 ON c2.id = agd2.id_calibre
                   WHERE agd2.id_agrupacion = ag.id
                   ORDER BY c2.orden
                   FOR XML PATH(''), TYPE
               ).value('.','NVARCHAR(MAX)'), 1, 2, '') AS calibres_lista
        FROM inst_agrupacion_calibre ag
        JOIN especie           e   ON e.id_especie    = ag.id_especie
        JOIN inst_exportadora  ex  ON ex.id            = ag.id_exportadora
        JOIN inst_categoria    cat ON cat.id            = ag.id_categoria
        ORDER BY e.especie, ex.Nombre_Exportadora, cat.nombre_categoria, ag.nombre_grupo";

$stmt = sqlsrv_query($conn, $sql);

$resultado = [];
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $resultado[] = $row;
    }
}

echo json_encode($resultado);
