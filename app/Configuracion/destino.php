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
    <title>Gestion de Destinos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Gestion de Destinos</h2>
        
        <form id="formDestino" class="mb-4">
            <input type="hidden" id="id_destino" name="id_destino">
            <div class="row">
                <div class="col-md-4">
                    <label for="codigo_destino" class="form-label">Codigo Destino</label>
                    <input type="text" class="form-control" id="codigo_destino" name="codigo_destino" required>
                </div>
                <div class="col-md-6">
                    <label for="nombre_destino" class="form-label">Nombre Destino</label>
                    <input type="text" class="form-control" id="nombre_destino" name="nombre_destino" required>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
                <button type="button" class="btn btn-warning" id="btnModificar">Modificar</button>
                <button type="button" class="btn btn-danger" id="btnEliminar">Eliminar</button>
                <button type="button" class="btn btn-secondary" id="btnLimpiar">Limpiar</button>
            </div>
        </form>

        <h4 class="mt-4">Destinos Registrados</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tablaDestino">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/destino.js"></script>
</body>
</html>

