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
    <title>GestiÃ³n de PLU</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">GestiÃ³n de PLU</h2>
        
        <form id="formPlu" class="mb-4">
            <input type="hidden" id="id_plu">
            <div class="row">
                <div class="col-md-3">
                    <label for="codigo_plu" class="form-label">CÃ³digo PLU</label>
                    <input type="text" class="form-control" id="codigo_plu" name="codigo_plu" required>
                </div>
                <div class="col-md-5">
                    <label for="nombre_plu" class="form-label">Nombre PLU</label>
                    <input type="text" class="form-control" id="nombre_plu" name="nombre_plu" required>
                </div>
                <div class="col-md-4">
                    <label for="especie" class="form-label">Especie</label>
                    <select class="form-select" id="especie" name="especie" required>
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

        <h4 class="mt-4">PLU Registrados</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tablaPlu">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>CÃ³digo</th>
                        <th>Nombre</th>
                        <th>Especie</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/plu.js"></script>
</body>
</html>

