# 🔄 CONVERSIÓN HTML A PHP - MENÚ UNIFICADO

## ✅ PÁGINAS CONVERTIDAS

### Proceso
| Original | Nuevo | Estado |
|----------|-------|--------|
| `Procesos/instructivo.html` | `Procesos/instructivo.php` | ✅ Convertido |
| `Procesos/exportar_instructivo.html` | `Procesos/exportar_instructivo.php` | ✅ Convertido |
| `Procesos/Pedidos.html` | `Procesos/Pedidos.php` | ✅ Convertido |

### Configuración
| Original | Nuevo | Estado |
|----------|-------|--------|
| `Configuracion/calibre.html` | `Configuracion/calibre.php` | ✅ Convertido |
| `Configuracion/categoria.html` | `Configuracion/categoria.php` | ✅ Convertido |
| `Configuracion/embalaje.html` | `Configuracion/embalaje.php` | ✅ Convertido |

---

## 📁 PÁGINAS PENDIENTES

### Proceso
- [ ] `copiar_instructivo.html` → `copiar_instructivo.php`
- [ ] `mostrar_instructivo.html` → `mostrar_instructivo.php`
- [ ] `detalle.html` → `detalle.php`

### Configuración
- [ ] `etiqueta.html` → `etiqueta.php`
- [ ] `pallet.html` → `pallet.php`
- [ ] `plu.html` → `plu.php`
- [ ] `exportadora.html` → `exportadora.php`
- [ ] `destino.html` → `destino.php`
- [ ] `inst_altura_pallet.html` → `inst_altura_pallet.php`
- [ ] `edicion_config.html` → `edicion_config.php`

---

## 🎯 BENEFICIOS DE LA CONVERSIÓN

1. **Menú consistente** - Todas las páginas usan el mismo menú
2. **Autenticación centralizada** - `header.php` verifica sesión automáticamente
3. **Mantenimiento fácil** - Cambias el menú en un lugar, se actualiza en todas
4. **Scripts comunes** - Footer incluye jQuery y Bootstrap automáticamente
5. **Títulos dinámicos** - Cada página puede tener su propio título

---

## 🔧 CÓMO CONVERTIR EL RESTO

Para convertir las páginas restantes:

1. **Copiar estructura base:**
```php
<?php
$titulo_pagina = 'Nombre de la Página';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Contenido de la página -->

<?php
$scripts_extra = '<script src="archivo.js"></script>';
require_once __DIR__ . '/../includes/footer.php';
?>
```

2. **Mover el contenido** del HTML original entre header y footer

3. **Actualizar el menú** en `includes/menu.php` para que apunte al nuevo `.php`

4. **Probar** que todo funcione

5. **Eliminar** el archivo `.html` original

---

## 🧪 PROBAR AHORA

1. `http://localhost/instructivo/app/inicio.php` - Inicio
2. `http://localhost/instructivo/app/Procesos/instructivo.php` - Crear Instructivo
3. `http://localhost/instructivo/app/Procesos/exportar_instructivo.php` - Exportar
4. `http://localhost/instructivo/app/Configuracion/calibre.php` - Calibres

---

## ⚠️ IMPORTANTE

Los archivos `.html` originales **NO se eliminan** todavía. Una vez que verifiques que todo funciona:

```bash
# Eliminar archivos HTML originales
del app\Procesos\instructivo.html
del app\Procesos\exportar_instructivo.html
del app\Procesos\Pedidos.html
del app\Configuracion\calibre.html
del app\Configuracion\categoria.html
del app\Configuracion\embalaje.html
```

---
_Hecho por Scapy 🧪_
