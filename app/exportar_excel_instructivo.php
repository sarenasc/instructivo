<?php
// php/exportar_excel_instructivo.php
ob_start();
require '../vendor/autoload.php';
include 'conexion.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


$id_instructivo = $_GET['id_instructivo'] ?? null;
$version = $_GET['version'] ?? null;

if (!$id_instructivo || !$version) {
    die('Faltan parámetros de ID de instructivo o versión.');
}

// --- Obtener cabecera ---
$sqlCab = "SELECT TOP 1 cab.id_instructivo, cab.fecha, exp.nombre_exportadora, esp.especie as nombre_especie, det.var_etiquetada as variedad, det.version,cab.observacion, cab.turno
FROM inst_cab_instructivo cab
JOIN inst_exportadora exp ON exp.id = cab.id_exportadora 
JOIN especie esp ON esp.id_especie = cab.id_especie
JOIN inst_detalle_instructivo det on det.id_cab_instructivo = cab.id_instructivo
WHERE cab.id_instructivo = ? AND det.version = ? 
ORDER BY cab.fecha DESC"; 

$stmtCab = sqlsrv_query($conn, $sqlCab, [$id_instructivo, $version]);
if ($stmtCab === false) {
    die(print_r(sqlsrv_errors(), true));
}
$cabecera = sqlsrv_fetch_array($stmtCab, SQLSRV_FETCH_ASSOC);
if (!$cabecera) {
    die('No se encontraron datos para el instructivo o versión especificados.');
}
// --- Obtener detalle ---
$sqlDet = "SELECT
    i.numero_pedido,
    i.var_etiquetada,
    e.codigo_emb AS embalaje,
    e.Descripcion_Embalaje, 
    e.Peso_Embalaje,
    et.Nombre_etiqueta,
    d.nombre_destino,
    pl.plu,
    ca.nombre_categoria AS categoria,
    p.Descrip_pallet,
    i.altura_pallet,
    CONCAT(ap.altura,'/',ap.cajas) as Altura,
    i.observacion,
    c.nombre_calibre,
    c.orden as Orden_Calibre,
    i.cantidad_pedido
FROM inst_detalle_instructivo i
LEFT JOIN inst_embalaje e ON e.id = i.id_embalaje
LEFT JOIN inst_calibre c ON c.id = i.id_calibre
LEFT JOIN inst_categoria ca ON ca.id = i.id_categoria
LEFT JOIN inst_plu pl ON pl.id = i.id_plu
LEFT JOIN inst_destino d ON d.id = i.id_destino
LEFT JOIN inst_pallet p ON p.id = i.id_pallet
LEFT JOIN inst_etiqueta et ON et.id = i.id_etiqueta
LEFT JOIN inst_altura_pallet ap ON i.altura_pallet = ap.id
WHERE i.id_cab_instructivo = ? AND i.version = ?
ORDER BY Orden_Calibre ASC";

$stmtDet = sqlsrv_query($conn, $sqlDet, [$id_instructivo, $version]);
if ($stmtDet === false) {
    die(print_r(sqlsrv_errors(), true));
}

$agrupadoPorPedido = [];
while ($row = sqlsrv_fetch_array($stmtDet, SQLSRV_FETCH_ASSOC)) {
    $pedido = $row['numero_pedido'];
    if (!isset($agrupadoPorPedido[$pedido])) {
        $agrupadoPorPedido[$pedido] = [];
    }
    $agrupadoPorPedido[$pedido][] = $row;
}

