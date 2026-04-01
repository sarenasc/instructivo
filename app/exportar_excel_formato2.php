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
use PhpOffice\PhpSpreadsheet\Style\Fill;

$id_instructivo = $_GET['id_instructivo'] ?? null;
$version = $_GET['version'] ?? null;

if (!$id_instructivo || !$version) {
    die('Faltan parámetros de ID de instructivo o versión.');
}

// --- Obtener cabecera ---
$sqlCab = "SELECT TOP 1 cab.id_instructivo, cab.fecha,
                  cab.id_exportadora AS id_exportadora,
                  exp.nombre_exportadora AS nombre_exportadora,
                  cab.id_especie AS id_especie,
                  esp.especie AS nombre_especie,
                  det.var_etiquetada AS variedad,
                  det.version AS version,
                  cab.observacion AS observacion,
                  cab.turno AS turno
           FROM inst_cab_instructivo cab
           JOIN inst_exportadora exp ON exp.id = cab.id_exportadora
           JOIN especie esp ON esp.id_especie = cab.id_especie
           JOIN inst_detalle_instructivo det ON det.id_cab_instructivo = cab.id_instructivo
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
    i.id_categoria AS id_categoria,
    p.Descrip_pallet,
    i.altura_pallet,
    CASE WHEN ap.id IS NOT NULL THEN CONCAT(ap.altura,'/',ap.cajas) ELSE NULL END as Altura,
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
ORDER BY i.numero_pedido ASC, c.orden ASC";

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
                'id_categoria' => $row['id_categoria'],
                'pallet' => $row['Descrip_pallet'],
                'var_etiquetada' => $row['var_etiquetada'],
                'altura_pallet' => $row['altura_pallet'],
                'altura' => $row['Altura'],
                'observacion' => $row['observacion'],
                'orden_calibre' => $row['Orden_Calibre'],
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

$sqlCantidades = "SELECT numero_pedido, cantidad, prioridad
                 FROM inst_pedidos
                 WHERE id_instructivo = ? AND version = ?
                 ORDER BY prioridad ASC";
$stmtCant = sqlsrv_query($conn, $sqlCantidades, [$id_instructivo, $version]);
if ($stmtCant !== false) {
    while ($row = sqlsrv_fetch_array($stmtCant, SQLSRV_FETCH_ASSOC)) {
        $pedidoCantidades[$row['numero_pedido']] = $row['cantidad'];
        $prioridades[$row['numero_pedido']] = $row['prioridad'];
    }
}

asort($calibres);
$calibres = array_keys($calibres);

// --- Agrupaciones de calibres desde BD ---
// $mapaGruposPorCat[id_categoria][nombre_calibre] = nombre_grupo
$mapaGruposPorCat = [];
$mapaGrupos       = []; // calibre => grupo (para $tieneAgrupaciones y fila de headers)
$id_especie_cab     = $cabecera['id_especie']     ?? null;
$id_exportadora_cab = $cabecera['id_exportadora'] ?? null;
if ($id_especie_cab && $id_exportadora_cab) {
    $sqlAgrup = "SELECT ag.id_categoria, ag.nombre_grupo, c.nombre_calibre
                 FROM inst_agrupacion_calibre ag
                 JOIN inst_agrupacion_calibre_detalle agd ON agd.id_agrupacion = ag.id
                 JOIN inst_calibre c ON c.id = agd.id_calibre
                 WHERE ag.id_especie = ? AND ag.id_exportadora = ?
                   AND ag.id_categoria IN (
                       SELECT DISTINCT id_categoria
                       FROM inst_detalle_instructivo
                       WHERE id_cab_instructivo = ? AND version = ?
                   )
                 ORDER BY ag.nombre_grupo, c.orden";
    $stmtAgrup = sqlsrv_query($conn, $sqlAgrup, [$id_especie_cab, $id_exportadora_cab, $id_instructivo, $version]);
    if ($stmtAgrup) {
        while ($rowA = sqlsrv_fetch_array($stmtAgrup, SQLSRV_FETCH_ASSOC)) {
            $mapaGruposPorCat[(int)$rowA['id_categoria']][$rowA['nombre_calibre']] = $rowA['nombre_grupo'];
            $mapaGrupos[$rowA['nombre_calibre']] = $rowA['nombre_grupo']; // para header fila 12
        }
    }
}

