<?php
$titulo_pagina = 'ConfiguraciÃ³n Altura Pallet';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-5">
    <h2 class="mb-4">GestiÃ³n de Altura Pallet</h2>
    
    <!-- Formulario -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Agregar/Editar Altura Pallet</h5>
            <form id="formAltura">
                <input type="hidden" id="id_altura_pallet" name="id_altura_pallet">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="buscadorEmbalaje" class="form-label">Embalaje</label>
                        <input type="text" class="form-control form-control-sm mb-1" id="buscadorEmbalaje" placeholder="Buscar embalaje...">
                        <select id="id_embalaje" class="form-select" name="id_embalaje" required>
                            <option value="">Cargando embalajes...</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="altura" class="form-label">Altura</label>
                        <input type="number" step="0.01" class="form-control" id="altura" name="altura" required>
                    </div>
                    <div class="col-md-4">
                        <label for="cajas" class="form-label">Cajas</label>
                        <input type="number" class="form-control" id="cajas" name="cajas" required>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
                    <button type="button" class="btn btn-warning" id="btnModificar">Modificar</button>
                    <button type="button" class="btn btn-danger" id="btnEliminar">Eliminar</button>
                    <button type="button" class="btn btn-secondary" id="btnLimpiar">Limpiar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Registros -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Alturas de Pallet Registradas</h5>
                <div class="d-flex align-items-center gap-2">
                    <input type="text" class="form-control form-control-sm" id="buscadorAlturas" placeholder="Buscar..." style="width:220px;">
                    <select class="form-select form-select-sm" id="porPaginaAlturas" style="width:90px;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <small class="text-muted text-nowrap">por página</small>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tablaAlturas">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Embalaje</th>
                            <th>Especie</th>
                            <th>Altura</th>
                            <th>Cajas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center">Cargando registros...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <small class="text-muted" id="infoAlturas"></small>
                <nav><ul class="pagination pagination-sm mb-0" id="paginacionAlturas"></ul></nav>
            </div>
        </div>
    </div>
</div>

<?php
$scripts_extra = '<script src="../assets/js/inst_altura_pallet.js"></script>';
require_once __DIR__ . '/../includes/footer.php';
?>

