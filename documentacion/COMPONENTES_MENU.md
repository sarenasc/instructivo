# 📋 COMPONENTES COMPARTIDOS - MENÚ UNIFICADO

## ✅ PROBLEMA RESUELTO

**Antes:** Cada página tenía su propio menú copiado y pegado, con links inconsistentes

**Ahora:** Un solo archivo de menú que se incluye en todas las páginas

---

## 📁 ARCHIVOS CREADOS

| Archivo | Propósito |
|---------|-----------|
| `app/includes/menu.php` | Menú de navegación completo |
| `app/includes/header.php` | Header con autenticación + menú |
| `app/includes/footer.php` | Footer con scripts comunes |
| `app/inicio.php` | Actualizado para usar componentes |

---

## 🔧 CÓMO USAR EN NUEVAS PÁGINAS

### Opción 1: PHP (Recomendado)

```php
<?php
$titulo_pagina = 'Mi Página';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Tu contenido aquí -->
<h1>Hola Mundo</h1>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
```

### Opción 2: Solo el menú

```php
<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mi Página</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php require_once __DIR__ . '/includes/menu.php'; ?>
    
    <!-- Tu contenido aquí -->
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## 📊 MENÚ ACTUALIZADO

### Inicio
- Link directo a `inicio.php`

### Proceso
- Creación de Instructivo
- Agregar Pedidos
- Descargar Instructivo
- Copiar Instructivo
- Desplegar Información

### Configuración
- Calibre
- Categoría
- Embalaje
- Etiqueta
- Pallet
- PLU
- Exportadora
- Destino
- Altura Pallet
- Edición de Configuración

### Usuario (derecha)
- 👤 Nombre del usuario
- Cerrar Sesión

---

## 🔄 PÁGINAS A ACTUALIZAR

### HTML → PHP (Recomendado)

Las páginas HTML actuales deberían convertirse a PHP para usar los componentes:

| Archivo Actual | Nuevo Sugerido |
|----------------|----------------|
| `Procesos/instructivo.html` | `Procesos/instructivo.php` |
| `Procesos/exportar_instructivo.html` | `Procesos/exportar_instructivo.php` |
| `Configuracion/calibre.html` | `Configuracion/calibre.php` |
| ... | ... |

### Si mantienes HTML

Si prefieres mantener los archivos HTML, puedes usar un menú estático. Pero perderás:
- Verificación de sesión
- Nombre de usuario dinámico
- Consistencia automática

---

## ✅ VENTAJAS

| Ventaja | Descripción |
|---------|-------------|
| 🎯 **Consistencia** | Todas las páginas tienen el mismo menú |
| 🔧 **Mantenimiento** | Cambias en un lado, se actualiza en todas |
| 🔐 **Seguridad** | Verificación de sesión centralizada |
| 👤 **Personalización** | Muestra el nombre del usuario logueado |
| 📱 **Responsive** | Menú responsive de Bootstrap 5 |

---

## 🛠️ PRÓXIMOS PASOS

### 1. Actualizar páginas existentes

Convertir las páginas HTML más importantes a PHP:

```bash
# Ejemplo para calibre.html
mv Configuracion/calibre.html Configuracion/calibre.php
# Editar para usar header.php y footer.php
```

### 2. Verificar todos los links

Asegurar que todos los links del menú funcionen correctamente.

### 3. Agregar verificación de sesión

En cada página PHP, el header ya verifica la sesión automáticamente.

---

## 📝 EJEMPLO COMPLETO

### `mi_pagina.php`

```php
<?php
$titulo_pagina = 'Mi Página - Instructivos';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container mt-5">
    <h1>Mi Página</h1>
    <p>Contenido de mi página...</p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
```

---

## ⚠️ IMPORTANTE

1. **Rutas relativas:** Los archivos en `includes/` usan rutas relativas asumiendo que están en `app/`
2. **Sesión:** El header inicia sesión automáticamente si no está iniciada
3. **Autenticación:** El header redirige al login si no hay sesión

---

## 🧪 TEST

1. Abre `http://localhost/instructivo/app/inicio.php`
2. Verifica que el menú se vea correctamente
3. Prueba todos los links del menú
4. Verifica que el nombre de usuario aparezca

---

_Hecho por Scapy 🧪 - Criatura de Laboratorio_
_2026-03-26_