$tieneAgrupaciones = !empty($mapaGrupos);

// --- Crear Excel ---
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("I_{$id_instructivo}_V_{$version}");
$sheet->setShowGridlines(false);

// Configuración de página: hoja carta completa, horizontal, centrada y legible
$pageSetup = $sheet->getPageSetup();
$pageSetup->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
$pageSetup->setPaperSize(PageSetup::PAPERSIZE_LETTER);
$pageSetup->setFitToPage(true);
$pageSetup->setFitToWidth(1);
$pageSetup->setFitToHeight(0); // permite crecer en alto sin aplastar demasiado la letra

$sheet->getPageMargins()->setTop(0.3);
$sheet->getPageMargins()->setRight(0.25);
$sheet->getPageMargins()->setLeft(0.25);
$sheet->getPageMargins()->setBottom(0.3);
$sheet->getPageMargins()->setHeader(0.15);
$sheet->getPageMargins()->setFooter(0.15);

$sheet->getSheetView()->setZoomScale(90);
$sheet->freezePane('A14');

$spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
$spreadsheet->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

// Anchos base más controlados para mejor lectura en hoja carta
$anchos = [
    'A' => 12, 'B' => 18, 'C' => 14, 'D' => 24, 'E' => 22,
    'F' => 8,  'G' => 8,  'H' => 8,  'I' => 8,  'J' => 8,
    'K' => 8,  'L' => 14, 'M' => 10, 'N' => 16, 'O' => 22,
    'P' => 14, 'Q' => 14, 'R' => 14, 'S' => 14, 'T' => 14,
    'U' => 14, 'V' => 14, 'W' => 14, 'X' => 14, 'Y' => 14,
    'Z' => 14,
];
foreach ($anchos as $colLetra => $ancho) {
    $sheet->getColumnDimension($colLetra)->setWidth($ancho);
}

// Estilos
$styleTitulo = [
    'font' => [
        'bold' => true,
        'size' => 16,
        'name' => 'Arial',
        'color' => ['argb' => 'FF000000']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
];

$styleHeader = [
    'font' => [
        'bold' => true,
        'size' => 10,
        'name' => 'Arial'
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FFD9D9D9']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
    ]
];

$styleYellowFill = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FFFFFF99']
    ],
    'font' => [
        'size' => 10,
        'bold' => true,
        'name' => 'Arial'
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
    ]
];

$styleYellowFillObs = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FFFFFF99']
    ],
    'font' => [
        'size' => 11,
        'bold' => true,
        'name' => 'Arial'
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_LEFT,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
    ]
];

$styleXCalibre = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FFFFFF99']
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => Color::COLOR_BLACK]
        ]
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
        'wrapText'   => true
    ],
    'font' => [
        'size' => 16,
        'bold' => true,
        'name' => 'Arial'
    ]
];

$styleGrupoData = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FFB8CCE4']
    ],
    'font' => ['bold' => true, 'size' => 12, 'name' => 'Arial'],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
    ],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];

$styleGrupoHeader = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FFB8CCE4']
    ],
    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
    ],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];

$styleTableCell = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => Color::COLOR_BLACK]
        ]
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
        'wrapText'   => true
    ],
    'font' => [
        'size' => 10,
        'bold' => false,
        'name' => 'Arial'
    ]
];

$estiloLineaA = [
    'font' => ['size' => 10, 'bold' => true, 'name' => 'Arial'],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];

$estiloLineaB = [
    'font' => ['size' => 10, 'name' => 'Arial'],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];

// Determinar última columna real según la cantidad de calibres
$totalColumnasTabla1 = 5 + count($calibres) + 4;
$ultimaColTabla1 = Coordinate::stringFromColumnIndex($totalColumnasTabla1);

