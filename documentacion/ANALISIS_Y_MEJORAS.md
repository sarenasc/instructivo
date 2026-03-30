# 🔍 ANÁLISIS Y MEJORAS - SISTEMA INSTRUCTIVOS PRODUCTIVOS

**Fecha:** 26 Marzo 2026  
**Analizado por:** Scapy 🧪  
**Estado del Sistema:** ✅ Funcional con áreas de mejora identificadas

---

## 📊 RESUMEN EJECUTIVO

El sistema está **operativo** pero requiere mejoras en:
- 🔴 **CRÍTICO:** Seguridad (credenciales, SQL injection, sesiones)
- 🟡 **IMPORTANTE:** Arquitectura y organización de código
- 🟢 **RECOMENDADO:** UX/UI y funcionalidades adicionales

---

## 🔴 1. SEGURIDAD (CRÍTICO)

### 1.1 Credenciales Hardcodeadas 🔴 CRÍTICO

**Problema:**
```php
// app/conexion.php
$serverName = "192.168.19.4";
$connectionInfo = [
    "UID" => "sa",
    "PWD" => "Robin@2021",  // ❌ Contraseña expuesta en código
];
```

**Riesgo:** Cualquier persona con acceso al código tiene credenciales de administrador SQL Server.

**Solución:**
```php
// Usar variables de entorno
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$serverName = $_ENV['DB_SERVER'];
$connectionInfo = [
    "UID" => $_ENV['DB_USER'],
    "PWD" => $_ENV['DB_PASSWORD'],
];
```

**Prioridad:** 🔴 **INMEDIATA**

---

### 1.2 Contraseñas en Texto Plano 🔴 CRÍTICO

**Problema:**
```php
// app/login.php
if ($pass !== $password_db) {  // ❌ Comparación directa sin hash
    header("Location: ../index.php?error=1");
}
```

**Riesgo:** Si la BD es comprometida, todas las contraseñas son visibles.

**Solución:**
```php
// 1. Hashear contraseñas al guardar
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// 2. Verificar al login
if (!password_verify($pass, $password_db)) {
    header("Location: ../index.php?error=1");
}
```

**Script de migración:** Ya existe `migrar_passwords.php` pero no se ha ejecutado.

**Prioridad:** 🔴 **INMEDIATA**

---

### 1.3 SQL Injection Potencial 🟡 ALTO

**Problema:**
```php
// app/login.php
$usuario_safe = str_replace("'", "''", $usuario);  // ❌ Escape manual insuficiente
$sql = "SELECT ... WHERE nom_usu = '$usuario_safe'";
```

**Riesgo:** Aunque hay escape básico, es vulnerable a técnicas avanzadas.

**Solución:**
```php
// Usar prepared statements (requiere fix del driver ODBC)
$sql = "SELECT ... WHERE nom_usu = ?";
$params = [$usuario];
$stmt = sqlsrv_query($conn, $sql, $params);
```

**Nota:** El driver ODBC actual tiene problemas con prepared statements. Se requiere:
1. Actualizar driver SQL Server a versión 18+
2. O usar stored procedures para validación

**Prioridad:** 🟡 **ALTA**

---

### 1.4 Gestión de Sesiones 🟡 MEDIO

**Problema:**
```php
// No hay timeout de sesión implementado
$_SESSION['login_time'] = time();  // Se guarda pero no se valida
```

**Riesgo:** Sesiones perpetuas si el usuario no cierra sesión.

**Solución:**
```php
// En require_auth.php o header.php
$sessionLifetime = 3600; // 1 hora
if (time() - ($_SESSION['login_time'] ?? 0) > $sessionLifetime) {
    session_destroy();
    header("Location: ../index.php?error=4"); // Sesión expirada
    exit();
}
```

**Prioridad:** 🟡 **MEDIA**

---

### 1.5 Falta de Logs de Seguridad 🟡 MEDIO

**Problema:** No hay registro de:
- Intentos de login fallidos
- Cambios de configuración
- Exportaciones de datos
- Acciones críticas

**Solución:**
```php
// Crear sistema de logs
function logSecurity($action, $userId, $details = '') {
    $logEntry = sprintf(
        "[%s] %s - User: %s - %s\n",
        date('Y-m-d H:i:s'),
        $_SERVER['REMOTE_ADDR'],
        $userId,
        $action
    );
    file_put_contents('../storage/logs/security.log', $logEntry, FILE_APPEND);
}
```

