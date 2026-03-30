<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}
$titulo_pagina = 'Editar Instructivo';
require_once '../includes/header.php';
?>
<style>
    .card-header-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .version-badge {
        font-size: 1.2rem;
        padding: 8px 16px;
    }
    .btn-guardar {
        font-size: 1.1rem;
        padding: 12px 30px;
    }
    .cinta-calibres {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
    }
    .badge-calibre {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 13px;
    }
</style>

<div class="container-fluid mt-4">
    <!-- Búsqueda de Instructivo -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">📋 Buscar Instructivo para Editar</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Exportadora</label>
                            <select class="form-select" id="filtro_exportadora">
                                <option value="">Todas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Especie</label>
                            <select class="form-select" id="filtro_especie">
                                <option value="">Todas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha Desde</label>
                            <input type="date" class="form-control" id="filtro_desde">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha Hasta</label>
                            <input type="date" class="form-control" id="filtro_hasta">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button class="btn btn-primary" onclick="buscarInstructivos()">🔍 Buscar</button>
                            <button class="btn btn-secondary" onclick="limpiarFiltros()">🧹 Limpiar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Lista de Instructivos -->
    <div class="row mb-4" id="lista_instructivos" style="display: none;">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">📑 Instructivos Encontrados</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Exportadora</th>
                                    <th>Especie</th>
                                    <th>Fecha</th>
                                    <th>Turno</th>
                                    <th>Versiones</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="tabla_instructivos_body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Formulario de Edición -->
    <div id="formulario_edicion" style="display: none;">
        <!-- Cabecera -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">✏️ Editando Instructivo #<span id="edit_id_instructivo"></span> - Versión <span id="edit_version"></span></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Exportadora *</label>
                                <select class="form-select" id="edit_exportadora" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Especie *</label>
                                <select class="form-select" id="edit_especie" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Fecha *</label>
                                <input type="date" class="form-control" id="edit_fecha" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Turno</label>
                                <select class="form-select" id="edit_turno">
                                    <option value="Día">Día</option>
                                    <option value="Tarde">Tarde</option>
                                    <option value="Noche">Noche</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Versión Actual</label>
                                <input type="text" class="form-control" id="edit_version_actual" readonly>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="form-label">Observación</label>
                                <textarea class="form-control" id="edit_observacion" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pedidos -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">📦 Pedidos</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="edit_numero_pedido" placeholder="Número de Pedido">
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control" id="edit_cantidad_pedido" placeholder="Cantidad Total">
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" id="edit_prioridad_pedido" placeholder="Prioridad" min="1">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-success" onclick="agregarPedido()">➕ Agregar</button>
                            </div>
                        </div>
                        <table class="table table-bordered" id="tabla_pedidos_edit">
                            <thead class="table-light">
                                <tr>
                                    <th>Pedido</th>
                                    <th>Cantidad</th>
                                    <th>Prioridad</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detalle -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📊 Detalle por Calibre</h5>
                        <button class="btn btn-sm btn-primary" onclick="abrirModalPantallaCompleta()">👁️ Ver en Pantalla Completa</button>
                    </div>
                    <div class="card-body">
                        <!-- Filtros dependientes -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Embalaje *</label>
                                <select class="form-select" id="edit_embalaje" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Categoría *</label>
                                <select class="form-select" id="edit_categoria" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">PLU *</label>
                                <select class="form-select" id="edit_plu" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Etiqueta</label>
                                <select class="form-select" id="edit_etiqueta">
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Calibres * (Ctrl+Click para múltiple)</label>
                                <select class="form-select" id="edit_calibres" multiple size="4" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Pallet</label>
                                <select class="form-select" id="edit_pallet">
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Altura Pallet</label>
                                <select class="form-select" id="edit_altura_pallet">
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Destino</label>
                                <select class="form-select" id="edit_destino">
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Variedad Etiqueta</label>
                                <input type="text" class="form-control" id="edit_variedad_etiqueta" placeholder="Ej: Golden">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Número Pedido *</label>
                                <select class="form-select" id="edit_numero_pedido_detalle" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Cantidad *</label>
                                <input type="number" class="form-control" id="edit_cantidad_detalle" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Observación</label>
                                <input type="text" class="form-control" id="edit_observacion_detalle" placeholder="Observación del detalle">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-success w-100" onclick="agregarDetalle()">➕ Agregar</button>
                            </div>
                        </div>
                        
                        <!-- Cinta de Calibres -->
                        <div id="cinta_calibres_edit" class="cinta-calibres" style="display: none;"></div>
                        
                        <table class="table table-bordered" id="tabla_detalle_edit">
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
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Botones de Acción -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <button class="btn btn-success btn-guardar me-3" onclick="guardarNuevaVersion()">
                    💾 Guardar como Versión <span id="btn_nueva_version"></span>
                </button>
                <button class="btn btn-secondary btn-guardar" onclick="cancelarEdicion()">
                    ❌ Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PANTALLA COMPLETA -->