// --- Cabecera Visual ---
$sheet->mergeCells("A1:{$ultimaColTabla1}1")->setCellValue('A1', 'INSTRUCTIVO PLANTA ALMAHUE');
$sheet->mergeCells("A11:{$ultimaColTabla1}11")->setCellValue('A11', 'INFORMACION DE EMBALAJE');

$sheet->mergeCells('A3:B3')->setCellValue('A3', 'Exportadora:');
$sheet->mergeCells('A4:B4')->setCellValue('A4', 'Fecha:');
$sheet->mergeCells('A5:B5')->setCellValue('A5', 'Especie:');
$sheet->mergeCells('A6:B6')->setCellValue('A6', 'Variedad Real:');
$sheet->mergeCells('A7:B7')->setCellValue('A7', 'Turno:');
$sheet->mergeCells('A8:B8')->setCellValue('A8', 'Número Instructivo:');
$sheet->mergeCells('A9:B9')->setCellValue('A9', 'Versión:');

$sheet->setCellValue('C3', $cabecera['nombre_exportadora']);

if ($cabecera['fecha'] instanceof DateTime) {
    $sheet->setCellValue('C4', $cabecera['fecha']->format('d-m-Y'));
} else {
    $sheet->setCellValue('C4', date('d-m-Y', strtotime((string)$cabecera['fecha'])));
}

$sheet->setCellValue('C5', $cabecera['nombre_especie']);
$sheet->setCellValue('C6', $cabecera['variedad']);
$sheet->setCellValue('C7', $cabecera['turno'] ?? 'Sin turno');
$sheet->setCellValue('C8', $cabecera['id_instructivo']);
$sheet->setCellValue('C9', $version);

$sheet->getStyle("A1:{$ultimaColTabla1}1")->applyFromArray($styleTitulo);
$sheet->getStyle("A11:{$ultimaColTabla1}11")->applyFromArray($styleTitulo);
$sheet->getStyle('A3:B9')->applyFromArray($estiloLineaA);
$sheet->getStyle('C3:C9')->applyFromArray($estiloLineaB);

foreach (range(1, 11) as $r) {
    $sheet->getRowDimension($r)->setRowHeight(22);
}
$sheet->getRowDimension(1)->setRowHeight(28);
$sheet->getRowDimension(11)->setRowHeight(28);

// --- Fila de agrupaciones de calibres (fila 12) ---
if ($tieneAgrupaciones) {
    $sheet->getRowDimension(12)->setRowHeight(18);
    $calibreColBase = 6; // las 5 columnas fijas van de 1 a 5
    // Calcular rango de columnas por grupo
    $gruposRango = [];
    foreach ($calibres as $idx => $cal) {
        $grupo = $mapaGrupos[$cal] ?? null;
        if ($grupo !== null) {
            $colIdx = $calibreColBase + $idx;
            if (!isset($gruposRango[$grupo])) {
                $gruposRango[$grupo] = ['start' => $colIdx, 'end' => $colIdx];
            } else {
                $gruposRango[$grupo]['end'] = $colIdx;
            }
        }
    }
    foreach ($gruposRango as $nombre => $cols) {
        $cA = Coordinate::stringFromColumnIndex($cols['start']) . '12';
        $cB = Coordinate::stringFromColumnIndex($cols['end']) . '12';
        if ($cols['start'] !== $cols['end']) {
            $sheet->mergeCells("{$cA}:{$cB}");
        }
        $sheet->setCellValue($cA, $nombre);
        $sheet->getStyle("{$cA}:{$cB}")->applyFromArray($styleGrupoHeader);
    }
}

// --- Encabezados Tabla 1 ---
$filaEncabezados = 13;
$colIndex = 1;

$encabezadosFijos = ['Pedido', 'Var Etiquetada', 'Código Envase', 'Embalaje', 'Etiqueta'];
foreach ($encabezadosFijos as $enc) {
    $cell = Coordinate::stringFromColumnIndex($colIndex) . $filaEncabezados;
    $sheet->setCellValue($cell, $enc);
    $sheet->getStyle($cell)->applyFromArray($styleHeader);
    $colIndex++;
}