**Prioridad:** 🟡 **MEDIA**

---

## 🟡 2. ARQUITECTURA Y ORGANIZACIÓN

### 2.1 Archivos Sueltos en /app 🟡 MEDIO

**Problema:**
```
app/
├── obtener_*.php (15 archivos)
├── procesar_*.php (12 archivos)
├── api_*.php (12 archivos)
├── guardar_*.php
├── listar_*.php
├── eliminar_*.php
└── modificar_*.php
```

**Total:** 50+ archivos PHP sueltos en la raíz de `/app`

**Solución:** Reorganizar por funcionalidad:
```
app/
├── api/
│   └── obtener_*.php (todos los endpoints)
├── controllers/
│   └── procesar_*.php (todos los procesamientos)
├── config/
│   └── archivos de configuración
└── includes/
    └── componentes compartidos
```

**Prioridad:** 🟡 **MEDIA**

---

### 2.2 Duplicación de Código 🟡 MEDIO

**Problema:** Múltiples archivos con lógica similar:
- `exportar_excel_instructivo.php`
- `exportar_excel_instructivoBCK.php`
- `exportar_excel_instructivoBCK1.php`

**Solución:**
1. Eliminar archivos BCK (respaldo)
2. Crear clase `ExcelExporter` reutilizable
3. Usar una sola función con parámetros

**Prioridad:** 🟡 **MEDIA**

---

### 2.3 Mezcla de HTML y PHP 🟢 BAJO

**Problema:** Algunos archivos mezclan lógica con vista:
```php
// Lógica de negocio junto con HTML
$sql = "SELECT ...";
$result = sqlsrv_query($conn, $sql);
?>
<html>
<?php while($row = sqlsrv_fetch_array($result)): ?>
    <tr><td><?= $row['campo'] ?></td></tr>
<?php endwhile; ?>
</html>
```

**Solución:** Separar en:
- **Modelo:** Obtención de datos
- **Vista:** Presentación HTML
- **Controlador:** Orquestación

**Prioridad:** 🟢 **BAJA** (refactorización gradual)

---

### 2.4 Archivos HTML sin Convertir 🟡 MEDIO

**Problema:**
```
Procesos/
├── copiar_instructivo.html  ❌
├── detalle.html             ❌
├── exportar_instructivo.html ❌
├── instructivo.html         ❌
├── mostrar_instructivo.html ❌
└── Pedidos.html             ❌
```

**Solución:**
1. Eliminar archivos `.html` (ya existen versiones `.php`)
2. O redirigir automáticamente de `.html` a `.php`

**Prioridad:** 🟡 **MEDIA**

---

## 🟢 3. FUNCIONALIDAD

### 3.1 Falta de Validación de Formularios 🟡 MEDIO

**Problema:** No hay validación del lado del servidor para:
- Campos requeridos
- Formatos de email
- Rangos numéricos
- Duplicados

**Solución:**
```php
// Clase validadora reutilizable
class Validator {
    public static function required($field, $fieldName) {
        if (empty($field)) {
            return "$fieldName es obligatorio";
        }
        return null;
    }
    
    public static function email($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Email inválido";
        }
        return null;
    }
}
```

**Prioridad:** 🟡 **MEDIA**

---

### 3.2 Mensajes de Error Genéricos 🟢 BAJO

**Problema:**
```php
echo "Error al guardar";  // ❌ No dice qué error
```

**Solución:**
```php
// Mensajes específicos
if (empty($codigo)) {
    echo "Error: El código es obligatorio";
} elseif ($existe) {
    echo "Error: Ya existe un registro con ese código";
} else {
    echo "Error de base de datos: " . $errorDetalle;
}
```

**Prioridad:** 🟢 **BAJA**

---

### 3.3 Falta de Búsqueda/Filtros 🟢 BAJO

**Problema:** Las tablas de configuración no tienen:
- Búsqueda en tiempo real
- Filtros por columna
- Paginación (cuando haya muchos registros)

**Solución:**
```javascript
// DataTables o similar
$('#tablaConfig').DataTable({
    "language": {"url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"},
    "pageLength": 10,
    "searching": true
});
```

