<?php
$titulo_pagina = 'Copiar Instructivo';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Contenido principal -->
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

  <!-- Modal -->
  <div class="modal fade" id="modalDetalle" tabindex="-1" aria-labelledby="modalDetalleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="formDetalle">
          <div class="modal-header">
            <h5 class="modal-title" id="modalDetalleLabel">Detalle del Instructivo</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="nueva_fecha" class="form-label">Nueva Fecha</label>
              <input type="date" id="nueva_fecha" name="nueva_fecha" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="turno" class="form-label">Turno</label>
              <input type="text" id="turno" name="turno" class="form-control" required>
            </div>
            <div id="detalleInstructivo" class="border p-3 bg-light"></div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Guardar Copia</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Scripts -->

<?php
$scripts_extra = '<script src="../assets/js/copiar_instructivo.js"></script>';
require_once __DIR__ . '/../includes/footer.php';
?>