foreach ($calibres as $cal) {
    $cell = Coordinate::stringFromColumnIndex($colIndex) . $filaEncabezados;
    $sheet->setCellValue($cell, $cal);
    $sheet->getStyle($cell)->applyFromArray($styleYellowFill);
    $colIndex++;
}

$extraEncabezados = ['Categoría', 'PLU', 'Destino', 'Observación'];
foreach ($extraEncabezados as $e) {
    $cell = Coordinate::stringFromColumnIndex($colIndex) . $filaEncabezados;
    $sheet->setCellValue($cell, $e);
    $sheet->getStyle($cell)->applyFromArray($styleHeader);
    $colIndex++;
}

$sheet->getRowDimension($filaEncabezados)->setRowHeight(32);
$sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(12, 13);

// --- Cuerpo Tabla 1 ---
$filaContenido = $filaEncabezados + 1;

$agrupadoPorPedido = [];
foreach ($datos as $fila) {
    $agrupadoPorPedido[$fila['pedido']][] = $fila;
}

uksort($agrupadoPorPedido, function ($a, $b) use ($prioridades) {
    $prioridadA = $prioridades[$a] ?? null;
    $prioridadB = $prioridades[$b] ?? null;

    if ($prioridadA !== null && $prioridadB !== null) return $prioridadA <=> $prioridadB;
    if ($prioridadA !== null) return -1;
    if ($prioridadB !== null) return 1;
    return $a <=> $b;
});

foreach ($agrupadoPorPedido as &$filasDelPedido) {
    usort($filasDelPedido, function ($a, $b) {
        return ($a['orden_calibre'] ?? 9999) <=> ($b['orden_calibre'] ?? 9999);
    });
}
unset($filasDelPedido);

foreach ($agrupadoPorPedido as $pedido => $filas) {
    $rowStart = $filaContenido;
    $rowEnd = $filaContenido + count($filas) - 1;

    foreach ($filas as $filaDatos) {
        $col = 1;

        if ($filaContenido == $rowStart) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . $filaContenido, $pedido);
            if ($rowEnd > $rowStart) {
                $sheet->mergeCells(Coordinate::stringFromColumnIndex($col) . $rowStart . ':' . Coordinate::stringFromColumnIndex($col) . $rowEnd);
            }
        }

        $col++;
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido, $filaDatos['var_etiquetada']);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido, $filaDatos['embalaje_cod']);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido, $filaDatos['embalaje_des']);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido, $filaDatos['etiqueta']);

        $gruposEstaFila1 = $mapaGruposPorCat[(int)($filaDatos['id_categoria'] ?? 0)] ?? [];
        $gruposRangoFila1 = [];
        foreach ($calibres as $cal) {
            $grupo = $gruposEstaFila1[$cal] ?? null;
            $cell  = Coordinate::stringFromColumnIndex($col) . $filaContenido;
            if ($grupo !== null) {
                if (!isset($gruposRangoFila1[$grupo])) {
                    $gruposRangoFila1[$grupo] = ['start' => $col, 'end' => $col];
                } else {
                    $gruposRangoFila1[$grupo]['end'] = $col;
                }
                $sheet->getStyle($cell)->applyFromArray($styleGrupoData);
            } else {
                $valor = $filaDatos['calibres'][$cal] ?? '';
                if ($valor === 0 || $valor === '0') $valor = 'X';
                $sheet->setCellValue($cell, $valor);
                $sheet->getStyle($cell)->applyFromArray($valor === 'X' ? $styleXCalibre : (is_numeric($valor) ? $styleYellowFill : $styleTableCell));
            }
            $col++;
        }
        foreach ($gruposRangoFila1 as $nombreGrupo => $cols) {
            $cA = Coordinate::stringFromColumnIndex($cols['start']) . $filaContenido;
            $cB = Coordinate::stringFromColumnIndex($cols['end'])   . $filaContenido;
            if ($cols['start'] !== $cols['end']) $sheet->mergeCells("{$cA}:{$cB}");
            $sheet->setCellValue($cA, $nombreGrupo);
            $sheet->getStyle("{$cA}:{$cB}")->applyFromArray($styleGrupoData);
        }

        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido, $filaDatos['categoria']);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido, $filaDatos['plu']);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido, $filaDatos['destino']);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido, $filaDatos['observacion']);

        $sheet->getStyle("A{$filaContenido}:" . Coordinate::stringFromColumnIndex($col - 1) . "{$filaContenido}")->applyFromArray($styleTableCell);
        $sheet->getRowDimension($filaContenido)->setRowHeight(24);

        $filaContenido++;
    }
}

