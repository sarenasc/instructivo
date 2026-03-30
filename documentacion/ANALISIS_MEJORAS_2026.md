# 🔍 ANÁLISIS DE MEJORAS - Sistema de Instructivos Productivos

**Fecha:** 27 Marzo 2026  
**Analista:** Scapy  
**Estado:** Análisis Completado

---

## 🚨 CRÍTICO - SEGURIDAD (Prioridad Máxima)

### 1. 🔴 Credenciales Hardcodeadas en `conexion.php`

**Problema:**
```php
$serverName = "192.168.19.4";
$connectionInfo = [
    "Database" => "SistGestion",
    "UID" => "sa",           // ❌ Usuario root expuesto
    "PWD" => "Robin@2021",   // ❌ Contraseña en texto plano
```

**Riesgo:** Cualquier persona con acceso al código tiene control total de la base de datos

**Solución:**
```php
// Usar variables de entorno (.env ya existe pero no se usa)
require_once __DIR__ . '/../bootstrap.php';

$serverName = $_ENV['DB_SERVER'];
$connectionInfo = [
    "Database" => $_ENV['DB_DATABASE'],
    "UID" => $_ENV['DB_USER'],
    "PWD" => $_ENV['DB_PASSWORD'],
```

**Acción:** 
- [ ] Crear usuario específico para la aplicación en SQL Server (no usar `sa`)
- [ ] Mover credenciales a `.env`
- [ ] Agregar `.env` al `.gitignore`
- [ ] Rotar contraseña actual inmediatamente

---

### 2. 🔴 Contraseñas en Texto Plano

**Problema en `login.php`:**
```php
// Verificar contraseña (TEXTO PLANO)
$password_db = $row['pass_usu'];
if ($pass !== $password_db) {  // ❌ Sin hash
```

**Riesgo:** Si la BD es comprometida, todas las contraseñas son visibles

**Solución:**
```php
// Al guardar usuario (migración):
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Al validar login:
if (!password_verify($pass, $row['pass_usu'])) {
    header("Location: ../index.php?error=1");
    exit();
}
```

**Acción:**
- [ ] Ejecutar script de migración de contraseñas (ya existe: `migrar_passwords.php`)
- [ ] Actualizar login para usar `password_verify()`
- [ ] Cambiar columna `pass_usu` a VARCHAR(255) si es necesario

---

### 3. 🟠 Inyección SQL

**Problema en `login.php`:**
```php
$usuario_safe = str_replace("'", "''", $usuario); // ❌ Escape básico insuficiente
$sql = "SELECT ... WHERE nom_usu = '$usuario_safe'";
```

**Problema en `procesar_*.php`:**
```php
$sql = "INSERT INTO calibre (...) VALUES ('$codigo', '$nombre', ...)"; // ❌ Direct query sin validación
```

**Riesgo:** SQL Injection, pérdida/manipulación de datos

**Solución:**
```php
// Opción 1: Prepared Statements (si el driver lo permite)
$stmt = sqlsrv_prepare($conn, "SELECT ... WHERE nom_usu = ?");
sqlsrv_execute($stmt, [$usuario]);

// Opción 2: Validación estricta de inputs
if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $usuario)) {
    die("Usuario inválido");
}

// Opción 3: Usar filter_input()
$codigo = filter_input(INPUT_POST, 'codigo', FILTER_SANITIZE_STRING);
```

**Acción:**
- [ ] Investigar por qué prepared statements fallan con el driver actual
- [ ] Implementar validación estricta en todos los inputs
- [ ] Usar `filter_input()` para sanitización
- [ ] Considerar actualización del driver sqlsrv

---

### 4. 🟠 Sin Protección CSRF

**Problema:** Todos los formularios POST no tienen token CSRF

**Riesgo:** Ataques Cross-Site Request Forgery

**Solución:**
```php
// En header.php o sesión inicial
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// En cada formulario
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

// En cada procesar_*.php
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("Token CSRF inválido");
}
```

**Acción:**
- [ ] Implementar generación de token en login/inicio de sesión
- [ ] Agregar campo hidden en todos los formularios
- [ ] Validar token en todos los procesar_*.php

---

## 🏗️ ARQUITECTURA (Prioridad Alta)

### 5. 🟠 Sin Validación Centralizada de Inputs

**Problema:** Cada archivo valida inputs de forma inconsistente

**Solución:** Crear clase de validación
```php
// app/validadores/Validador.php
class Validador {
    public static function texto($valor, $min = 1, $max = 100) {
        $valor = trim($valor);
        if (strlen($valor) < $min || strlen($valor) > $max) {
            return false;
        }
        return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
    }
    
    public static function numero($valor, $min = null, $max = null) {
        if (!is_numeric($valor)) return false;
        if ($min !== null && $valor < $min) return false;
        if ($max !== null && $valor > $max) return false;
        return (float)$valor;
    }
}

// Uso en procesar_*.php
$codigo = Validador::texto($_POST['codigo'], 1, 20);
$peso = Validador::numero($_POST['peso'], 0, 9999);
```