$datos = [];
$calibres = [];
foreach ($agrupadoPorPedido as $pedido => $filas) {
    foreach ($filas as $row) {
        $clave = implode('|', [
            $row['numero_pedido'],
            $row['embalaje'],
            $row['Descripcion_Embalaje'],
            $row['Peso_Embalaje'],
            $row['Nombre_etiqueta'],
            $row['nombre_destino'],
            $row['plu'],
            $row['categoria'],
            $row['Descrip_pallet'],
            $row['var_etiquetada'],
            $row['altura_pallet'],
            $row['Altura'],
            $row['observacion']
        ]);

        if (!isset($datos[$clave])) {
            $datos[$clave] = [
                'pedido' => $row['numero_pedido'],
                'embalaje_cod' => $row['embalaje'],
                'embalaje_des' => $row['Descripcion_Embalaje'],
                'peso_embalaje' => $row['Peso_Embalaje'],
                'etiqueta' => $row['Nombre_etiqueta'],
                'destino' => $row['nombre_destino'],
                'plu' => $row['plu'],
                'categoria' => $row['categoria'],
                'pallet' => $row['Descrip_pallet'],
                'var_etiquetada' => $row['var_etiquetada'],
                'altura_pallet' => $row['altura_pallet'],
                'altura' => $row['Altura'],
                'observacion' => $row['observacion'],
                'calibres' => []
            ];
        }

        $datos[$clave]['calibres'][$row['nombre_calibre']] = $row['cantidad_pedido'];
                if (!isset($calibres[$row['nombre_calibre']])) {
                     $calibres[$row['nombre_calibre']] = $row['Orden_Calibre'];
                }

    }
}

$pedidoCantidades = [];
$prioridades = [];

$sqlCantidades = "SELECT numero_pedido, cantidad, prioridad FROM inst_pedidos WHERE id_instructivo = ? AND version = ? ORDER BY prioridad ASC";
$stmtCant = sqlsrv_query($conn, $sqlCantidades, [$id_instructivo, $version]);
if ($stmtCant !== false) {
    while ($row = sqlsrv_fetch_array($stmtCant, SQLSRV_FETCH_ASSOC)) {
        $pedidoCantidades[$row['numero_pedido']] = $row['cantidad'];
        $prioridades[$row['numero_pedido']] = $row['prioridad'];
    }
}


// Ordena por Orden_Calibre
asort($calibres); // ordena manteniendo claves (nombre_calibre => orden)

// Extrae solo los nombres de calibre en el orden correcto
$calibres = array_keys($calibres);


// --- Crear Excel ---
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("I_$id_instructivo V_$version");
$sheet->getPageSetup()->setFitToPage(true);
$sheet->getPageSetup()->setFitToWidth(1);
$sheet->getPageSetup()->setFitToHeight(1);
$sheet->setShowGridlines(false);
$sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
$sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LETTER);

// Estilos
$styleTitulo = [
    'font' => [
        'bold' => true,
        'size' => 48,
        'name' => 'Arial',
    ],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
];
$styleHeader = [
    'font' => [
        'bold' => true,
        'size' => 36,
    ],
    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
               'startColor' => ['argb' => 'FFD9D9D9']],
    'alignment' => ['horizontal' => 'center'],
    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
];
$styleYellowFill = [
    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
               'startColor' => ['argb' => 'FFFFFF00']],
    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
    'font' => [
        'size'=> 36
    ],
];
$styleYellowFillObs = [
    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
               'startColor' => ['argb' => 'FFFFFF00']],
    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
    'font' => [
        'size'=> 45
    ],
];
$styleTableCell = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN
        ]
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
        'wrapText'   => true
    ],
    'font' => [
        'size' => 48,
        'bold' => true
    ]
];
$EstiloLineaA = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb'=> Color::COLOR_BLACK],
        ],
    ],
    'alignment'=>[
        'horizontal' => 'left',
        'vertical' => 'center'
    ],
    'font' => [
        'size'=> 36,
        'bold' => true
    ]
];

$EstiloLineaB = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb'=> Color::COLOR_BLACK],
        ],
    ],
    'alignment'=>[
        'horizontal' => 'center',
        'vertical' => 'center'
    ],
    'font' => [
        'size'=> 36,
    ]
];