// --- Segunda tabla: Paletizaje ---
$filaPalTitulo = $filaContenido + 3;
$sheet->mergeCells("A{$filaPalTitulo}:N{$filaPalTitulo}")->setCellValue("A{$filaPalTitulo}", 'INFORMACION DE PALETIZAJE');
$sheet->getStyle("A{$filaPalTitulo}:N{$filaPalTitulo}")->applyFromArray($styleTitulo);
$sheet->getRowDimension($filaPalTitulo)->setRowHeight(28);

// Fila de agrupaciones para tabla 2
if ($tieneAgrupaciones) {
    $filaGrupos2 = $filaPalTitulo + 2;
    $sheet->getRowDimension($filaGrupos2)->setRowHeight(18);
    $calibreColBase2 = 6; // cols C,D,E son fijas (Pedido, Cod Env, Tipo Pallet), calibres desde col 6
    foreach ($gruposRango as $nombre => $cols) {
        $cA = Coordinate::stringFromColumnIndex($cols['start']) . $filaGrupos2;
        $cB = Coordinate::stringFromColumnIndex($cols['end']) . $filaGrupos2;
        if ($cols['start'] !== $cols['end']) {
            $sheet->mergeCells("{$cA}:{$cB}");
        }
        $sheet->setCellValue($cA, $nombre);
        $sheet->getStyle("{$cA}:{$cB}")->applyFromArray($styleGrupoHeader);
    }
    $filaEncabezados2 = $filaPalTitulo + 3;
} else {
    $filaEncabezados2 = $filaPalTitulo + 2;
}

$colIndex = 3;

$encabezadosFijos2 = ['Pedido', 'Código Envase / Etiqueta', 'Tipo Pallet'];
foreach ($encabezadosFijos2 as $enc) {
    $cell = Coordinate::stringFromColumnIndex($colIndex) . $filaEncabezados2;
    $sheet->setCellValue($cell, $enc);
    $sheet->getStyle($cell)->applyFromArray($styleHeader);
    $colIndex++;
}

foreach ($calibres as $cal) {
    $cell = Coordinate::stringFromColumnIndex($colIndex) . $filaEncabezados2;
    $sheet->setCellValue($cell, $cal);
    $sheet->getStyle($cell)->applyFromArray($styleYellowFill);
    $colIndex++;
}

$extraEncabezados2 = ['Cantidad Pedido', 'Altura/Cajas Por Pallet'];
foreach ($extraEncabezados2 as $e) {
    $cell = Coordinate::stringFromColumnIndex($colIndex) . $filaEncabezados2;
    $sheet->setCellValue($cell, $e);
    $sheet->getStyle($cell)->applyFromArray($styleHeader);
    $colIndex++;
}
$sheet->getRowDimension($filaEncabezados2)->setRowHeight(32);

$filaContenido2 = $filaEncabezados2 + 1;