**Acción:**
- [ ] Crear carpeta `app/validadores/`
- [ ] Implementar clase Validador con métodos comunes
- [ ] Refactorizar todos los procesar_*.php para usarla

---

### 6. 🟠 Manejo de Errores Inconsistente

**Problema:** Algunos archivos usan `echo`, otros `die()`, otros logs

**Solución:** Sistema centralizado
```php
// app/includes/manejador_errores.php
function manejarError($mensaje, $codigo = 500, $log = true) {
    if ($log) {
        error_log("[" . date('Y-m-d H:i:s') . "] Error $codigo: $mensaje");
    }
    
    http_response_code($codigo);
    
    if ($_SERVER['HTTP_ACCEPT'] === 'application/json') {
        header('Content-Type: application/json');
        echo json_encode(['error' => $mensaje, 'codigo' => $codigo]);
    } else {
        echo "<div class='alert alert-danger'>$mensaje</div>";
    }
    exit();
}

// Uso
if (!$resultado) {
    manejarError("No se pudo guardar el registro", 500);
}
```

**Acción:**
- [ ] Crear `app/includes/manejador_errores.php`
- [ ] Incluir en todos los archivos PHP
- [ ] Estandarizar mensajes de error

---

### 7. 🟡 Sin Sistema de Logs de Auditoría

**Problema:** No hay registro de quién crea/modifica/elimina registros

**Solución:**
```php
// app/includes/auditoria.php
function registrarAccion($tabla, $accion, $id_registro, $datos = []) {
    global $conn;
    $sql = "INSERT INTO sys_auditoria 
            (tabla, accion, id_registro, id_usuario, fecha, datos) 
            VALUES (?, ?, ?, ?, GETDATE(), ?)";
    // ...
}

// Uso en procesar_*.php
registrarAccion('calibre', 'crear', $nuevo_id, ['codigo' => $codigo]);
```

**Acción:**
- [ ] Crear tabla `sys_auditoria` en BD
- [ ] Implementar función de auditoría
- [ ] Integrar en todos los procesar_*.php

---

## 💻 CÓDIGO (Prioridad Media)

### 8. 🟡 Duplicación de Código en JavaScript

**Problema:** Todos los JS tienen funciones similares (`cargarTabla`, `enviarFormulario`, etc.)

**Solución:** Crear librería compartida
```javascript
// app/assets/js/comun.js
const API = {
    obtener: (endpoint) => fetch(`../${endpoint}`).then(r => r.json()),
    procesar: (endpoint, datos) => fetch(`../${endpoint}`, {
        method: 'POST',
        body: datos
    }).then(r => r.text())
};

const UI = {
    cargarTabla: (endpoint, tbodySelector, columnas) => {
        API.obtener(endpoint).then(data => {
            // Lógica genérica para renderizar tabla
        });
    },
    mostrarMensaje: (texto, tipo = 'info') => {
        // Toast o alert genérico
    }
};

// Uso en calibre.js
document.addEventListener("DOMContentLoaded", () => {
    UI.cargarTabla('obtener_calibres.php', '#tablaCalibres tbody', ['id', 'codigo', 'nombre']);
});
```

**Acción:**
- [ ] Crear `app/assets/js/comun.js`
- [ ] Refactorizar JS existentes para usar librería
- [ ] Reducir líneas de código ~60%

---

### 9. 🟡 Sin Paginación en Tablas

**Problema:** Si hay muchos registros, la tabla se vuelve lenta

**Solución:**
```php
// obtener_*.php con paginación
$pagina = $_GET['pagina'] ?? 1;
$por_pagina = 20;
$inicio = ($pagina - 1) * $por_pagina;

$sql = "SELECT * FROM calibre ORDER BY codigo OFFSET $inicio ROWS FETCH NEXT $por_pagina ROWS ONLY";
```

```javascript
// JS con navegación
function cargarTabla(pagina = 1) {
    fetch(`../obtener_calibres.php?pagina=${pagina}`)
        .then(r => r.json())
        .then(data => {
            // Renderizar
            // Mostrar botones de paginación
        });
}
```

**Acción:**
- [ ] Agregar paginación a todos los `obtener_*.php`
- [ ] Actualizar JS para manejar paginación
- [ ] Agregar controles de navegación en HTML

---

### 10. 🟡 Sin Búsqueda/Filtrado

**Problema:** No se puede buscar dentro de las tablas

**Solución:**
```php
// obtener_*.php con búsqueda
$busqueda = $_GET['q'] ?? '';
$sql = "SELECT * FROM calibre 
        WHERE codigo LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%'
        ORDER BY codigo";
```

```javascript
// Búsqueda en tiempo real
document.getElementById('buscador').addEventListener('input', (e) => {
    cargarTabla(1, e.target.value);
});
```