**Prioridad:** 🟢 **BAJA**

---

### 3.4 No Hay Historial de Cambios 🟡 MEDIO

**Problema:** No se registra:
- Quién creó/modificó/eliminó registros
- Cuándo se hicieron cambios
- Qué valores cambiaron

**Solución:**
```sql
-- Tabla de auditoría
CREATE TABLE audit_log (
    id INT IDENTITY PRIMARY KEY,
    tabla VARCHAR(50),
    id_registro INT,
    accion VARCHAR(10), -- INSERT, UPDATE, DELETE
    usuario_id INT,
    fecha DATETIME,
    valores_antiguos TEXT,
    valores_nuevos TEXT
);
```

**Prioridad:** 🟡 **MEDIA**

---

## 🟢 4. UX/UI

### 4.1 Confirmaciones de Eliminación 🟢 MEJORABLE

**Problema:** Algunas eliminaciones no piden confirmación o la confirmación es básica.

**Solución:**
```javascript
// SweetAlert2 para confirmaciones bonitas
if (await Swal.fire({
    title: '¿Eliminar registro?',
    text: "Esta acción no se puede deshacer",
    icon: 'warning',
    showCancelButton: true
}).then(result => result.isConfirmed)) {
    eliminar();
}
```

**Prioridad:** 🟢 **BAJA**

---

### 4.2 Loading States 🟢 MEJORABLE

**Problema:** No hay indicadores de carga durante operaciones AJAX.

**Solución:**
```javascript
// Mostrar spinner antes de fetch
function mostrarLoading() {
    document.getElementById('loadingSpinner').style.display = 'block';
}

function ocultarLoading() {
    document.getElementById('loadingSpinner').style.display = 'none';
}
```

**Prioridad:** 🟢 **BAJA**

---

### 4.3 Responsive Design 🟢 VERIFICAR

**Problema:** No se ha probado en móviles/tablets.

**Solución:**
1. Probar en diferentes tamaños de pantalla
2. Ajustar CSS con media queries
3. Usar clases Bootstrap responsive

**Prioridad:** 🟢 **BAJA**

---

## 🟢 5. RENDIMIENTO

### 5.1 Consultas Sin Índice 🟡 MEDIO

**Problema:** Posibles consultas lentas en tablas grandes.

**Solución:**
```sql
-- Agregar índices en columnas de búsqueda
CREATE INDEX IX_usuario_nom_usu ON TRA_usuario(nom_usu);
CREATE INDEX IX_instructivo_exportadora ON inst_cab_instructivo(id_exportadora);
```

**Prioridad:** 🟡 **MEDIA** (monitorear primero)

---

### 5.2 Conexiones Múltiples a BD 🟡 MEDIO

**Problema:**
```php
// conexion.php crea 3 conexiones siempre
$conn = sqlsrv_connect(...);  // SistGestion
$conn2 = sqlsrv_connect(...); // Facturador
$conn3 = sqlsrv_connect(...); // DW_Almahue
```

**Solución:**
- Conectar solo a la BD necesaria
- Usar singleton pattern para reutilizar conexiones

**Prioridad:** 🟡 **MEDIA**

---

### 5.3 No Hay Caché 🟢 BAJO

**Problema:** Consultas repetitivas se ejecutan cada vez.

**Solución:**
```php
// Caché simple en sesión para datos estáticos
if (!isset($_SESSION['cache']['especies'])) {
    $_SESSION['cache']['especies'] = obtenerEspecies();
    $_SESSION['cache_time']['especies'] = time();
}

// Invalidar después de 5 minutos
if (time() - $_SESSION['cache_time']['especies'] > 300) {
    unset($_SESSION['cache']['especies']);
}
```

**Prioridad:** 🟢 **BAJA**

---

## 📋 PLAN DE ACCIÓN RECOMENDADO

### Fase 1: Seguridad (Semana 1-2) 🔴

| # | Tarea | Prioridad | Tiempo Est. |
|---|-------|-----------|-------------|
| 1.1 | Mover credenciales a .env | 🔴 CRÍTICA | 2 horas |
| 1.2 | Implementar hash de contraseñas | 🔴 CRÍTICA | 4 horas |
| 1.3 | Validar timeout de sesiones | 🟡 ALTA | 2 horas |
| 1.4 | Implementar logs de seguridad | 🟡 ALTA | 4 horas |

