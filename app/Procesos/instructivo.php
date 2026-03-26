<?php
$titulo_pagina = 'Creación de Instructivo';
require_once __DIR__ . '/../includes/header.php';
?>

<body class="p-4">
<h1>Instructivo de Proceso Planta Almahue</h1>

<div class="container-fluid">
    <form id="instructivoForm">
        <!-- Cabecera -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" class="form-control" id="fecha" name="fecha" required>
            </div>
            <div class="col-md-3">
                <label for="id_exportadora" class="form-label">Exportadora</label>
                <select class="form-select" id="id_exportadora" name="id_exportadora" required>
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="id_especie" class="form-label">Especie</label>
                <select class="form-select" id="id_especie" name="id_especie" required>
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="turno" class="form-label">Turno</label>
                <select class="form-select" id="turno" name="turno" required>
                    <option value="">Seleccione...</option>
                    <option value="DIA">Día</option>
                    <option value="NOCHE">Noche</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label for="observacion" class="form-label">Observación</label>
                <textarea class="form-control" id="observacion" name="observacion" rows="2"></textarea>
            </div>
        </div>

        <!-- Detalle -->
        <hr>
        <h4>Detalle del Instructivo</h4>
        
        <div class="row mb-3">
            <div class="col-md-2">
                <label for="numero_pedido" class="form-label">Número Pedido</label>
                <input type="text" class="form-control" id="numero_pedido" name="numero_pedido">
            </div>
            <div class="col-md-2">
                <label for="var_etiquetada" class="form-label">Var. Etiquetada</label>
                <input type="text" class="form-control" id="var_etiquetada" name="var_etiquetada">
            </div>
            <div class="col-md-2">
                <label for="id_embalaje" class="form-label">Embalaje</label>
                <select class="form-select" id="id_embalaje" name="id_embalaje">
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="id_calibre" class="form-label">Calibre</label>
                <select class="form-select" id="id_calibre" name="id_calibre">
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="id_categoria" class="form-label">Categoría</label>
                <select class="form-select" id="id_categoria" name="id_categoria">
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="id_plu" class="form-label">PLU</label>
                <select class="form-select" id="id_plu" name="id_plu">
                    <option value="">Seleccione...</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-2">
                <label for="id_destino" class="form-label">Destino</label>
                <select class="form-select" id="id_destino" name="id_destino">
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="id_pallet" class="form-label">Pallet</label>
                <select class="form-select" id="id_pallet" name="id_pallet">
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="id_etiqueta" class="form-label">Etiqueta</label>
                <select class="form-select" id="id_etiqueta" name="id_etiqueta">
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="altura_pallet" class="form-label">Altura Pallet</label>
                <select class="form-select" id="altura_pallet" name="altura_pallet">
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="cantidad_pedido" class="form-label">Cantidad Pedido</label>
                <input type="number" class="form-control" id="cantidad_pedido" name="cantidad_pedido">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Guardar Instructivo</button>
                <button type="button" class="btn btn-secondary">Limpiar</button>
            </div>
        </div>
    </form>
</div>

<?php
$scripts_extra = '<script src="instructivo.js"></script>';
require_once __DIR__ . '/../includes/footer.php';
?>