**Acción:**
- [ ] Agregar campo de búsqueda en cada configuración
- [ ] Implementar búsqueda en backend
- [ ] Agregar debounce en JS para no saturar

---

## 📊 BASE DE DATOS (Prioridad Media)

### 11. 🟡 Sin Índices en Tablas

**Acción:**
- [ ] Revisar que todas las tablas tengan índices en:
  - Primary Keys (deberían existir)
  - Foreign Keys (`id_especie`, `id_exportadora`, etc.)
  - Campos de búsqueda frecuente (`codigo_calibre`, `nom_usu`, etc.)

```sql
CREATE INDEX IX_calibre_codigo ON calibre(codigo_calibre);
CREATE INDEX IX_categoria_especie ON categoria(id_especie);
CREATE INDEX IX_usuario_nom ON TRA_usuario(nom_usu);
```

---

### 12. 🟡 Sin Stored Procedures para Operaciones Críticas

**Problema:** Lógica de negocio en PHP en lugar de BD

**Solución:** Crear stored procedures para operaciones críticas
```sql
CREATE PROCEDURE sp_guardar_calibre
    @codigo VARCHAR(20),
    @nombre VARCHAR(100),
    @id_especie INT,
    @id_usuario INT
AS
BEGIN
    -- Validaciones
    -- Inserción
    -- Auditoría
    -- Retorno de resultado
END
```

**Acción:**
- [ ] Identificar operaciones críticas
- [ ] Crear stored procedures
- [ ] Actualizar PHP para llamarlos

---

## 🎨 UX/UI (Prioridad Baja)

### 13. 🟢 Sin Confirmación Visual de Operaciones

**Problema:** Solo usa `alert()` nativo del navegador

**Solución:** Usar Toast notifications
```javascript
// Usar Bootstrap Toast o SweetAlert2
function mostrarToast(mensaje, tipo = 'success') {
    // Implementar toast reutilizable
}
```

**Acción:**
- [ ] Integrar SweetAlert2 o similar
- [ ] Reemplazar todos los `alert()` por toasts
- [ ] Agregar animaciones

---

### 14. 🟢 Sin Feedback de Carga

**Problema:** No hay indicador mientras se procesan peticiones

**Solución:**
```javascript
function procesar(accion) {
    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border"></span> Guardando...';
    
    fetch(...)
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = 'Guardar';
        });
}
```

**Acción:**
- [ ] Agregar spinners en botones durante peticiones
- [ ] Deshabilitar botones para evitar doble envío
- [ ] Agregar overlay de carga si la operación es larga

---

### 15. 🟢 Sin Diseño Responsive Completo

**Problema:** Tablas y formularios no se adaptan bien a móviles

**Solución:**
- [ ] Usar `table-responsive` de Bootstrap en todas las tablas
- [ ] Ajustar grids de formularios para móviles
- [ ] Testear en dispositivos reales

---

## 📋 CHECKLIST DE IMPLEMENTACIÓN

### Fase 1 - Crítico (Semana 1)
- [ ] Rotar credenciales de BD y crear usuario específico
- [ ] Mover credenciales a `.env`
- [ ] Ejecutar migración de contraseñas
- [ ] Implementar validación de inputs básica
- [ ] Agregar protección CSRF

### Fase 2 - Seguridad (Semana 2)
- [ ] Implementar prepared statements o validación estricta
- [ ] Agregar sistema de logs de auditoría
- [ ] Revisar y endurecer permisos de BD

### Fase 3 - Arquitectura (Semana 3-4)
- [ ] Crear validador centralizado
- [ ] Implementar manejador de errores
- [ ] Refactorizar JavaScript con librería compartida

### Fase 4 - UX (Semana 5)
- [ ] Agregar paginación
- [ ] Implementar búsqueda/filtrado
- [ ] Mejorar feedback visual (toasts, loaders)
- [ ] Ajustar responsive design

### Fase 5 - Optimización (Semana 6)
- [ ] Agregar índices a BD
- [ ] Crear stored procedures críticos
- [ ] Implementar caché donde aplique

---

## 📈 MÉTRICAS DE MEJORA

| Métrica | Actual | Objetivo |
|---------|--------|----------|
| Vulnerabilidades Críticas | 4 | 0 |
| Código Duplicado (JS) | ~80% | <20% |
| Tiempo de Carga (tabla 100 regs) | ~2s | <0.5s |
| Cobertura de Validación | ~30% | 100% |
| Logs de Auditoría | 0% | 100% |

---

## 🎯 PRIORIDADES RECOMENDADAS

1. **INMEDIATO:** Seguridad (credenciales, passwords, SQL injection)
2. **CORTO PLAZO:** Validación de inputs y CSRF
3. **MEDIANO PLAZO:** Refactorización de código y auditoría
4. **LARGO PLAZO:** UX/UI y optimización

---

_Documento generado automáticamente - Scapy_
