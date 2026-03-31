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
    <title>Gestion de Etiquetas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Gestion de Etiquetas</h2>
        
        <form id="formEtiqueta" class="mb-4">
            <input type="hidden" id="id_etiqueta" name="id_etiqueta">
            <div class="row">
                <div class="col-md-3">
                    <label for="codigo_etiqueta" class="form-label">Codigo Etiqueta</label>
                    <input type="text" class="form-control" id="codigo_etiqueta" name="codigo_etiqueta" required>
                </div>
                <div class="col-md-5">
                    <label for="nombre_etiqueta" class="form-label">Nombre Etiqueta</label>
                    <input type="text" class="form-control" id="nombre_etiqueta" name="nombre_etiqueta" required>
                </div>
                <div class="col-md-4">
                    <label for="exportadora" class="form-label">Exportadora</label>
                    <select class="form-select" id="exportadora" name="exportadora" required>
                        <option value="">Seleccione...</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
                <button type="button" class="btn btn-warning" id="btnModificar">Modificar</button>
                <button type="button" class="btn btn-danger" id="btnEliminar">Eliminar</button>
                <button type="button" class="btn btn-secondary" id="btnLimpiar">Limpiar</button>
            </div>
        </form>

        <div class="card mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Etiquetas Registradas</h5>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="form-control form-control-sm" id="buscadorEtiqueta" placeholder="Buscar..." style="width:220px;">
                        <select class="form-select form-select-sm" id="porPaginaEtiqueta" style="width:90px;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <small class="text-muted text-nowrap">por página</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tablaEtiqueta">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>Exportadora</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted" id="infoEtiqueta"></small>
                    <nav><ul class="pagination pagination-sm mb-0" id="paginacionEtiqueta"></ul></nav>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
<script src="../assets/js/etiqueta.js"></script>