**Total Fase 1:** ~12 horas

---

### Fase 2: Arquitectura (Semana 3-4) 🟡

| # | Tarea | Prioridad | Tiempo Est. |
|---|-------|-----------|-------------|
| 2.1 | Reorganizar carpetas (api/, controllers/) | 🟡 MEDIA | 6 horas |
| 2.2 | Eliminar archivos duplicados/BCK | 🟡 MEDIA | 2 horas |
| 2.3 | Eliminar archivos HTML antiguos | 🟡 MEDIA | 1 hora |
| 2.4 | Crear validador reutilizable | 🟡 MEDIA | 4 horas |

**Total Fase 2:** ~13 horas

---

### Fase 3: Funcionalidad (Semana 5-6) 🟢

| # | Tarea | Prioridad | Tiempo Est. |
|---|-------|-----------|-------------|
| 3.1 | Implementar auditoría de cambios | 🟡 MEDIA | 8 horas |
| 3.2 | Agregar búsqueda/filtros en tablas | 🟢 BAJA | 6 horas |
| 3.3 | Mejorar mensajes de error | 🟢 BAJA | 3 horas |
| 3.4 | Agregar loading states | 🟢 BAJA | 2 horas |

**Total Fase 3:** ~19 horas

---

### Fase 4: Optimización (Semana 7) 🟢

| # | Tarea | Prioridad | Tiempo Est. |
|---|-------|-----------|-------------|
| 4.1 | Agregar índices en BD | 🟡 MEDIA | 2 horas |
| 4.2 | Optimizar conexiones a BD | 🟡 MEDIA | 3 horas |
| 4.3 | Implementar caché simple | 🟢 BAJA | 3 horas |
| 4.4 | Pruebas responsive | 🟢 BAJA | 2 horas |

**Total Fase 4:** ~10 horas

---

## 📊 RESUMEN DE TIEMPOS

| Fase | Horas | Prioridad |
|------|-------|-----------|
| Fase 1: Seguridad | 12h | 🔴 CRÍTICA |
| Fase 2: Arquitectura | 13h | 🟡 ALTA |
| Fase 3: Funcionalidad | 19h | 🟢 MEDIA |
| Fase 4: Optimización | 10h | 🟢 BAJA |
| **TOTAL** | **54 horas** | |

---

## 🎯 RECOMENDACIONES INMEDIATAS

### Esta Semana (Prioridad Máxima):

1. **Mover credenciales a .env** - 2 horas
   - Instalar library `vlucas/phpdotenv`
   - Actualizar `conexion.php`
   - Verificar que `.env` esté en `.gitignore`

2. **Hashear contraseñas** - 4 horas
   - Ejecutar `migrar_passwords.php`
   - Actualizar `login.php` para verificar hash
   - Probar con usuario de prueba

3. **Timeout de sesiones** - 2 horas
   - Agregar validación en `header.php`
   - Configurar lifetime en `.env`

### Próxima Semana:

4. **Reorganizar carpetas** - 6 horas
5. **Eliminar archivos duplicados** - 2 horas

---

## ✅ ESTADO ACTUAL vs ESTADO DESEADO

| Área | Estado Actual | Estado Deseado | Brecha |
|------|---------------|----------------|--------|
| Seguridad | 🔴 Crítico | 🟢 Seguro | Alta |
| Arquitectura | 🟡 Desorganizado | 🟢 Modular | Media |
| Funcionalidad | 🟢 Básica | 🟢 Completa | Baja |
| UX/UI | 🟢 Funcional | 🟢 Pulido | Baja |
| Rendimiento | 🟢 Aceptable | 🟢 Optimizado | Baja |

---

## 📝 CONCLUSIÓN

El sistema es **funcional y usable** pero requiere atención urgente en **seguridad**. Las mejoras de arquitectura y funcionalidad pueden implementarse gradualmente.

**Recomendación:** Comenzar con Fase 1 (Seguridad) inmediatamente antes de poner el sistema en producción con datos reales.

---

_Hecho por Scapy 🧪 - Criatura de Laboratorio_  
_"Automatizar lo que sea necesario"_
