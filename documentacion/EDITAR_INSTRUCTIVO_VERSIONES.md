# ✏️ EDITAR INSTRUCTIVO - CREAR NUEVAS VERSIONES (V2, V3, V4...)

**Fecha:** 27 Marzo 2026  
**Funcionalidad:** Editar instructivos existentes y crear nuevas versiones

---

## 🎯 OBJETIVO

Permitir a los usuarios:

1. **Buscar** instructivos existentes por exportadora, especie y fecha
2. **Cargar** un instructivo con TODA su información (cabecera + pedidos + detalle)
3. **Editar** cualquier campo:
   - Modificar cabecera (exportadora, especie, fecha, turno, observación)
   - Agregar/eliminar pedidos
   - Agregar/eliminar detalles (calibres con configuración)
4. **Guardar** como nueva versión automáticamente (V2, V3, V4...)

---

## 📊 FLUJO DE TRABAJO

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Usuario va a "Editar Instructivo"                        │
│    - Menu: Procesos → Editar Instructivo                    │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Busca instructivo por:                                   │
│    - Exportadora                                            │
│    - Especie                                                │
│    - Rango de fechas                                        │
│    - Click en "Buscar"                                      │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. Sistema muestra lista de instructivos:                   │
│    ┌────┬─────────────┬───────────┬──────────┬───────────┐  │
│    │ ID │ Exportadora │ Especie   │ Fecha    │ Versiones │  │
│    ├────┼─────────────┼───────────┼──────────┼───────────┤  │
│    │1324│ AGUA SANTA  │ KIWI      │2026-03-27│   V2      │  │
│    └────┴─────────────┴───────────┴──────────┴───────────┘  │
│    - Botón "✏️ Editar / Crear Versión"                       │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. Sistema carga instructivo COMPLETO:                      │
│    - Cabecera (exportadora, especie, fecha, turno, obs)     │
│    - Pedidos (número, cantidad, prioridad)                  │
│    - Detalle (calibres + configuración)                     │
│    - Muestra versión actual: "Versión 2"                    │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. Usuario EDITA lo que necesite:                           │
│    ✅ Cambiar fecha                                         │
│    ✅ Agregar nuevo pedido                                  │
│    ✅ Eliminar pedido existente                             │
│    ✅ Agregar nuevo calibre con configuración               │
│    ✅ Eliminar calibre existente                            │
│    ✅ Modificar observaciones                               │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│ 6. Usuario guarda:                                          │
│    - Click en "💾 Guardar como Versión 3"                   │
│    - Sistema valida datos                                   │
│    - Confirma: "¿Crear Versión 3 del Instructivo 1324?"     │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│ 7. Sistema guarda NUEVA VERSIÓN:                            │
│    - Mismo id_instructivo: 1324                             │
│    - Nueva versión: 3                                       │
│    - Nueva cabecera en inst_cab_instructivo                 │
│    - Nuevos pedidos en inst_pedidos (version=3)             │
│    - Nuevo detalle en inst_detalle_instructivo (version=3)  │
│    - Mensaje: "✅ Versión 3 creada exitosamente"            │
└─────────────────────────────────────────────────────────────┘
```

---

## 🗄️ ESTRUCTURA DE DATOS

### Tablas Involucradas

```
inst_cab_instructivo
├── id_instructivo (PK) ← MISMO número
├── id_exportadora
├── id_especie
├── fecha ← NUEVA fecha
├── turno
└── observacion

inst_pedidos
├── id_pedido (PK) ← NUEVO ID
├── id_instructivo ← MISMO número (ej: 1324)
├── version ← NUEVA versión (ej: 3)
├── numero_pedido
├── cantidad
└── prioridad

