<?php
$titulo_pagina = 'Copiar Instructivo';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-4">
  <div class="card">
    <div class="card-header">
      <h5>Copiar Instructivo</h5>
    </div>
    <div class="card-body">
      <form id="formCopiar">
        <div class="mb-3">
          <label for="exportadora" class="form-label">Exportadora</label>
          <select id="exportadora" name="exportadora" class="form-select">
            <option value="">Seleccione una exportadora</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="instructivo" class="form-label">Instructivo</label>
          <select id="instructivo" name="instructivo" class="form-select" disabled>
            <option value="">Seleccione un instructivo</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="version" class="form-label">Versión</label>
          <select id="version" name="version" class="form-select" disabled>
            <option value="">Seleccione una versión</option>
          </select>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal detalle editable -->
<div class="modal fade" id="modalDetalle" tabindex="-1" aria-labelledby="modalDetalleLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form id="formDetalle">
        <div class="modal-header">
          <h5 class="modal-title" id="modalDetalleLabel">Editar y Copiar Instructivo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">

          <!-- Nueva fecha y turno -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="nueva_fecha" class="form-label">Nueva Fecha</label>
              <input type="date" id="nueva_fecha" name="nueva_fecha" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label for="turno" class="form-label">Turno</label>
              <input type="text" id="turno" name="turno" class="form-control" required>
            </div>
          </div>

          <!-- Gestión de pedidos -->
          <div class="card mb-3">
            <div class="card-header fw-semibold">Gestión de Pedidos</div>
            <div class="card-body">
              <div id="listaPedidos" class="mb-3 d-flex flex-wrap gap-2"></div>

              <div class="row g-2 align-items-end">
                <!-- Quitar pedido -->
                <div class="col-auto">
                  <label class="form-label mb-1">Quitar pedido</label>
                  <div class="d-flex gap-2">
                    <select id="selectQuitarPedido" class="form-select form-select-sm" style="min-width:160px">
                      <option value="">Seleccione...</option>
                    </select>
                    <button type="button" class="btn btn-sm btn-danger" id="btnQuitarPedido">Quitar</button>
                  </div>
                </div>

                <div class="col-auto d-flex align-items-end pb-1 text-muted">|</div>

                <!-- Agregar pedido -->
                <div class="col-auto">
                  <label class="form-label mb-1">Agregar pedido (basado en)</label>
                  <div class="d-flex gap-2">
                    <input type="text" id="nuevoPedidoNum" class="form-control form-control-sm" placeholder="Nº nuevo pedido" style="width:140px">
                    <select id="basarseEnPedido" class="form-select form-select-sm" style="min-width:160px">
                      <option value="">Pedido base...</option>
                    </select>
                    <button type="button" class="btn btn-sm btn-success" id="btnAgregarPedido">+ Agregar</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Tabla de detalle editable -->
          <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle" id="tablaDetalleEditable">
              <thead class="table-dark">
                <tr>
                  <th>Pedido</th>
                  <th>Embalaje</th>
                  <th>Calibres</th>
                  <th>PLU</th>
                  <th>Etiqueta</th>
                  <th>Categoría</th>
                  <th>Altura</th>
                  <th>Destino</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="tbodyDetalle"></tbody>
            </table>
          </div>

        </div>
        <div class="modal-footer">
          <span id="contadorFilas" class="me-auto text-muted small"></span>
          <button type="submit" class="btn btn-primary">Guardar Copia</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
$scripts_extra = '<script src="../assets/js/copiar_instructivo.js"></script>';
require_once __DIR__ . '/../includes/footer.php';
?>