<div class="modal fade" id="modalPantallaCompletaEdit" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">🔍 Vista Completa - Detalle de Calibres</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Cinta de calibres en modal -->
                <div id="cintaCalibresModalEdit" class="mb-4 p-3 bg-light rounded">
                    <strong class="d-block mb-2">📏 Todos los Calibres:</strong>
                    <div id="cintaCalibresModalContentEdit" style="display: flex; gap: 8px; flex-wrap: wrap;"></div>
                </div>
                
                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-0" id="statPedidosEdit">0</h3>
                                <small>📦 Pedidos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-0" id="statCalibresEdit">0</h3>
                                <small>📏 Calibres</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-0" id="statCajasEdit">0</h3>
                                <small>📈 Total Cajas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h3 class="mb-0" id="statDestinosEdit">0</h3>
                                <small>🌍 Destinos</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabla completa -->
                <table class="table table-bordered table-hover" id="tablaDetalleModalEdit">
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

<!-- MODAL EDITAR PEDIDO -->
<div class="modal fade" id="modalEditarPedido" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">✏️ Editar Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_pedido_index">
                <div class="mb-3">
                    <label class="form-label">Número de Pedido</label>
                    <input type="text" class="form-control" id="edit_pedido_numero">
                </div>
                <div class="mb-3">
                    <label class="form-label">Cantidad Total</label>
                    <input type="number" class="form-control" id="edit_pedido_cantidad">
                </div>
                <div class="mb-3">
                    <label class="form-label">Prioridad</label>
                    <input type="number" class="form-control" id="edit_pedido_prioridad" min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="guardarEdicionPedido()">💾 Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDITAR DETALLE -->
<div class="modal fade" id="modalEditarDetalle" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">✏️ Editar Detalle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_detalle_index">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Número Pedido *</label>
                        <select class="form-select" id="edit_detalle_numero_pedido">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Cantidad *</label>
                        <input type="number" class="form-control" id="edit_detalle_cantidad">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Variedad Etiqueta</label>
                        <input type="text" class="form-control" id="edit_detalle_variedad">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Observación</label>
                        <input type="text" class="form-control" id="edit_detalle_observacion">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Embalaje</label>
                        <select class="form-select" id="edit_detalle_embalaje">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" id="edit_detalle_categoria">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">PLU</label>
                        <select class="form-select" id="edit_detalle_plu">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Etiqueta</label>
                        <select class="form-select" id="edit_detalle_etiqueta">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Pallet</label>
                        <select class="form-select" id="edit_detalle_pallet">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Altura Pallet</label>
                        <select class="form-select" id="edit_detalle_altura">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Destino</label>
                        <select class="form-select" id="edit_detalle_destino">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Calibres (Ctrl+Click)</label>
                        <select class="form-select" id="edit_detalle_calibres" multiple size="3">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="guardarEdicionDetalle()">💾 Guardar</button>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/editar_instructivo.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