inst_detalle_instructivo
├── id (PK) ← NUEVO ID
├── id_cab_instructivo ← MISMO número (ej: 1324)
├── version ← NUEVA versión (ej: 3)
├── numero_pedido
├── cantidad_pedido
├── id_calibre
├── id_embalaje
├── id_categoria
├── id_plu
├── id_etiqueta
├── id_pallet
├── altura_pallet
├── id_destino
├── var_etiquetada
└── observacion
```

### Ejemplo de Versiones Múltiples

**Instructivo #1324:**

| Versión | Fecha | Exportadora | Especie | Pedidos | Detalle |
|---------|-------|-------------|---------|---------|---------|
| V1 | 2026-03-27 | AGUA SANTA | KIWI | 1044, 1045 | 8 calibres |
| V2 | 2026-03-28 | AGUA SANTA | KIWI | 1044, 1045, 1046 | 10 calibres |
| V3 | 2026-03-29 | AGUA SANTA | KIWI | 1044, 1046 | 6 calibres |

**Cada versión es INDEPENDIENTE:**
- ✅ Mismo número de instructivo (1324)
- ✅ Diferente versión (1, 2, 3)
- ✅ Diferente fecha
- ✅ Diferente cantidad de pedidos
- ✅ Diferente cantidad de calibres

---

## 📁 ARCHIVOS CREADOS

### Backend

| Archivo | Función |
|---------|---------|
| `app/models/obtener_instructivo_para_edicion.php` | Carga instructivo completo (cab + ped + det) |
| `app/models/obtener_instructivos.php` | Lista instructivos con filtros (actualizado) |
| `app/controllers/guardar_nueva_version.php` | Guarda nueva versión con transacción |

### Frontend

| Archivo | Función |
|---------|---------|
| `app/Procesos/editar_instructivo.php` | Pantalla de edición (HTML) |
| `app/assets/js/editar_instructivo.js` | Lógica frontend (carga, edición, guardado) |

### UI Actualizada

| Archivo | Cambio |
|---------|--------|
| `app/inicio.php` | Agregado botón "Editar Instructivo" |

---

## 🔧 CARACTERÍSTICAS IMPLEMENTADAS

### 1. Búsqueda con Filtros ✅
- Filtrar por exportadora
- Filtrar por especie
- Filtrar por rango de fechas
- Botón "Limpiar" para resetear filtros

### 2. Lista de Instructivos ✅
- Muestra ID, exportadora, especie, fecha, turno
- Badge con versión más reciente (V1, V2, V3...)
- Botón "Editar / Crear Versión"

### 3. Carga Completa de Datos ✅
- Cabecera: todos los campos pre-llenados
- Pedidos: tabla con pedidos existentes
- Detalle: tabla con calibres y configuración
- Cinta de calibres: muestra todos los calibres únicos

### 4. Edición Completa ✅
- **Cabecera:** Editar exportadora, especie, fecha, turno, observación
- **Pedidos:** Agregar nuevos, eliminar existentes
- **Detalle:** Agregar nuevos calibres, eliminar existentes
- **Combos dependientes:** Se recargan según selección

### 5. Guardado como Nueva Versión ✅
- Calcula automáticamente: `nueva_version = version_actual + 1`
- Botón muestra: "💾 Guardar como Versión 3"
- Confirmación antes de guardar
- Transacción SQL para integridad
- Mensaje de éxito/error

### 6. Validaciones ✅
- Exportadora y especie obligatorias
- Al menos un pedido
- Al menos un detalle
- Calibres obligatorios en cada detalle

---

## 🧪 PRUEBAS

### URL de Prueba
```
http://localhost/instructivo/app/Procesos/editar_instructivo.php
```

### Escenario 1: Crear Versión 2
1. Ir a `editar_instructivo.php`
2. Buscar instructivo 1324 (versión 1)
3. Click en "✏️ Editar / Crear Versión"
4. Sistema carga datos de versión 1
5. Agregar nuevo pedido: 1046, 500 cajas, prioridad 2
6. Agregar nuevo calibre: 25 con configuración
7. Click en "💾 Guardar como Versión 2"
8. Verificar mensaje: "✅ Versión 2 creada exitosamente"
9. Verificar en BD:
   ```sql
   SELECT version, COUNT(*) as pedidos 
   FROM inst_pedidos 
   WHERE id_instructivo = 1324 
   GROUP BY version
   -- Debería mostrar: V1=2 pedidos, V2=3 pedidos
   ```

### Escenario 2: Crear Versión 3
1. Buscar instructivo 1324 (ahora tiene V1 y V2)
2. Click en "Editar"
3. Sistema carga ÚLTIMA versión (V2)
4. Eliminar pedido 1045
5. Eliminar calibre 23
6. Click en "💾 Guardar como Versión 3"
7. Verificar mensaje de éxito
8. Verificar en BD:
   ```sql
   SELECT version, COUNT(*) as detalle 
   FROM inst_detalle_instructivo 
   WHERE id_cab_instructivo = 1324 
   GROUP BY version
   -- Debería mostrar: V1=8, V2=10, V3=6
   ```

### Escenario 3: Modificar Cabecera
1. Cargar instructivo existente
2. Cambiar fecha
3. Cambiar turno (Día → Tarde)
4. Agregar observación: "Versión con ajuste de fecha"
5. Guardar como nueva versión
6. Verificar que nueva cabecera tiene los cambios

---

## 🔍 QUERIES DE VERIFICACIÓN

### Ver versiones de un instructivo
```sql
SELECT 
    cab.id_instructivo,
    cab.version,
    cab.fecha,
    cab.turno,
    cab.observacion,
    COUNT(DISTINCT ped.id_pedido) as total_pedidos,
    COUNT(DISTINCT det.id) as total_detalle
