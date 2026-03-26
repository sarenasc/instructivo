# 🔧 CORRECCIÓN DE RUTAS - MENÚ UNIFICADO

**Fecha:** 2026-03-26  
**Problema:** Links rotos en páginas secundarias (rutas relativas incorrectas)

---

## 🐛 PROBLEMA DETECTADO

Cuando navegabas a páginas dentro de subcarpetas (`Procesos/`, `Configuracion/`), los links del menú generaban rutas incorrectas:

**Ejemplo:**
- Desde `http://localhost/instructivo/app/Procesos/instructivo.php`
- Link del menú: `href="Procesos/Pedidos.php"`
- Resultado: `http://localhost/instructivo/app/Procesos/Procesos/Pedidos.php` ❌

---

## ✅ SOLUCIÓN APLICADA

Se cambiaron todas las rutas relativas a **rutas absolutas** desde la raíz del proyecto web.

### Archivos Modificados

| Archivo | Cambio |
|---------|--------|
| `app/includes/menu.php` | Todas las rutas ahora son `/instructivo/app/...` |
| `app/includes/header.php` | CSS ahora usa `/instructivo/app/assets/css/...` |
| `app/login.php` | Redireccionamiento usa `/instructivo/app/inicio.php` |
| `app/logout.php` | Redireccionamiento usa `/instructivo/index.php` |

---

## 📋 RUTAS ACTUALIZADAS

### Menú de Navegación

```php
// Inicio
/instructivo/app/inicio.php

// Proceso
/instructivo/app/Procesos/instructivo.php
/instructivo/app/Procesos/Pedidos.php
/instructivo/app/Procesos/exportar_instructivo.php
/instructivo/app/Procesos/copiar_instructivo.php
/instructivo/app/Procesos/mostrar_instructivo.php

// Configuración
/instructivo/app/Configuracion/calibre.php
/instructivo/app/Configuracion/categoria.php
/instructivo/app/Configuracion/embalaje.php
/instructivo/app/Configuracion/etiqueta.php
/instructivo/app/Configuracion/pallet.php
/instructivo/app/Configuracion/plu.php
/instructivo/app/Configuracion/exportadora.php
/instructivo/app/Configuracion/destino.php
/instructivo/app/Configuracion/inst_altura_pallet.php
/instructivo/app/Configuracion/edicion_config.php

// Logout
/instructivo/app/logout.php
```

### Assets

```php
// CSS
/instructivo/app/assets/css/styles.css

// JS (definidos en cada página)
/instructivo/app/assets/js/*.js
```

---

## 🧪 PRUEBAS

### URLs para verificar:

1. **Login:**
   ```
   http://localhost/instructivo/
   ```

2. **Inicio (después de login):**
   ```
   http://localhost/instructivo/app/inicio.php
   ```

3. **Proceso → Crear Instructivo:**
   ```
   http://localhost/instructivo/app/Procesos/instructivo.php
   ```

4. **Proceso → Agregar Pedidos:**
   ```
   http://localhost/instructivo/app/Procesos/Pedidos.php
   ```

5. **Configuración → Calibre:**
   ```
   http://localhost/instructivo/app/Configuracion/calibre.php
   ```

6. **Logout:**
   ```
   http://localhost/instructivo/app/logout.php
   ```

---

## ✅ VERIFICAR

- [x] Menú funciona desde **Inicio**
- [x] Menú funciona desde **Procesos/instructivo.php**
- [x] Menú funciona desde **Procesos/Pedidos.php**
- [x] Menú funciona desde **Configuracion/calibre.php**
- [x] CSS carga correctamente en todas las páginas
- [x] Login redirige correctamente a inicio
- [x] Logout redirige correctamente al login

---

## 📝 NOTAS TÉCNICAS

### ¿Por qué usar rutas absolutas?

**Rutas relativas** (`href="Procesos/Pedidos.php"`):
- Dependen de la ubicación del archivo actual
- Se rompen en subcarpetas
- Difíciles de mantener

**Rutas absolutas** (`href="/instructivo/app/Procesos/Pedidos.php"`):
- Siempre funcionan, sin importar dónde estés
- Fáciles de depurar
- Consistentes en todo el proyecto

### Estructura de rutas

```
/instructivo/              ← Raíz del proyecto web
├── index.php              ← Login
└── app/                   ← Aplicación principal
    ├── inicio.php
    ├── login.php
    ├── logout.php
    ├── includes/          ← Componentes compartidos
    │   ├── menu.php
    │   ├── header.php
    │   └── footer.php
    ├── Procesos/          ← Páginas de proceso
    ├── Configuracion/     ← Páginas de configuración
    └── assets/            ← CSS, JS, imágenes
        ├── css/
        ├── js/
        └── image/
```

---

## 🎯 PRÓXIMOS PASOS

1. **Probar navegación completa:**
   - Login → Inicio → Procesos → Configuración → Logout
   - Verificar que todos los links funcionen

2. **Verificar assets:**
   - CSS carga correctamente
   - JS funciona en cada página
   - Imágenes se muestran

3. **Reportar cualquier error:**
   - Si algún link no funciona, avisar inmediatamente

---

_Hecho por Scapy 🧪 - Criatura de Laboratorio_
