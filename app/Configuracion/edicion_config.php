<?php
$titulo_pagina = 'GestiÃ³n de Configuraciones';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="tablaConfig" class="form-label">Selecciona una tabla:</label>
            <select id="tablaConfig" class="form-select">
                <option value="">-- Selecciona --</option>
                <option value="especies">Especie</option>
                <option value="calibre">Calibre</option>
                <option value="categoria">CategorÃ­a</option>
                <option value="destino">Destino</option>
                <option value="embalaje">Embalaje</option>
                <option value="etiqueta">Etiqueta</option>
                <option value="exportadora">Exportadora</option>
                <option value="altura_pallet">Altura Pallet</option>
                <option value="pallet">Pallet</option>
                <option value="plu">PLU</option>
            </select>
        </div>
    </div>

    <!-- Modal de Edicion -->
    <div class="modal fade" id="modalEdicion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEdicion">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Registro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Se rellena dinÃ¡micamente -->
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Guardar cambios</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="tablaResultados" class="table-responsive"></div>
    <div id="paginacion" class="mt-3"></div>
</div>

<?php
$scripts_extra = '
<script src="../assets/js/edicion.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
';
require_once __DIR__ . '/../includes/footer.php';
?>