FROM inst_cab_instructivo cab
LEFT JOIN inst_pedidos ped ON ped.id_instructivo = cab.id_instructivo AND ped.version = cab.version
LEFT JOIN inst_detalle_instructivo det ON det.id_cab_instructivo = cab.id_instructivo AND det.version = cab.version
WHERE cab.id_instructivo = 1324
GROUP BY cab.id_instructivo, cab.version, cab.fecha, cab.turno, cab.observacion
ORDER BY cab.version ASC
```

### Comparar dos versiones
```sql
-- Versión 1
SELECT 'V1' as version, numero_pedido, cantidad, prioridad
FROM inst_pedidos
WHERE id_instructivo = 1324 AND version = 1
UNION ALL
-- Versión 2
SELECT 'V2' as version, numero_pedido, cantidad, prioridad
FROM inst_pedidos
WHERE id_instructivo = 1324 AND version = 2
ORDER BY version, prioridad
```

### Ver detalle con calibres por versión
```sql
SELECT 
    det.version,
    det.numero_pedido,
    cal.cod_calibre,
    emb.Codigo_emb as embalaje,
    cat.cod_categoria as categoria,
    det.cantidad_pedido
FROM inst_detalle_instructivo det
LEFT JOIN inst_calibre cal ON cal.id = det.id_calibre
LEFT JOIN inst_embalaje emb ON emb.id = det.id_embalaje
LEFT JOIN inst_categoria cat ON cat.id = det.id_categoria
WHERE det.id_cab_instructivo = 1324
ORDER BY det.version, det.numero_pedido, cal.cod_calibre
```

---

## ⚠️ CONSIDERACIONES IMPORTANTES

### 1. Mismo ID, Diferente Versión
- **NO** se crea nuevo `id_instructivo`
- **SÍ** se usa el mismo número con diferente `version`
- Esto permite rastrear el historial de cambios

### 2. Independencia de Versiones
- Cada versión es INDEPENDIENTE
- Modificar V2 NO afecta V1
- Modificar V3 NO afecta V2
- Cada versión tiene sus propios pedidos y detalle

### 3. Fecha de Cabecera
- La fecha se actualiza a la fecha de creación de la nueva versión
- Permite saber cuándo se creó cada versión

### 4. Transacción SQL
- Todo el guardado está en transacción
- Si falla algo, se hace rollback completo
- Garantiza integridad de datos

### 5. No Hay "Edición" de Versión Existente
- **NO** se puede modificar V1 directamente
- **SÍ** se crea V2 basada en V1
- Esto preserva el historial original

---

## 🚀 PRÓXIMAS MEJORAS (OPCIONAL)

- [ ] Comparar dos versiones lado a lado
- [ ] Ver historial completo de versiones
- [ ] Restaurar versión anterior (crear V4 basada en V1)
- [ ] Exportar versión específica a Excel
- [ ] Ver diferencias entre versiones (diff)
- [ ] Comentario/justificación al crear nueva versión

---

**URL de Acceso:**
```
http://localhost/instructivo/app/Procesos/editar_instructivo.php
```

**Estado:** ✅ IMPLEMENTADO Y LISTO PARA PRUEBAS
