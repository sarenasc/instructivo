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
    <title>Gestion de CategorÃ­as</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Gestion de Categori­as</h2>
        
        <form id="formCategoria" class="mb-4">
            <input type="hidden" id="id_categoria">
            <div class="row">
                <div class="col-md-3">
                    <label for="codigo_categoria" class="form-label">Codigo CategorÃ­a</label>
                    <input type="text" class="form-control" id="codigo_categoria" name="codigo_categoria" required>
                </div>
                <div class="col-md-4">
                    <label for="nombre_categoria" class="form-label">Nombre Categori­a</label>
                    <input type="text" class="form-control" id="nombre_categoria" name="nombre_categoria" required>
                </div>
                <div class="col-md-3">
                    <label for="especie_categoria" class="form-label">Especie</label>
                    <select class="form-select" id="especie_categoria" name="especie_categoria" required>
                        <option value="">Seleccione...</option>
                    </select>
                </div>
                <div class="col-md-2">
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

        <h4 class="mt-4">CategorÃ­as Registradas</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tablaCategoria">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Especie</th>
                        <th>Exportadora</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
<script src="../assets/js/categoria.js"></script>

