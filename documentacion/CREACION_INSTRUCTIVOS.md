# 📋 CREACIÓN DE INSTRUCTIVOS - NUEVA FUNCIONALIDAD

**Fecha:** 27 Marzo 2026  
**Estado:** ✅ IMPLEMENTADO  
**Ubicación:** `http://localhost/instructivo/app/Procesos/crear_instructivo.php`

---

## 🎯 OBJETIVO

Unificar en una sola pantalla el proceso completo de creación de instructivos, eliminando la necesidad de múltiples pasos y permitiendo:

1. Ingresar cabecera (exportadora, especie, turno, observación)
2. Agregar múltiples pedidos con cantidad total y prioridad
3. Agregar detalle por calibre, asignando cada calibre a un pedido específico
4. Guardar TODO en una sola transacción

---

## 🏗️ ARQUITECTURA

### Archivos Creados

| Archivo | Función |
|---------|---------|
| `app/Procesos/crear_instructivo.php` | Pantalla principal de creación |
| `app/assets/js/crear_instructivo.js` | Lógica JavaScript (tablas dinámicas, validaciones) |
| `app/controllers/guardar_instructivo_completo.php` | Controller que guarda todo en transacción SQL |
| `app/inicio.php` | Actualizado con enlace a nueva funcionalidad |

### Archivos Existentes Reutilizados

| Archivo | Uso |
|---------|-----|
| `app/models/obtener_calibres.php` | Cargar combo de calibres |
| `app/models/obtener_embalajes.php` | Cargar combo de embalajes |
| `app/models/obtener_categoria.php` | Cargar combo de categorías |
| `app/models/obtener_plus.php` | Cargar combo de PLU |
| `app/models/obtener_etiquetas.php` | Cargar combo de etiquetas |
| `app/models/obtener_pallets.php` | Cargar combo de pallets |
| `app/services/api_exportadoras.php` | Cargar combo de exportadoras |
| `app/services/api_especies.php` | Cargar combo de especies |

---

## 📊 FLUJO DE TRABAJO

### PASO 1: Cabecera del Instructivo

**Campos:**
- **Exportadora:** Select con todas las exportadoras disponibles
- **Especie:** Select con las 16 especies (DAMASCO, NECTARINE, DURAZNO, etc.)
- **Turno:** Select (Turno 1, Turno 2, Turno 3)
- **Fecha:** Date picker (por defecto fecha actual)
- **Observación:** Textarea opcional

**Validación:** Todos los campos excepto observación son obligatorios

---

### PASO 2: Pedidos

**Campos por pedido:**
- **Número de Pedido:** Número entero (ej: 1044, 1232)
- **Cantidad Total:** Cantidad total del pedido (ej: 1000 cajas)
- **Prioridad:** Número entero (1 = más prioritario)

**Características:**
- Se pueden agregar múltiples pedidos
- Cada pedido se muestra en una tabla temporal
- Se puede eliminar pedidos antes de guardar
- Los pedidos aparecen en un combo para asignar calibres

**Tabla de BD:** `inst_pedidos`
```sql
id_pedido | id_instructivo | version | numero_pedido | cantidad | prioridad
```

---

### PASO 3: Detalle por Calibre

**Campos obligatorios:**
- **Calibre:** Select agrupado por especie
- **Asignar a Pedido:** Select con los pedidos agregados en PASO 2

**Campos opcionales:**
- **Cantidad Pedido:** Cantidad específica para este calibre (ej: 500 de las 1000 totales)
- **Embalaje:** Select con embalajes disponibles
- **Categoría:** Select con categorías
- **PLU:** Select con códigos PLU
- **Etiqueta:** Select con etiquetas
- **Pallet:** Select con tipos de pallet
- **Altura Pallet:** Número
- **Cajas:** Número

**Características:**
- Cada registro de detalle representa UN calibre específico
- El calibre se asigna a un pedido específico (numero_pedido)
- Se pueden agregar múltiples calibres al mismo pedido
- Se pueden agregar múltiples calibres con diferentes configuraciones

