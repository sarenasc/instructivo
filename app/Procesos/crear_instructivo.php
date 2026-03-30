<?php
$titulo_pagina = 'Crear Instructivo';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid mt-4">
    <h2 class="mb-4">📋 Crear Nuevo Instructivo de Proceso</h2>

    <!-- PASO 1: CABECERA -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">1️⃣ Cabecera del Instructivo</h5>
        </div>
        <div class="card-body">
            <form id="formCabecera">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="exportadora" class="form-label">Exportadora</label>
                        <select class="form-select" id="exportadora" name="exportadora" required>
                            <option value="">Cargando...</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="especie" class="form-label">Especie</label>
                        <select class="form-select" id="especie" name="especie" required>
                            <option value="">Cargando...</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="turno" class="form-label">Turno</label>
                        <select class="form-select" id="turno" name="turno" required>
                            <option value="">Seleccione...</option>
                            <option value="Turno 1">Turno 1</option>
                            <option value="Turno 2">Turno 2</option>
                            <option value="Turno 3">Turno 3</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="fecha" name="fecha" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="observacion" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacion" name="observacion" rows="2"></textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- PASO 2: PEDIDOS -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">2️⃣ Pedidos (Cantidad Total por Pedido)</h5>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end mb-4">
                <div class="col-md-4">
                    <label for="numero_pedido" class="form-label">Número de Pedido</label>
                    <input type="number" class="form-control" id="numero_pedido" placeholder="Ej: 1044">
                </div>
                <div class="col-md-3">
                    <label for="cantidad_pedido" class="form-label">Cantidad Total</label>
                    <input type="number" class="form-control" id="cantidad_pedido" placeholder="Ej: 1000" min="1">
                </div>
                <div class="col-md-3">
                    <label for="prioridad_pedido" class="form-label">Prioridad</label>
                    <input type="number" class="form-control" id="prioridad_pedido" placeholder="Ej: 1" min="1">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success w-100" id="btnAgregarPedido">➕ Agregar</button>
                </div>
            </div>

            <h6 class="mb-3">Pedidos Agregados</h6>
            <table class="table table-bordered table-hover" id="tablaPedidos">
                <thead class="table-light">
                    <tr>
                        <th>N° Pedido</th>
                        <th>Cantidad Total</th>
                        <th>Prioridad</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Se llena dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- PASO 3: DETALLE POR CALIBRE -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">3️⃣ Detalle por Calibre (Asignar a Pedido)</h5>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end mb-4">
                <div class="col-md-3">
                    <label for="detalle_calibre" class="form-label">Calibres (Ctrl+Click para múltiple)</label>
                    <select class="form-select" id="detalle_calibre" multiple size="5">
                        <option value="">Seleccione...</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="detalle_pedido" class="form-label">Asignar a Pedido</label>
                    <select class="form-select" id="detalle_pedido">
                        <option value="">Primero agregue pedidos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="detalle_cantidad" class="form-label">Cantidad Pedido</label>
                    <input type="number" class="form-control" id="detalle_cantidad" placeholder="Ej: 500" min="0">
                </div>
                <div class="col-md-2">
                    <label for="detalle_embalaje" class="form-label">Embalaje</label>
                    <select class="form-select" id="detalle_embalaje">
                        <option value="">Seleccione...</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-info w-100" id="btnAgregarDetalle">➕ Agregar</button>
                </div>
            </div>

            <!-- Más campos del detalle -->
            <div class="row g-3 mb-4">
                <div class="col-md-2">
                    <label for="detalle_categoria" class="form-label">Categoría</label>
                    <select class="form-select" id="detalle_categoria">
                        <option value="">Seleccione...</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="detalle_plu" class="form-label">PLU</label>
                    <select class="form-select" id="detalle_plu">
                        <option value="">Seleccione...</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="detalle_etiqueta" class="form-label">Etiqueta</label>
                    <select class="form-select" id="detalle_etiqueta">
                        <option value="">Seleccione...</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="detalle_pallet" class="form-label">Pallet</label>
                    <select class="form-select" id="detalle_pallet">
                        <option value="">Seleccione...</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="detalle_altura" class="form-label">Altura Pallet</label>
                    <select class="form-select" id="detalle_altura">
                        <option value="">Primero seleccione embalaje</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="detalle_destino" class="form-label">Destino</label>
                    <select class="form-select" id="detalle_destino">
                        <option value="">Seleccione...</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="detalle_variedad" class="form-label">Variedad Etiquetada</label>
                    <input type="text" class="form-control" id="detalle_variedad" placeholder="Ej: Golden">
                </div>
                <div class="col-md-3">
                    <label for="detalle_obs" class="form-label">Observación</label>
                    <input type="text" class="form-control" id="detalle_obs" placeholder="Observaciones...">
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Detalle Agregado</h6>
                <button class="btn btn-outline-primary btn-sm" id="btnVerPantallaCompleta" onclick="mostrarPantallaCompleta()">
                    👁️ Ver en Pantalla Completa
                </button>
            </div>
            
            <!-- Cinta de Calibres -->
            <div id="cintaCalibres" class="mb-3 p-3 bg-light rounded" style="display: none;">
                <strong class="d-block mb-2">📏 Calibres Seleccionados:</strong>
                <div id="cintaCalibresContent" style="display: flex; gap: 6px; flex-wrap: wrap;"></div>
            </div>
            
            <table class="table table-bordered table-hover" id="tablaDetalle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">Pedido</th>
                        <th style="width: 100px;">Variedad</th>
                        <th style="width: 150px;">Embalaje</th>
                        <th style="width: 120px;">Etiqueta</th>
                        <th style="width: 200px;">Calibres</th>
                        <th style="width: 80px;">Categoría</th>
                        <th style="width: 80px;">PLU</th>
                        <th style="width: 100px;">Destino</th>
                        <th style="width: 120px;">Pallet</th>
                        <th style="width: 90px;">Cantidad</th>
                        <th style="width: 120px;">Altura</th>
                        <th>Obs</th>
                        <th style="width: 100px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Se llena dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- BOTÓN GUARDAR -->
    <div class="d-grid gap-2 mb-5">
        <button class="btn btn-primary btn-lg" id="btnGuardarInstructivo">
            💾 GUARDAR INSTRUCTIVO COMPLETO
        </button>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="modalConfirmacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Guardado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Exportadora:</strong> <span id="confirmExportadora"></span></p>
                <p><strong>Especie:</strong> <span id="confirmEspecie"></span></p>
                <p><strong>Turno:</strong> <span id="confirmTurno"></span></p>
                <p><strong>Fecha:</strong> <span id="confirmFecha"></span></p>
                <hr>
                <p><strong>Pedidos:</strong> <span id="confirmPedidos"></span></p>
                <p><strong>Detalle (calibres):</strong> <span id="confirmDetalle"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarGuardado">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PANTALLA COMPLETA -->
