<?php
$titulo_pagina = 'Agregar Pedidos';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">Instructivo de Proceso</h2>

    <!-- Selector de instructivo existente -->
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="selectInstructivo" class="form-label">Instructivo existente</label>
            <select id="selectInstructivo" class="form-select"></select>
        </div>
        <div class="col-md-6">
            <label for="selectVersion" class="form-label">Versión</label>
            <select id="selectVersion" class="form-select"></select>
        </div>
    </div>
</div>

<!-- MODAL: Visualizar y Editar Versión -->
<div class="modal fade" id="modalEditarVersion" tabindex="-1" aria-labelledby="modalEditarVersionLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEditarVersionLabel">Editar Versión de Instructivo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <!-- Cabecera (Solo lectura) -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Exportadora</label>
                        <input type="text" id="readonlyExportadora" class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Especie</label>
                        <input type="text" id="readonlyEspecie" class="form-control" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Turno</label>
                        <input type="text" id="readonlyTurno" class="form-control" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Versión</label>
                        <input type="text" id="readonlyVersion" class="form-control" readonly>
                    </div>
                    <div class="col-md-12 mt-2">
                        <label class="form-label">Observación</label>
                        <textarea id="readonlyObservacion" class="form-control" rows="2" readonly></textarea>
                    </div>
                </div>

                <!-- Ingreso de Pedido -->
                <h5 class="mt-4">Agregar Pedido</h5>
                <div class="row g-3 align-items-end mb-4">
                    <div class="col-md-6">
                        <label for="selectNumeroPedido" class="form-label">Número de Pedido</label>
                        <select id="selectNumeroPedido" class="form-select"></select>
                    </div>
                    <div class="col-md-4">
                        <label for="inputCantidad" class="form-label">Cantidad</label>
                        <input type="number" id="inputCantidad" class="form-control" min="1" placeholder="Ingrese cantidad">
                    </div>
                    <div class="col-md-4">
                        <label for="inputPrioridad" class="form-label">Prioridad</label>
                        <input type="number" id="inputPrioridad" class="form-control" min="1" placeholder="Ingrese Prioridad">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" id="btnAgregarPedido">Agregar</button>
                    </div>
                </div>

                <!-- Lista de Pedidos Insertados -->
                <h6>Pedidos Añadidos</h6>
                <table class="table table-bordered" id="tablaPedidos">
                    <thead class="table-light">
                        <tr>
                            <th>N° Pedido</th>
                            <th>Cantidad</th>
                            <th>Prioridad</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se agregarán los pedidos dinámicamente -->
                    </tbody>
                </table>

                <div class="d-flex justify-content-end">
                    <button class="btn btn-success" id="btnGuardarPedidos">Guardar Pedidos</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$scripts_extra = '
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../assets/js/detalle_instructivo.js"></script>
<script src="../assets/js/modal_pedidos.js"></script>
<script src="../assets/js/instructivo_selector.js"></script>
';
require_once __DIR__ . '/../includes/footer.php';
?>