// --- Cabecera Visual: Fila 1 ---
$sheet->mergeCells('A1:P1')->setCellValue('A1', 'INSTRUCTIVO PLANTA ALMAHUE ');
$sheet->setCellValue('A3', 'Exportadora: ');
$sheet->setCellValue('B3', $cabecera['nombre_exportadora']);
$sheet->setCellValue('A4', 'Fecha: ');
$sheet->setCellValue('B4', (new DateTime($cabecera['fecha']))->format('d-m-Y'));
$sheet->setCellValue('A5', 'Especie: ');
$sheet->setCellValue('B5', $cabecera['nombre_especie']);
$sheet->setCellValue('A6', 'Variedad Real: ' );
$sheet->setCellValue('B6',$cabecera['variedad']);
$sheet->setCellValue('A7', 'Turno: ');
$sheet->setCellValue('B7', $cabecera['turno']);
$sheet->setCellValue('A8', 'Numero Instructivo: ');
$sheet->setCellValue('B8', $cabecera['id_instructivo']);
$sheet->setCellValue('A9', 'Versión:');
$sheet->setCellValue('B9', $version);
$sheet->mergeCells('A11:P11')->setCellValue('A11', 'INFORMACION DE EMBALAJE ');
$sheet->getColumnDimension('A1')->setAutoSize(true);

// Estilo a encabezado visual
$sheet->getStyle('A1:P1')->applyFromArray($styleTitulo);
$sheet->getStyle('A11:P11')->applyFromArray($styleTitulo);
$sheet->getStyle('A3:A9')->applyFromArray($EstiloLineaA);
$sheet->getStyle('B3:B9')->applyFromArray($EstiloLineaB);
//$sheet->getStyle('E3:L6')->applyFromArray($styleHeader);

// --- Encabezados Tabla ---
$filaEncabezados = 13;
$colIndex = 1;

// Encabezados fijos
$encabezadosFijos = ['Pedido','Var Etiquetada', 'Código Envase', 'Embalaje', 'Etiqueta'];
foreach ($encabezadosFijos as $enc) {
    $cell = Coordinate::stringFromColumnIndex($colIndex) . $filaEncabezados;
    $sheet->setCellValue($cell, $enc);
    $sheet->getStyle($cell)->applyFromArray($styleHeader);
    $colIndex++;
}

// Calibres dinámicos (fondo amarillo)
foreach ($calibres as $cal) {
    $cell = Coordinate::stringFromColumnIndex($colIndex) . $filaEncabezados;
    $sheet->setCellValue($cell, $cal);
    $sheet->getStyle($cell)->applyFromArray($styleYellowFill);
    $colIndex++;
}

// Encabezados adicionales
$extraEncabezados = ['Categoría', 'PLU', 'Destino', 'Observación'];
foreach ($extraEncabezados as $e) {
    $cell = Coordinate::stringFromColumnIndex($colIndex) . $filaEncabezados;
    $sheet->setCellValue($cell, $e);
    $sheet->getStyle($cell)->applyFromArray($styleHeader);
    $colIndex++;
}

// --- Cuerpo Tabla 1 ---

$filaContenido = $filaEncabezados + 1;

// Agrupar por pedido
$agrupadoPorPedido = [];
foreach ($datos as $fila) {
    $agrupadoPorPedido[$fila['pedido']][] = $fila;
}
uksort($agrupadoPorPedido, function($a, $b) use ($prioridades) {
    $prioridadA = $prioridades[$a] ?? null;
    $prioridadB = $prioridades[$b] ?? null;

    if ($prioridadA !== null && $prioridadB !== null) {
        return $prioridadA <=> $prioridadB; // ambos tienen prioridad
    } elseif ($prioridadA !== null) {
        return -1; // A tiene prioridad, B no => A va primero
    } elseif ($prioridadB !== null) {
        return 1; // B tiene prioridad, A no => B va primero
    } else {
        return $a <=> $b; // ninguno tiene prioridad => ordenar por número de pedido
    }
});