**Tabla de BD:** `inst_detalle_instructivo`
```sql
id | id_cab_instructivo | version | id_calibre | numero_pedido | cantidad_pedido |
id_embalaje | id_categoria | id_plu | id_etiqueta | id_pallet | altura_pallet
```

---

## 💾 PROCESO DE GUARDADO

### Transacción SQL

Al hacer clic en "GUARDAR INSTRUCTIVO COMPLETO":

```sql
BEGIN TRANSACTION

-- 1. Insertar cabecera
INSERT INTO inst_cab_instructivo (fecha, id_exportadora, id_especie, turno, observacion)
VALUES (...);
-- Obtener id_instructivo generado (SCOPE_IDENTITY)

-- 2. Insertar pedidos (uno por cada pedido agregado)
INSERT INTO inst_pedidos (id_instructivo, version, numero_pedido, cantidad, prioridad)
VALUES (id_instructivo, 1, 1044, '1000', 1),
       (id_instructivo, 1, 1232, '500', 2),
       ...;

-- 3. Insertar detalle (uno por cada calibre agregado)
INSERT INTO inst_detalle_instructivo (
    id_cab_instructivo, version, id_calibre, numero_pedido, cantidad_pedido,
    id_embalaje, id_categoria, id_plu, id_etiqueta, id_pallet, altura_pallet
)
VALUES 
    (id_instructivo, 1, 1222, 1044, '500', 1172, 1056, 54, 1038, NULL, NULL),
    (id_instructivo, 1, 1223, 1044, '500', 1172, 1056, 54, 1038, NULL, NULL),
    ...;

COMMIT TRANSACTION
```

### Manejo de Errores

- Si ANY paso falla → ROLLBACK de toda la transacción
- Mensaje de error específico indica qué falló
- El usuario puede corregir y reintentar

---

## 🎨 INTERFAZ DE USUARIO

### Diseño

