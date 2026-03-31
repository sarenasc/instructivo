<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Calibres</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Gestion de Calibres</h2>
    
    <!-- Formulario -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Agregar/Editar Calibre</h5>
            <form id="formCalibre">
                <input type="hidden" id="id_calibre" name="id_calibre">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="codigo_calibre" class="form-label">Codigo Calibre</label>
                        <input type="text" class="form-control" id="codigo_calibre" name="codigo_calibre" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="nombre_calibre" class="form-label">Nombre Calibre</label>
                        <input type="text" class="form-control" id="nombre_calibre" name="nombre_calibre" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="orden" class="form-label">Orden</label>
                        <input type="number" class="form-control" id="orden" name="orden">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="especie" class="form-label">Especie</label>
                        <select class="form-control" id="especie" name="especie" required>
                            <option value="">Cargando especies...</option>
                        </select>
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
                <h5 class="card-title mb-0">Registros Existentes</h5>
                <div class="d-flex align-items-center gap-2">
                    <input type="text" class="form-control form-control-sm" id="buscadorCalibre" placeholder="Buscar..." style="width:220px;">
                    <select class="form-select form-select-sm" id="porPaginaCalibre" style="width:90px;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <small class="text-muted text-nowrap">por página</small>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tablaCalibres">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Codigo</th>
                            <th>Nombre</th>
                            <th>Orden</th>
                            <th>Especie</th>
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
                <small class="text-muted" id="infoCalibre"></small>
                <nav><ul class="pagination pagination-sm mb-0" id="paginacionCalibre"></ul></nav>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/calibre.js"></script>

