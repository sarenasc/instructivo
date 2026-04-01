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
    <title>Agrupación de Calibres</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-1">Agrupación de Calibres</h2>
    <p class="text-muted mb-4">
        Define grupos de calibres (ej: XLA, XLB) para una especie, exportadora y categoría.
        Los calibres se eligen individualmente. Usa <strong>Ctrl+Click</strong> para selección múltiple.
    </p>

    <!-- Formulario -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Agregar / Editar Agrupación</h5>
            <form id="formAgrupacion">
                <input type="hidden" id="id_agrupacion">

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Especie <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_especie" required>
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Exportadora <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_exportadora" required>
                            <option value="">Seleccione especie primero</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_categoria" required>
                            <option value="">Seleccione especie y exportadora</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Nombre del grupo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre_grupo" placeholder="Ej: XLA" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label class="form-label">
                            Calibres <span class="text-danger">*</span>
                            <small class="text-muted">(Ctrl+Click para selección múltiple)</small>
                        </label>
                        <select class="form-select" id="calibres" multiple size="6" required>
                            <option value="">Seleccione especie primero</option>
                        </select>
                    </div>
                    <div class="col-md-7 mb-3 d-flex align-items-end">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-primary"   id="btnGuardar">Guardar</button>
                            <button type="button" class="btn btn-warning"   id="btnModificar">Modificar</button>
                            <button type="button" class="btn btn-danger"    id="btnEliminar">Eliminar</button>
                            <button type="button" class="btn btn-secondary" id="btnLimpiar">Limpiar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Agrupaciones configuradas</h5>
                <input type="text" class="form-control form-control-sm" id="buscador"
                       placeholder="Buscar..." style="width:220px;">
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tablaAgrupaciones">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Especie</th>
                            <th>Exportadora</th>
                            <th>Categoría</th>
                            <th>Grupo</th>
                            <th>Calibres</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="7" class="text-center">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/agrupacion_calibre.js"></script>
</body>
</html>