```
┌─────────────────────────────────────────────────────────────┐
│  📋 Crear Nuevo Instructivo de Proceso                      │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  1️⃣ Cabecera del Instructivo                               │
│  ┌───────────────────────────────────────────────────────┐ │
│  │ Exportadora | Especie | Turno | Fecha | Observación  │ │
│  └───────────────────────────────────────────────────────┘ │
│                                                             │
│  2️⃣ Pedidos (Cantidad Total por Pedido)                    │
│  ┌───────────────────────────────────────────────────────┐ │
│  │ Número | Cantidad | Prioridad | [Agregar]            │ │
│  │                                                       │ │
│  │  TABLA DE PEDIDOS AGREGADOS                           │ │
│  │  ┌────────────────────────────────────────────────┐  │ │
│  │  │ N° | Cantidad | Prioridad | [Eliminar]        │  │ │
│  │  └────────────────────────────────────────────────┘  │ │
│  └───────────────────────────────────────────────────────┘ │
│                                                             │
│  3️⃣ Detalle por Calibre (Asignar a Pedido)                 │
│  ┌───────────────────────────────────────────────────────┐ │
│  │ Calibre | Pedido | Cantidad | Embalaje | [Agregar]   │ │
│  │ Categoría | PLU | Etiqueta | Pallet | Altura | Cajas │ │
│  │                                                       │ │
│  │  TABLA DE DETALLE AGREGADO                            │ │
│  │  ┌────────────────────────────────────────────────┐  │ │
│  │  │ Calibre | Pedido | Cantidad | ... | [Eliminar]│  │ │
│  │  └────────────────────────────────────────────────┘  │ │
│  └───────────────────────────────────────────────────────┘ │
│                                                             │
│              💾 GUARDAR INSTRUCTIVO COMPLETO                │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Validaciones Visuales

- ✅ Campos obligatorios marcados
- ✅ Alertas si faltan datos antes de guardar
- ✅ Modal de confirmación muestra resumen antes de guardar
- ✅ Mensajes de éxito/error claros

---

## 🧪 CASOS DE USO

### CASO 1: Pedido único con múltiples calibres

**Escenario:** Pedido 1044 con 1000 cajas distribuidas en 5 calibres

1. Agregar pedido: 1044, cantidad 1000, prioridad 1
2. Agregar 5 registros de detalle:
   - Calibre 21 → Pedido 1044, cantidad 200
   - Calibre 23 → Pedido 1044, cantidad 200
   - Calibre 25 → Pedido 1044, cantidad 200
   - Calibre 27 → Pedido 1044, cantidad 200
   - Calibre 30 → Pedido 1044, cantidad 200
3. Guardar

**Resultado:**
- 1 registro en `inst_cab_instructivo`
- 1 registro en `inst_pedidos` (1044, 1000 cajas)
- 5 registros en `inst_detalle_instructivo` (uno por calibre)

---

### CASO 2: Múltiples pedidos con calibres compartidos

**Escenario:** 
- Pedido 1044: 500 cajas (prioridad 1)
- Pedido 1232: 300 cajas (prioridad 2)
- Mismos calibres para ambos pedidos

1. Agregar pedidos:
   - 1044, 500, prioridad 1
   - 1232, 300, prioridad 2
2. Agregar detalle:
   - Calibre 21 → Pedido 1044
   - Calibre 23 → Pedido 1044
   - Calibre 21 → Pedido 1232 (mismo calibre, diferente pedido)
   - Calibre 23 → Pedido 1232
3. Guardar

**Resultado:**
- 1 registro en `inst_cab_instructivo`
- 2 registros en `inst_pedidos`
- 4 registros en `inst_detalle_instructivo`

---

### CASO 3: Pedido con configuración específica por calibre

**Escenario:** Cada calibre tiene diferente embalaje y etiqueta

1. Agregar pedido: 1044, 1000, prioridad 1
2. Agregar detalle con configuraciones distintas:
   - Calibre 21 → Embalaje KP10PGE, Etiqueta DON PABLO
   - Calibre 23 → Embalaje KP10AGE, Etiqueta DONCELLA
   - Calibre 25 → Embalaje KP10PGE, Etiqueta DONCELLA
3. Guardar

**Resultado:** Cada calibre mantiene su configuración específica

---

## 🔗 RELACIÓN CON OTRAS PÁGINAS

### `Pedidos.php` (Existente)

- **Propósito:** Editar instructivos EXISTENTES
- **Uso:** Cuando necesitas modificar pedidos de un instructivo ya creado
- **Estado:** Se mantiene (no se elimina)

### `crear_instructivo.php` (Nueva)

- **Propósito:** Crear instructivos NUEVOS desde cero
- **Uso:** Cuando necesitas crear un instructivo completo
- **Ventaja:** Todo en una sola pantalla, sin pasos intermedios

---

## 📝 NOTAS TÉCNICAS

### Consideraciones de Diseño

1. **Versión siempre es 1:** Para instructivos nuevos, la versión es fija en 1
2. **Cantidad en detalle es opcional:** Puede ser 0 si se distribuye después
3. **Campos NULL permitidos:** Embalaje, categoría, PLU, etc. pueden ser NULL
4. **Transacción atómica:** Todo o nada, sin estados intermedios

### Compatibilidad

- ✅ SQL Server (192.168.19.4)
- ✅ ODBC Driver (consultas directas, sin prepared statements)
- ✅ Nombres de tablas con prefijo `inst_`
- ✅ Campo PK `id` en todas las tablas de configuración

---

## 🚀 PRÓXIMAS MEJORAS (OPCIONAL)

- [ ] Permitir editar pedidos después de guardar (desde Pedidos.php)
- [ ] Permitir editar detalle después de guardar
- [ ] Duplicar instructivo existente (copiar cabecera + detalle)
- [ ] Validar que cantidad total de calibres ≤ cantidad del pedido
- [ ] Reporte de instructivos creados por fecha/exportadora

---

## 📖 DOCUMENTACIÓN RELACIONADA

- `CORRECCION_NOMENCLATURA_TABLAS.md` - Nombres correctos de tablas y campos
- `FASE2_COMPLETADA.md` - Reestructuración del proyecto
- `REESTRUCTURACION_FASE2.md` - Detalles de la arquitectura MVC

---

_Hecho por Scapy 🧪 - Criatura de Laboratorio_  
_"Automatizar lo que sea necesario"_
