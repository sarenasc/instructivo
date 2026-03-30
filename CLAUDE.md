# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Sistema de Instructivos Productivos** — AgroIndustrial Almahue (v2.0.0)
PHP web application running on XAMPP (Windows) with SQL Server backend. Manages production instructions (instructivos) with CRUD operations, versioning, and Excel export.

## Development Environment

- **URL local:** `http://localhost/instructivo/`
- **URL producción:** `http://192.168.19.4/instructivo/`
- **DB Server:** 192.168.19.4 — SQL Server con `sqlsrv_` PHP extension
- **Bases de datos:** `SistGestion` (principal), `Facturador_ASanta_Almahue`, `DW_Almahue`
- **Config:** Variables de entorno en `.env` (no commitear)

## Key Commands

### PHP / Composer
```bash
composer install          # Instalar dependencias PHP
php migrar_passwords.php  # Migrar contraseñas a bcrypt
```

### Node.js (API opcional, puerto 3003)
```bash
npm install
node app/server.js
```

### Python (API opcional, puerto 8000)
```bash
pip install -r requirements.txt
python app/api/main.py
```

### Scripts de generación (Windows)
```bat
actualizar_menu.bat        # Propaga cambios del menú a todas las páginas
generar_backend.bat        # Genera stubs CRUD en PHP
convertir_todo.bat         # Convierte HTML a plantillas PHP
limpiar_archivos.bat       # Limpia archivos temporales/backup
```

```bash
python generar_configuraciones.py   # Genera páginas de configuración
python generar_php_backend.py       # Genera controladores CRUD
python generar_js_config.py         # Genera objetos de configuración JS
```

## Architecture

### Request Flow
```
Index.php (login)
  → app/login.php          (POST credentials → session)
  → app/inicio.php         (dashboard, requiere sesión)
    → app/Procesos/        (flujos: crear, editar, copiar, exportar instructivo)
    → app/Configuracion/   (tablas maestras: calibre, pallet, embalaje, etc.)
```

### MVC Pattern (sin framework)
- **Views:** `app/Procesos/`, `app/Configuracion/` — HTML + PHP con includes
- **Controllers:** `app/controllers/procesar_*.php` — reciben POST/AJAX, ejecutan lógica
- **Models:** `app/models/obtener_*.php` — consultas SQL, devuelven JSON
- **Includes:** `app/includes/header.php`, `footer.php`, `menu.php`, `Validator.php`

### Configuration
- `config/app.php` — constantes globales (paths, URLs, sesión, logging)
- `config/database.php` — conexiones a las 3 BDs vía `sqlsrv_connect()`
- `config/auth.php` — funciones bcrypt (`hashPassword`, `verifyPassword`)
- `config/require_auth.php` — middleware de sesión (incluir al inicio de páginas protegidas)
- `bootstrap.php` — inicialización global: carga `.env`, define constantes, registra error handlers

### Helpers (`src/Helpers.php`)
Funciones globales disponibles tras `bootstrap.php`: `redirect()`, `jsonResponse()`, `sanitize()`, `writeLog()`, `isAjax()`, `post()`, `get()`, `formatSQLDate()`, `generateCSRFToken()`, `verifyCSRFToken()`.

### Logging
Logs diarios en `storage/logs/app_YYYY-MM-DD.log`. Niveles: INFO, ERROR, WARNING, DEBUG, CRITICAL. Rotación automática a 30 días.

### PHP Dependencies (Composer)
- `phpoffice/phpspreadsheet` — exportación a Excel
- `vlucas/phpdotenv` — carga de `.env`

## Database Tables (SistGestion)

Principales tablas del módulo:
- `TRA_usuario` — usuarios del sistema
- `inst_cab_instructivo` — cabecera del instructivo
- `inst_pedido_instructivo` — pedidos asociados
- `inst_detalle_instructivo` — detalle por calibre

## Security Notes

- **Contraseñas:** actualmente en texto plano en BD. Ejecutar `migrar_passwords.php` para migrar a bcrypt. Las funciones ya están en `config/auth.php`.
- **CSRF:** tokens disponibles vía `generateCSRFToken()` / `verifyCSRFToken()`.
- **Consultas SQL:** preferir parámetros con `sqlsrv_query($conn, $sql, $params)` en lugar de concatenación.
- **HTTPS:** el flag `secure` de cookies está en 0; activar en `config/app.php` si se habilita HTTPS.

## Adding New Configuration Pages

1. Crear `app/Configuracion/nueva_entidad.php` (view)
2. Crear `app/models/obtener_nueva_entidad.php` (SELECT → JSON)
3. Crear `app/controllers/procesar_nueva_entidad.php` (INSERT/UPDATE/DELETE)
4. Agregar enlace en `app/includes/menu.php`
5. Ejecutar `actualizar_menu.bat` si el menú está replicado en otras páginas
