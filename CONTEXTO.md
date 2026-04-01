# Contexto del Proyecto — Sistema de Instructivos Productivos

## Stack y entorno
- **PHP** sin framework, XAMPP Windows, extensión `sqlsrv_`
- **BD:** SQL Server en `192.168.19.4` — base principal `SistGestion`
- **URL local:** `http://localhost/instructivo/`
- **Bootstrap:** `bootstrap.php` → carga `.env`, constantes, error handlers

## Estructura de carpetas relevantes
```
app/
  Procesos/         → vistas PHP (crear, editar, copiar, exportar instructivo)
  Configuracion/    → tablas maestras (calibre, embalaje, categoría, etc.)
  controllers/      → lógica POST/AJAX (procesar_*.php, guardar_*.php)
  models/           → queries SELECT → JSON (obtener_*.php)
  services/         → APIs ligeras (api_exportadoras.php, api_especies.php, etc.)
  includes/         → header.php, footer.php, menu.php
  assets/js/        → JS por módulo (editar_instructivo.js, crear_instructivo.js, etc.)
  conexion.php      → conexión sqlsrv al servidor
config/
  app.php           → constantes globales
  database.php      → 3 conexiones BD
  auth.php          → bcrypt helpers
  require_auth.php  → middleware sesión
src/Helpers.php     → helpers globales (redirect, jsonResponse, sanitize, writeLog…)
storage/logs/       → logs diarios app_YYYY-MM-DD.log
```

## Tablas principales (SistGestion)
| Tabla | Descripción |
|---|---|
| `inst_cab_instructivo` | Cabecera del instructivo. PK IDENTITY = `id_instructivo`. Una fila por instructivo. Campos: `id_exportadora`, `id_especie`, `fecha`, `turno`, `observacion` |
| `inst_detalle_instructivo` | Detalle por calibre. FK `id_cab_instructivo` → `inst_cab_instructivo.id_instructivo`. Tiene campo `version` para historial |
| `inst_pedidos` | Pedidos asociados. FK `id_instructivo`. Tiene campo `version` |
| `TRA_usuario` | Usuarios del sistema |
| `inst_calibre`, `inst_embalaje`, `inst_categoria`, `inst_plu`, `inst_etiqueta`, `inst_pallet`, `inst_altura_pallet`, `inst_destino` | Tablas maestras de configuración |
| `inst_exportadora`, `especie` | Catálogos de exportadoras y especies |

## Flujo de edición de instructivo

### Vista: `app/Procesos/editar_instructivo.php`
- Carga JS: `app/assets/js/editar_instructivo.js`
- Busca instructivos vía `app/models/obtener_instructivos.php`
- Carga el instructivo completo vía `app/models/obtener_instructivo_para_edicion.php`

### Guardado: `app/controllers/guardar_nueva_version.php`
- Recibe JSON: `{ id_instructivo, cabecera, pedidos, detalle }`
- **Calcula `nueva_version = MAX(version) + 1` desde la BD** (no confía en el cliente)
- Hace `UPDATE inst_cab_instructivo` con los nuevos datos de cabecera
- Inserta nuevos pedidos en `inst_pedidos` con la nueva versión
- Inserta nuevo detalle en `inst_detalle_instructivo` con la nueva versión
- Todo en una transacción con rollback en error

### IMPORTANTE sobre `inst_cab_instructivo`
- `id_instructivo` es PK IDENTITY → **no se puede INSERT con valor explícito**
- Cada instructivo tiene exactamente UNA fila en esta tabla
- Para editar cabecera = hacer `UPDATE`, nunca `INSERT`

## Flujo de creación: `app/controllers/guardar_instructivo_completo.php`
1. INSERT `inst_cab_instructivo` (sin especificar `id_instructivo`) → obtiene PK con `SCOPE_IDENTITY()`
2. INSERT `inst_pedidos` version=1
3. INSERT `inst_detalle_instructivo` version=1

## Modelo de versiones
- La cabecera no versiona (una sola fila, se UPDATE)
- Los pedidos y el detalle versionan (nueva fila por versión)
- Para leer la versión más reciente: `MAX(version)` en `inst_detalle_instructivo` o `inst_pedidos`

## Notas de seguridad
- Passwords en texto plano en BD → ejecutar `migrar_passwords.php`
- Preferir parámetros en `sqlsrv_query($conn, $sql, $params)` sobre concatenación
- CSRF tokens disponibles en `src/Helpers.php`