<div class="modal fade" id="modalPantallaCompleta" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">🔍 Vista Completa - Detalle de Calibres</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Cinta de calibres en modal -->
                    <div id="cintaCalibresModal" class="mb-4 p-3 bg-light rounded">
                        <strong class="d-block mb-2">📏 Todos los Calibres:</strong>
                        <div id="cintaCalibresModalContent" style="display: flex; gap: 8px; flex-wrap: wrap;"></div>
                    </div>
                    
                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0" id="statPedidos">0</h3>
                                    <small>📦 Pedidos</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0" id="statCalibres">0</h3>
                                    <small>📏 Calibres</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0" id="statCajas">0</h3>
                                    <small>📈 Total Cajas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h3 class="mb-0" id="statDestinos">0</h3>
                                    <small>🌍 Destinos</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla completa -->
                    <table class="table table-bordered table-hover" id="tablaDetalleModal">
                        <thead class="table-light">
                            <tr>
                                <th>Pedido</th>
                                <th>Variedad</th>
                                <th>Embalaje</th>
                                <th>Etiqueta</th>
                                <th>Calibres</th>
                                <th>Categoría</th>
                                <th>PLU</th>
                                <th>Destino</th>
                                <th>Pallet</th>
                                <th>Cantidad</th>
                                <th>Altura</th>
                                <th>Obs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se llena dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$scripts_extra = '
<script src="../assets/js/crear_instructivo.js"></script>
';
require_once __DIR__ . '/../includes/footer.php';
?>
