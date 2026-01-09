<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
   
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Instructivo -- AgroIndustrial Almahue</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="procesosDropdown" role="button" data-bs-toggle="dropdown">
                            Proceso
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../app/Procesos/instructivo.html">Creacion de Instructivo</a></li>
                            <li><a class="dropdown-item" href="../app/Procesos/Pedidos.html">Agregar Pedidos</a></li>
                            <li><a class="dropdown-item" href="../app/Procesos/exportar_instructivo.html">Descargar Instructivo</a></li>
                            <li><a class="dropdown-item" href="../app/Procesos/copiar_instructivo.html">Copiar Instructivo</a></li>
                            <li><a class="dropdown-item" href="../app/Procesos/mostrar_instructivo.html">Desplegar informacion en Pantall</a></li>
                            
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="configDropdown" role="button" data-bs-toggle="dropdown">
                            Configuración
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../app/Configuracion/calibre.html">Calibre</a></li>
                            <li><a class="dropdown-item" href="../app/Configuracion/categoria.html">Categoría</a></li>
                            <li><a class="dropdown-item" href="../app/Configuracion/embalaje.html">Embalaje</a></li>
                            <li><a class="dropdown-item" href="../app/Configuracion/etiqueta.html">Etiqueta</a></li>
                            <li><a class="dropdown-item" href="../app/Configuracion/pallet.html">Pallet</a></li>
                            <li><a class="dropdown-item" href="../app/Configuracion/plu.html">PLU</a></li>
                            <li><a class="dropdown-item" href="../app/Configuracion/exportadora.html">Exportadora</a></li>
                            <li><a class="dropdown-item" href="../app/Configuracion/destino.html">Destino</a></li>
                            <li><a class="dropdown-item" href="../app/Configuracion/inst_altura_pallet.html">Altura Pallet</a></li>
                            <li><a class="dropdown-item" href="../app/Configuracion/edicion_config.html">Edicion de Configuracion</a></li>
                            <li><a class="dropdown-item" href="../app/logout.php">Salir</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="page-header text-center mt-5">
        <h1>Inicio</h1>
        <p>Bienvenido al sistema</p>
    </header>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10 text-center">
                <h2 class="estadisticas-title"></h2>
                <img src="../app/image/Frutas y Numeros.png" alt="Frutas con estadística" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/scripts.js"></script>
</body>
</html>
