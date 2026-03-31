<?php
$titulo_pagina = 'Gestion de Embalajes';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-5">
    <h2 class="mb-4">Gestion de Embalajes</h2>
    
    <!-- Formulario -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Agregar/Editar Embalaje</h5>
            <form id="formEmbalaje">
                <input type="hidden" id="id_embalaje" name="id_embalaje">
                <div class="mb-3">
                    <label for="codigo_embalaje" class="form-label">Codigo Embalaje</label>
                    <input type="text" class="form-control" id="codigo_embalaje" name="codigo_embalaje" required>
                </div>
                <div class="mb-3">
                    <label for="nombre_embalaje" class="form-label">Descripcion Embalaje</label>
                    <input type="text" class="form-control" id="nombre_embalaje" name="nombre_embalaje" required>
                </div>
                <div class="mb-3">
                    <label for="peso_embalaje" class="form-label">Peso Embalaje</label>
                    <input type="text" class="form-control" id="peso_embalaje" name="peso_embalaje" required>
                </div>
                <div class="mb-3">
                    <label for="etiqueta" class="form-label">Etiqueta</label>
                    <select class="form-control" id="etiqueta" name="etiqueta" required>
                        <option value="">Cargando etiquetas...</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="especie" class="form-label">Especie</label>
                    <select class="form-control" id="especie" name="especie" required>
                        <option value="">Cargando especies...</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="exportadora" class="form-label">Exportadora</label>
                    <select class="form-control" id="exportadora" name="exportadora" required>
                        <option value="">Cargando exportadora...</option>
                    </select>
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
                <h5 class="card-title mb-0">Registros Existentes</h5>
                <div class="d-flex align-items-center gap-2">
                    <input type="text" class="form-control form-control-sm" id="buscadorEmbalaje" placeholder="Buscar..." style="width:220px;">
                    <select class="form-select form-select-sm" id="porPaginaEmbalaje" style="width:90px;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <small class="text-muted text-nowrap">por página</small>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tablaEmbalaje">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Codigo</th>
                            <th>Descripcion</th>
                            <th>Peso</th>
                            <th>Etiqueta</th>
                            <th>Especie</th>
                            <th>Exportadora</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="text-center">Cargando registros...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <small class="text-muted" id="infoEmbalaje"></small>
                <nav><ul class="pagination pagination-sm mb-0" id="paginacionEmbalaje"></ul></nav>
            </div>
        </div>
    </div>
</div>

<?php
$scripts_extra = '<script src="../assets/js/embalaje.js"></script>';
require_once __DIR__ . '/../includes/footer.php';
?>