// Ordena por Orden_Calibre dentro de cada pedido
foreach ($agrupadoPorPedido as &$filasDelPedido) {
    usort($filasDelPedido, function($a, $b) {
        $oa = $a['Orden_Calibre'] ?? 9999; ;
        $ob = $b['Orden_Calibre'] ?? 9999; ;
        return $oa <=> $ob;
    });
}
unset($filasDelPedido);


foreach ($agrupadoPorPedido as $pedido => $filas) {
    $rowStart = $filaContenido;
    $rowEnd = $filaContenido + count($filas) - 1;

    foreach ($filas as $filaDatos) {
        $col = 1;

        // Solo escribir y hacer merge en la primera fila del grupo
        if ($filaContenido == $rowStart) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . $filaContenido, $pedido);
            if ($rowEnd > $rowStart) {
                $sheet->mergeCells(Coordinate::stringFromColumnIndex($col) . $rowStart . ':' . Coordinate::stringFromColumnIndex($col) . $rowEnd);
            }
        }

        $col++; // var_etiquetada
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido, $filaDatos['var_etiquetada']);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido, $filaDatos['embalaje_cod']); 
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido, $filaDatos['embalaje_des']);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido, $filaDatos['etiqueta']);

        foreach ($calibres as $cal) {
            $valor = $filaDatos['calibres'][$cal] ?? '';
            $cell = Coordinate::stringFromColumnIndex($col++) . $filaContenido;
            $sheet->setCellValue($cell, $valor);

            $sheet->getStyle($cell)->applyFromArray(
                is_numeric($valor) && $valor >= 0 ? $styleYellowFill : $styleTableCell
            );
        }

        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido, $filaDatos['categoria']);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido, $filaDatos['plu']);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido, $filaDatos['destino']);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido, $filaDatos['observacion']);

        $sheet->getStyle("A{$filaContenido}:" . Coordinate::stringFromColumnIndex($col - 1) . "{$filaContenido}")
              ->applyFromArray($styleTableCell);

        $filaContenido++;
    }
}


// segunda tabla de paletizaje
$filaEncabezados = $filaContenido + 6;
$sheet->mergeCells('A'.$filaEncabezados .':N'.($filaEncabezados + 1))->setCellValue('A'.$filaEncabezados, 'INFORMACION DE PALETIZAJE');
$sheet->getStyle('A'.$filaEncabezados .':N'.($filaEncabezados + 1))->applyFromArray($styleTitulo);
$filaEncabezados += 3;
$colIndex = 3;

$encabezadosFijos = ['Pedido','Codigo Envase / Etiqueta','Tipo Pallet'];
foreach ($encabezadosFijos as $enc) {
    $cell = Coordinate::stringFromColumnIndex($colIndex) . $filaEncabezados;
    $sheet->setCellValue($cell, $enc);
    $sheet->getStyle($cell)->applyFromArray($styleHeader);
    $colIndex++;
}

// Calibres dinámicos (fondo amarillo)
foreach ($calibres as $cal) {
    $cell = Coordinate::stringFromColumnIndex($colIndex) . $filaEncabezados;
    $sheet->setCellValue($cell, $cal);
    $sheet->getStyle($cell)->applyFromArray($styleYellowFill);
    $colIndex++;
}

// Encabezados adicionales
$extraEncabezados = ['Cantidad Pedido', 'Altura/Cajas Por Pallet'];
foreach ($extraEncabezados as $e) {
    $cell = Coordinate::stringFromColumnIndex($colIndex) . $filaEncabezados;
    $sheet->setCellValue($cell, $e);
    $sheet->getStyle($cell)->applyFromArray($styleHeader);
    $colIndex++;
}

// Cuerpo Tabla Paletizaje

$filaEncabezados += 1;

