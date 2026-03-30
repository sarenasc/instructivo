# 📋 VALIDADOR REUTILIZABLE - EJEMPLOS DE USO

**Ubicación:** `app/includes/Validator.php`

---

## 🎯 ¿CÓMO USARLO?

### 1. Incluir el validador

```php
<?php
require_once '../includes/Validator.php';
```

---

## 📝 EJEMPLOS PRÁCTICOS

### Ejemplo 1: Validación Básica

```php
<?php
require_once '../includes/Validator.php';

$errors = [];

// Validar campo requerido
$error = Validator::required($_POST['codigo'], 'Código');
if ($error) $errors[] = $error;

// Validar email
$error = Validator::email($_POST['email']);
if ($error) $errors[] = $error;

// Validar número
$error = Validator::numeric($_POST['precio'], 'Precio');
if ($error) $errors[] = $error;

// Si hay errores
if (!empty($errors)) {
    echo Validator::errorsToHtml($errors);
    exit();
}

// Si todo está válido, continuar con el guardado
```

---

### Ejemplo 2: Validación Completa

```php
<?php
require_once '../includes/Validator.php';
require_once '../conexion.php';

$errors = [];

// Validar código (requerido, único, max 50 caracteres)
$error = Validator::required($_POST['codigo'], 'Código');
if ($error) $errors[] = $error;

$error = Validator::maxLength($_POST['codigo'], 50, 'Código');
if ($error) $errors[] = $error;

$error = Validator::unique($conn, 'calibre', 'codigo', $_POST['codigo']);
if ($error) $errors[] = $error;

// Validar nombre (requerido, max 100 caracteres)
$error = Validator::required($_POST['nombre'], 'Nombre');
if ($error) $errors[] = $error;

$error = Validator::maxLength($_POST['nombre'], 100, 'Nombre');
if ($error) $errors[] = $error;

// Validar especie (requerido)
$error = Validator::required($_POST['id_especie'], 'Especie');
if ($error) $errors[] = $error;

// Retornar errores como JSON (para AJAX)
if (!empty($errors)) {
    header('Content-Type: application/json');
    echo Validator::errorsToJson($errors);
    exit();
}

// Continuar con guardado...
```

---

### Ejemplo 3: Validación para Modificación

```php
<?php
require_once '../includes/Validator.php';
require_once '../conexion.php';

$errors = [];
$id = $_POST['id_calibre'];

// Validar código (excluyendo el registro actual)
$error = Validator::required($_POST['codigo'], 'Código');
if ($error) $errors[] = $error;

// El 'unique' excluye el registro actual para no marcar como duplicado el mismo
$error = Validator::unique($conn, 'calibre', 'codigo', $_POST['codigo'], $id);
if ($error) $errors[] = $error;

if (!empty($errors)) {
    echo Validator::errorsToJson($errors);
    exit();
}

// Continuar con modificación...
```

---

### Ejemplo 4: Validación de Rango

```php
<?php
require_once '../includes/Validator.php';

$errors = [];

// Validar que altura esté entre 1 y 100
$error = Validator::range($_POST['altura'], 1, 100, 'Altura');
if ($error) $errors[] = $error;

// Validar que cajas esté entre 1 y 1000
$error = Validator::range($_POST['cajas'], 1, 1000, 'Cajas');
if ($error) $errors[] = $error;

if (!empty($errors)) {
    echo Validator::errorsToJson($errors);
    exit();
}
```

---

### Ejemplo 5: Validación de Fecha

```php
<?php
require_once '../includes/Validator.php';

$errors = [];

// Validar fecha de producción
$error = Validator::date($_POST['fecha_produccion']);
if ($error) $errors[] = $error;

if (!empty($errors)) {
    echo Validator::errorsToJson($errors);
    exit();
}
```

---

### Ejemplo 6: Validación de Contraseñas

```php
<?php
require_once '../includes/Validator.php';

$errors = [];

// Validar que las contraseñas coincidan
$error = Validator::matches(
    $_POST['password'],
    $_POST['password_confirm'],
    'Contraseña',
    'Confirmación de contraseña'
);
if ($error) $errors[] = $error;

// Validar longitud mínima
$error = Validator::minLength($_POST['password'], 8, 'Contraseña');
if ($error) $errors[] = $error;

if (!empty($errors)) {
    echo Validator::errorsToJson($errors);
    exit();
}
```

---

## 📚 MÉTODOS DISPONIBLES

| Método | Parámetros | Descripción |
|--------|------------|-------------|
| `required($field, $fieldName)` | valor, nombre | Valida que no esté vacío |
| `email($email)` | email | Valida formato de email |
| `integer($number, $fieldName)` | número, nombre | Valida entero |
| `numeric($number, $fieldName)` | número, nombre | Valida número decimal |
| `minLength($field, $min, $fieldName)` | valor, mínimo, nombre | Longitud mínima |
| `maxLength($field, $max, $fieldName)` | valor, máximo, nombre | Longitud máxima |
| `range($number, $min, $max, $fieldName)` | número, min, max, nombre | Rango numérico |
| `matches($f1, $f2, $n1, $n2)` | campo1, campo2, nombre1, nombre2 | Compara dos campos |
| `date($date)` | fecha | Valida formato YYYY-MM-DD |
| `unique($conn, $table, $field, $value, $excludeId)` | conexión, tabla, campo, valor, excluir | Valida unicidad en BD |
| `errorsToHtml($errors)` | array errores | Convierte errores a HTML |
| `errorsToJson($errors)` | array errores | Convierte errores a JSON |

---

## 🎨 FORMATOS DE SALIDA

### HTML (para formularios tradicionales)

```php
if (!empty($errors)) {
    echo Validator::errorsToHtml($errors);
    // Muestra:
    // <div class="alert alert-danger">
    //     <div>❌ El campo Código es obligatorio</div>
    //     <div>❌ Ya existe un registro con ese código</div>
    // </div>
}
```

### JSON (para AJAX)

```php
if (!empty($errors)) {
    header('Content-Type: application/json');
    echo Validator::errorsToJson($errors);
    // Retorna:
    // {"success":false,"errors":["El campo Código es obligatorio","..."]}
}
```

---

## 💡 MEJORES PRÁCTICAS

1. **Validar siempre del lado del servidor** aunque haya validación en JavaScript
2. **Retornar errores específicos** para que el usuario sepa qué corregir
3. **Usar `unique()` con `$excludeId`** en modificaciones para no marcar el propio registro
4. **Sanitizar datos** antes de usarlos en consultas SQL
5. **Retornar JSON** para peticiones AJAX, HTML para formularios tradicionales

---

## 🔗 INTEGRACIÓN CON CONTROLADORES EXISTENTES

Para integrar el validador en un controlador existente:

```php
<?php
// controllers/procesar_calibre.php

require_once("../conexion.php");
require_once("../includes/Validator.php");  // ← Agregar esta línea

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Validaciones
    $error = Validator::required($_POST['codigo_calibre'], 'Código');
    if ($error) $errors[] = $error;
    
    $error = Validator::required($_POST['nombre_calibre'], 'Nombre');
    if ($error) $errors[] = $error;
    
    // Si hay errores, retornar
    if (!empty($errors)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
        exit();
    }
    
    // Si no hay errores, continuar con la lógica existente...
}
```

---

_Hecho por Scapy 🧪 - Criatura de Laboratorio_
