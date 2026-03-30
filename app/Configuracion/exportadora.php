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
    <title>GestiÃ³n de Exportadoras</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">GestiÃ³n de Exportadoras</h2>
        
        <form id="formExportadora" class="mb-4">
            <input type="hidden" id="id_exportadora">
            <div class="row">
                <div class="col-md-4">
                    <label for="cod_exportadora" class="form-label">CÃ³digo Exportadora</label>
                    <input type="text" class="form-control" id="cod_exportadora" name="cod_exportadora" required>
                </div>
                <div class="col-md-6">
                    <label for="nombre_exportadora" class="form-label">Nombre Exportadora</label>
                    <input type="text" class="form-control" id="nombre_exportadora" name="nombre_exportadora" required>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
                <button type="button" class="btn btn-warning" id="btnModificar">Modificar</button>
                <button type="button" class="btn btn-danger" id="btnEliminar">Eliminar</button>
                <button type="button" class="btn btn-secondary" id="btnLimpiar">Limpiar</button>
            </div>
        </form>

        <h4 class="mt-4">Exportadoras Registradas</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tablaExportadora">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>CÃ³digo</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/exportadora.js"></script>
</body>
</html>

