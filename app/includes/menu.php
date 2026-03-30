<!-- Menú de Navegación -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/instructivo/app/inicio.php">Instructivo -- AgroIndustrial Almahue</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/instructivo/app/inicio.php">Inicio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="procesosDropdown" role="button" data-bs-toggle="dropdown">
                        Proceso
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/instructivo/app/Procesos/crear_instructivo.php">Crear Instructivo</a></li>
                        <li><a class="dropdown-item" href="/instructivo/app/Procesos/editar_instructivo.php">Editar Instructivo</a></li>
                        <li><a class="dropdown-item" href="/instructivo/app/Procesos/exportar_instructivo.php">Exportar Instructivo</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/instructivo/app/Procesos/Pedidos.php">Agregar Pedidos</a></li>
                        <li><a class="dropdown-item" href="/instructivo/app/Procesos/copiar_instructivo.php">Copiar Instructivo</a></li>
                        <li><a class="dropdown-item" href="/instructivo/app/Procesos/mostrar_instructivo.php">Desplegar Información</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="configDropdown" role="button" data-bs-toggle="dropdown">
                        Configuración
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/instructivo/app/Configuracion/calibre.php">Calibre</a></li>
                        <li><a class="dropdown-item" href="/instructivo/app/Configuracion/categoria.php">Categoría</a></li>
                        <li><a class="dropdown-item" href="/instructivo/app/Configuracion/embalaje.php">Embalaje</a></li>
                        <li><a class="dropdown-item" href="/instructivo/app/Configuracion/etiqueta.php">Etiqueta</a></li>
                        <li><a class="dropdown-item" href="/instructivo/app/Configuracion/pallet.php">Pallet</a></li>
                        <li><a class="dropdown-item" href="/instructivo/app/Configuracion/plu.php">PLU</a></li>
                        <li><a class="dropdown-item" href="/instructivo/app/Configuracion/exportadora.php">Exportadora</a></li>
                        <li><a class="dropdown-item" href="/instructivo/app/Configuracion/destino.php">Destino</a></li>
                        <li><a class="dropdown-item" href="/instructivo/app/Configuracion/inst_altura_pallet.php">Altura Pallet</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/instructivo/app/Configuracion/edicion_config.php">Edición de Configuración</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/instructivo/app/logout.php">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