// Agrupar los datos por pedido
$agrupadoPorPedido = [];
foreach ($datos as $fila) {
    $agrupadoPorPedido[$fila['pedido']][] = $fila;
}
uksort($agrupadoPorPedido, function($a, $b) use ($prioridades) {
    $prioridadA = $prioridades[$a] ?? null;
    $prioridadB = $prioridades[$b] ?? null;

    if ($prioridadA !== null && $prioridadB !== null) {
        return $prioridadA <=> $prioridadB; // ambos tienen prioridad
    } elseif ($prioridadA !== null) {
        return -1; // A tiene prioridad, B no => A va primero
    } elseif ($prioridadB !== null) {
        return 1; // B tiene prioridad, A no => B va primero
    } else {
        return $a <=> $b; // ninguno tiene prioridad => ordenar por número de pedido
    }
});

foreach ($agrupadoPorPedido as &$filasDelPedido) {
    usort($filasDelPedido, function($a, $b) {
        return $a['Orden_Calibre'] <=> $b['Orden_Calibre'];
    });
}
unset($filasDelPedido); 

foreach ($agrupadoPorPedido as $pedido => $filas) {
    $rowStart = $filaEncabezados;
    $rowEnd = $filaEncabezados + count($filas) - 1;

    foreach ($filas as $filaDatos) {
        $col = 3;

        // Solo en la primera fila se escribe el valor y se hace merge
        if ($filaEncabezados == $rowStart) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . $rowStart, $pedido);
            if ($rowEnd > $rowStart) {
                $sheet->mergeCells(Coordinate::stringFromColumnIndex($col) . $rowStart . ':' . Coordinate::stringFromColumnIndex($col) . $rowEnd);
            }
        }
        $col++; // columna embalaje_cod
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaEncabezados, $filaDatos['embalaje_cod'] . "/" . $filaDatos['etiqueta']);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaEncabezados, $filaDatos['pallet']);

        foreach ($calibres as $cal) {
            $valor = $filaDatos['calibres'][$cal] ?? '';
            $cell = Coordinate::stringFromColumnIndex($col++) . $filaEncabezados;
            $sheet->setCellValue($cell, $valor);
            $sheet->getStyle($cell)->applyFromArray(
                is_numeric($valor) && $valor >= 0 ? $styleYellowFill : $styleTableCell
            );
        }

        // Solo en la primera fila se escribe el valor y se hace merge
        $cantidadPedido = $pedidoCantidades[$pedido] ?? '';
        if ($filaEncabezados == $rowStart) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . $rowStart, $cantidadPedido);
            if ($rowEnd > $rowStart) {
                $sheet->mergeCells(Coordinate::stringFromColumnIndex($col) . $rowStart . ':' . Coordinate::stringFromColumnIndex($col) . $rowEnd);
            }
        }
        $col++; // siguiente columna para altura

        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaEncabezados, $filaDatos['altura']);

        // Estilos de toda la fila
        $sheet->getStyle("C{$filaEncabezados}:" . Coordinate::stringFromColumnIndex($col - 1) . "{$filaEncabezados}")
              ->applyFromArray($styleTableCell);

        $filaEncabezados++;
    }
}


// Ajuste automático de ancho de columnas para segunda tabla
for ($i = 3; $i < $col; $i++) {
    $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
}

$observacion = $cabecera['observacion'];
// --- Fila Observación Final (fondo amarillo y fusión) ---
$filaObs = $filaEncabezados + 3;
$ultimaCol = Coordinate::stringFromColumnIndex($col - 1);
$sheet->mergeCells("A{$filaObs}:{$ultimaCol}{$filaObs}");
$sheet->setCellValue("A{$filaObs}", 'OBSERVACION: '. $observacion);
$sheet->getStyle("A{$filaObs}:{$ultimaCol}{$filaObs}")->applyFromArray($styleYellowFillObs);

// Ajuste automático de ancho de columnas (general)
for ($i = 1; $i <= $col; $i++) {
    $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
}

if (ob_get_length()) {
    ob_end_clean();
}

// Configura las cabeceras para la descarga del archivo Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=Instructivo_{$id_instructivo}_v{$version}_Exp{$cabecera['nombre_exportadora']}.xlsx");
header('Cache-Control: max-age=0');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Pragma: public');

// Guarda el archivo Excel en la salida
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