foreach ($agrupadoPorPedido as $pedido => $filas) {
    $rowStart = $filaContenido2;
    $rowEnd = $filaContenido2 + count($filas) - 1;

    foreach ($filas as $filaDatos) {
        $col = 3;

        if ($filaContenido2 == $rowStart) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . $rowStart, $pedido);
            if ($rowEnd > $rowStart) {
                $sheet->mergeCells(Coordinate::stringFromColumnIndex($col) . $rowStart . ':' . Coordinate::stringFromColumnIndex($col) . $rowEnd);
            }
        }

        $col++;
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido2, $filaDatos['embalaje_cod'] . '/' . $filaDatos['etiqueta']);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido2, $filaDatos['pallet']);

        $gruposEstaFila2 = $mapaGruposPorCat[(int)($filaDatos['id_categoria'] ?? 0)] ?? [];
        $gruposRangoFila2 = [];
        foreach ($calibres as $cal) {
            $grupo = $gruposEstaFila2[$cal] ?? null;
            $cell  = Coordinate::stringFromColumnIndex($col) . $filaContenido2;
            if ($grupo !== null) {
                if (!isset($gruposRangoFila2[$grupo])) {
                    $gruposRangoFila2[$grupo] = ['start' => $col, 'end' => $col];
                } else {
                    $gruposRangoFila2[$grupo]['end'] = $col;
                }
                $sheet->getStyle($cell)->applyFromArray($styleGrupoData);
            } else {
                $valor = $filaDatos['calibres'][$cal] ?? '';
                if ($valor === 0 || $valor === '0') $valor = 'X';
                $sheet->setCellValue($cell, $valor);
                $sheet->getStyle($cell)->applyFromArray($valor === 'X' ? $styleXCalibre : (is_numeric($valor) ? $styleYellowFill : $styleTableCell));
            }
            $col++;
        }
        foreach ($gruposRangoFila2 as $nombreGrupo => $cols) {
            $cA = Coordinate::stringFromColumnIndex($cols['start']) . $filaContenido2;
            $cB = Coordinate::stringFromColumnIndex($cols['end'])   . $filaContenido2;
            if ($cols['start'] !== $cols['end']) $sheet->mergeCells("{$cA}:{$cB}");
            $sheet->setCellValue($cA, $nombreGrupo);
            $sheet->getStyle("{$cA}:{$cB}")->applyFromArray($styleGrupoData);
        }

        $cantidadPedido = $pedidoCantidades[$pedido] ?? '';
        if ($cantidadPedido === 0 || $cantidadPedido === '0') $cantidadPedido = 'TODOS';
        if ($filaContenido2 == $rowStart) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . $rowStart, $cantidadPedido);
            if ($rowEnd > $rowStart) {
                $sheet->mergeCells(Coordinate::stringFromColumnIndex($col) . $rowStart . ':' . Coordinate::stringFromColumnIndex($col) . $rowEnd);
            }
        }
        $col++;

        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $filaContenido2, $filaDatos['altura']);
        $sheet->getStyle("C{$filaContenido2}:" . Coordinate::stringFromColumnIndex($col - 1) . "{$filaContenido2}")->applyFromArray($styleTableCell);
        $sheet->getRowDimension($filaContenido2)->setRowHeight(24);

        $filaContenido2++;
    }
}

$observacion = trim((string)($cabecera['observacion'] ?? ''));
$filaObs = $filaContenido2 + 2;
$ultimaCol = Coordinate::stringFromColumnIndex(max($col - 1, $totalColumnasTabla1));
$sheet->mergeCells("A{$filaObs}:{$ultimaCol}{$filaObs}");
$sheet->setCellValue("A{$filaObs}", 'OBSERVACIÓN: ' . ($observacion !== '' ? $observacion : 'Sin observaciones'));
$sheet->getStyle("A{$filaObs}:{$ultimaCol}{$filaObs}")->applyFromArray($styleYellowFillObs);
$sheet->getRowDimension($filaObs)->setRowHeight(28);

// Alinear algunas columnas específicas
foreach (['D', 'E', 'N', 'O'] as $colWrap) {
    $sheet->getStyle("{$colWrap}:{$colWrap}")->getAlignment()->setWrapText(true);
}

if (ob_get_length()) {
    ob_end_clean();
}

$nombreExportadora = preg_replace('/[^A-Za-z0-9_-]/', '_', (string)$cabecera['nombre_exportadora']);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=Instructivo_{$id_instructivo}_v{$version}_Exp{$nombreExportadora}.xlsx");
header('Cache-Control: max-age=0');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
