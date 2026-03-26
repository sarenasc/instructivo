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
    <title>Gestión de Calibres</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Gestión de Calibres</h2>
    
    <!-- Formulario -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Agregar/Editar Calibre</h5>
            <form id="formCalibre">
                <input type="hidden" id="id_calibre" name="id_calibre">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="codigo_calibre" class="form-label">Código Calibre</label>
                        <input type="text" class="form-control" id="codigo_calibre" name="codigo_calibre" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="nombre_calibre" class="form-label">Nombre Calibre</label>
                        <input type="text" class="form-control" id="nombre_calibre" name="nombre_calibre" required>
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
            <h5 class="card-title">Registros Existentes</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tablaCalibres">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Especie</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center">Cargando registros...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/calibre.js"></script>
