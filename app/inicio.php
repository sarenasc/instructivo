<?php
$titulo_pagina = 'Inicio - Instructivos';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1>Inicio</h1>
            <p class="text-muted">Bienvenido al sistema de Instructivos Productivos</p>
            <p class="text-muted">Usuario: <strong><?= htmlspecialchars($_SESSION['Nombre'] ?? $_SESSION['Nom_Usuario']) ?></strong></p>
        </div>
    </div>

    <!-- Imagen y Accesos rápidos en la misma fila -->
    <div class="row align-items-center">
        <!-- Imagen más pequeña -->
        <div class="col-md-6 text-center">
            <img src="image/Frutas y Numeros.png" alt="Frutas con estadística" class="img-fluid rounded shadow" style="max-height: 400px; width: auto;">
        </div>

        <!-- Accesos rápidos al costado -->
        <div class="col-md-6">
            <h3 class="mb-4">Accesos Rápidos</h3>
            
            <div class="d-grid gap-3">
                <!-- Crear Instructivo -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <span style="font-size: 2.5rem;">📋</span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-1">Crear Instructivo</h5>
                                <p class="card-text text-muted mb-2">Crea nuevos instructivos de proceso productivo</p>
                                <a href="Procesos/instructivo.php" class="btn btn-primary btn-sm">Ir a Crear</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exportar -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <span style="font-size: 2.5rem;">📊</span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-1">Exportar</h5>
                                <p class="card-text text-muted mb-2">Descarga instructivos en formato Excel</p>
                                <a href="Procesos/exportar_instructivo.php" class="btn btn-success btn-sm">Descargar</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <span style="font-size: 2.5rem;">⚙️</span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-1">Configuración</h5>
                                <p class="card-text text-muted mb-2">Gestiona parámetros del sistema</p>
                                <a href="Configuracion/calibre.php" class="btn btn-secondary btn-sm">Configurar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
