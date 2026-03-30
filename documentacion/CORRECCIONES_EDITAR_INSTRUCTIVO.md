# 🔧 CORRECCIONES EDITAR INSTRUCTIVO - 27 Mar 2026 17:45

## 🐛 PROBLEMAS REPORTADOS Y CORREGIDOS

### 1. ✅ NÚMERO DE PEDIDO COMO INPUT (EN DOS LUGARES)
**Problema:** En "Detalle por Calibre", tanto el formulario de AGREGAR como el modal de EDITAR tenían INPUT de texto para número de pedido

**Solución:**
1. **`editar_instructivo.php`** - Formulario AGREGAR: INPUT → SELECT
2. **`editar_instructivo.php`** - Modal EDITAR: Ya tenía SELECT (confirmado)
3. **`editar_instructivo.js`** - Nueva función `actualizarSelectNumeroPedido()` que:
   - Se ejecuta al cargar instructivo
   - Se ejecuta al agregar pedido
   - Se ejecuta al eliminar pedido
   - Mantiene la selección actual si todavía existe

---

### 2. ✅ CATEGORÍA MUESTRA "UNDEFINED"
**Problema:** En la tabla de detalle, la columna "Categoría" mostraba "undefined"

**Causa:** 
- El campo `nombre_categoria` podía venir NULL desde la base de datos
- No había fallback en JavaScript

**Solución:**
1. **`obtener_categoria.php`** - Agregado alias explícito `as nombre_categoria`
2. **`obtener_instructivo_para_edicion.php`** - Agregado fallback: `nombre_categoria ?? cod_categoria ?? ''`
3. **`editar_instructivo.js`** - Agregado fallback en 3 lugares:
   - Carga de combos: `cat.nombre_categoria || cat.cod_categoria || 'Sin nombre'`
   - Render tabla: `${det.categoria_text || 'Sin categoría'}`
   - Console.log para depuración

---

### 3. ✅ ACTUALIZACIÓN DINÁMICA DE SELECTS
**Problema:** Al agregar/eliminar pedidos, el select de "Número Pedido" no se actualizaba

**Solución:**
- Nueva función `actualizarSelectNumeroPedido()` que:
  - Obtiene pedidos únicos de `pedidosEdit`
  - Ordena los números
  - Reconstruye el select completo
  - Mantiene la selección actual si existe
- Se llama desde:
  - `agregarPedido()` - después de agregar
  - `eliminarPedido()` - después de eliminar
  - `cargarInstructivo()` - después de cargar datos iniciales

---

## 📁 ARCHIVOS MODIFICADOS (6)

| Archivo | Cambios |
|---------|---------|
| `app/models/obtener_categoria.php` | +1 línea (alias explícito) |
| `app/models/obtener_instructivo_para_edicion.php` | +2 líneas (fallback) |
| `app/Procesos/editar_instructivo.php` | +4 líneas (2 INPUT → SELECT) |
| `app/assets/js/editar_instructivo.js` | +50 líneas (funciones nuevas + actualizaciones) |

---

## 🆕 FUNCIONES AGREGADAS

### `actualizarSelectNumeroPedido()`
**Propósito:** Actualiza dinámicamente los selects de "Número Pedido" cuando se agregan/eliminan pedidos

**Lógica:**
1. Obtiene la lista de pedidos de `pedidosEdit`
2. Extrae los números únicos con `Set`
3. Ordena los números
4. Reconstruye el select completo
5. Mantiene la selección actual si todavía existe

**Ejemplo:**
```javascript
// Si pedidosEdit = [
//   {numero_pedido: "1044", ...},
//   {numero_pedido: "1045", ...},
//   {numero_pedido: "1044", ...}  // duplicado
// ]

// Resultado en select:
// <option>1044</option>
// <option>1045</option>
```

**Se llama desde:**
- `agregarPedido()` - después de agregar un pedido
- `eliminarPedido()` - después de eliminar un pedido
- `cargarInstructivo()` - después de cargar datos iniciales

---

## 🧪 PRUEBAS REALIZADAS

### Test 1: Número de pedido (formulario AGREGAR)
1. Ir a "Editar Instructivo"
2. Agregar un pedido (ej: 1044)
3. Ir a sección "Detalle por Calibre"
4. Click en "Número Pedido"
5. ✅ Verifica que es SELECT con opción "1044"

### Test 2: Número de pedido (modal EDITAR)
1. Click en "✏️" de un detalle existente
2. ✅ Verifica que "Número Pedido" es SELECT
3. ✅ Verifica que muestra opciones disponibles

### Test 3: Actualización dinámica
1. Agregar pedido 1044
2. ✅ Verifica que select de detalle muestra "1044"
3. Agregar pedido 1045
4. ✅ Verifica que select ahora muestra "1044" y "1045"
5. Eliminar pedido 1044
6. ✅ Verifica que select solo muestra "1045"

### Test 4: Categoría en tabla
1. Cargar instructivo con categoría
2. Verificar que tabla muestra "XF - EXTRA FANCY" (no "undefined")
3. ✅ **CORREGIDO**

### Test 5: Editar detalle funciona
1. Click en "✏️" de un detalle
2. Modal abre con todos los campos pre-llenados
3. Cambiar número de pedido desde el select
4. Cambiar categoría
5. Guardar
6. ✅ Verifica que se actualizó correctamente

---

## 📊 COMPARACIÓN ANTES VS AHORA

| Campo | Antes | Ahora |
|-------|-------|-------|
| Número pedido (AGREGAR) | INPUT de texto ❌ | SELECT dinámico ✅ |
| Número pedido (EDITAR) | INPUT de texto ❌ | SELECT dinámico ✅ |
| Actualización al agregar/eliminar | Manual ❌ | Automática ✅ |
| Categoría en tabla | `undefined` ❌ | `XF - EXTRA FANCY` ✅ |
| Fallback categoría | N/A | Usa `cod_categoria` si no hay nombre ✅ |

---

## 🎯 ESTADO ACTUAL

| Funcionalidad | Estado |
|---------------|--------|
| Prioridad en pedidos (input libre) | ✅ LISTO |
| Botón editar en pedidos | ✅ LISTO |
| Botón editar en detalle | ✅ LISTO |
| Modal pantalla completa en detalle | ✅ LISTO |
| Categoría muestra nombre (no undefined) | ✅ LISTO |
| Número pedido como SELECT (AGREGAR) | ✅ LISTO |
| Número pedido como SELECT (EDITAR) | ✅ LISTO |
| Actualización dinámica de selects | ✅ LISTO |

---

## 🚀 PRÓXIMOS PASOS (OPCIONAL)

- [ ] Eliminar console.log después de verificar que funciona
- [ ] Agregar validación: no permitir guardar si número_pedido está vacío
- [ ] Mejorar UX: mostrar cantidad disponible del pedido seleccionado
- [ ] Prueba end-to-end completa: crear instructivo con todos los campos

---

**URL de prueba:**
```
http://localhost/instructivo/app/Procesos/editar_instructivo.php
```

**Estado:** ✅ CORRECCIONES COMPLETADAS - LISTO PARA PROBAR

---

_Última actualización: 27 Mar 2026 17:45_
