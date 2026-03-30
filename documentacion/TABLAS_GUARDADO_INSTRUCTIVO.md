# 📊 TABLAS DONDE SE GUARDA EL INSTRUCTIVO

## RESUMEN

Al crear un instructivo, se guarda en **3 tablas**:

1. **inst_cab_instructivo** → Cabecera (1 registro)
2. **inst_pedidos** → Pedidos (1 registro por pedido)
3. **inst_detalle_instructivo** → Detalle por calibre (1 registro por calibre)

---

## 1️⃣ inst_cab_instructivo (CABECERA)

**Propósito:** Datos generales del instructivo

**Campos:**
```sql
id_instructivo  (INT, PK, AUTOINCREMENT)
fecha           (DATE)
id_exportadora  (INT, FK → inst_exportadora.id)
id_especie      (INT, FK → especie.id_especie)
turno           (NCHAR)
observacion     (NVARCHAR)
```

**Ejemplo de registro:**
```
id_instructivo: 1324
fecha: 2026-03-27
id_exportadora: 7 (AGUA SANTA)
id_especie: 18 (KIWI)
turno: Turno 1
observacion: Instructivo de prueba
```

**Cantidad:** 1 registro por instructivo

---

## 2️⃣ inst_pedidos (PEDIDOS)

**Propósito:** Números de pedido con cantidad total y prioridad

**Campos:**
```sql
id_pedido       (INT, PK, AUTOINCREMENT)
id_instructivo  (INT, FK → inst_cab_instructivo.id_instructivo)
version         (INT)
numero_pedido   (INT)
cantidad        (NVARCHAR)
prioridad       (INT)
```

**Ejemplo de registros:**
```
id_pedido: 1701 | id_instructivo: 1324 | version: 1 | numero_pedido: 1044 | cantidad: 1000 | prioridad: 1
id_pedido: 1702 | id_instructivo: 1324 | version: 1 | numero_pedido: 1232 | cantidad: 500  | prioridad: 2
```

**Cantidad:** 1 registro por pedido agregado

---

## 3️⃣ inst_detalle_instructivo (DETALLE POR CALIBRE)

**Propósito:** Configuración específica para CADA calibre

**Campos:**
```sql
id                (INT, PK, AUTOINCREMENT)
id_cab_instructivo (INT, FK → inst_cab_instructivo.id_instructivo)
version            (INT)
id_calibre         (INT, FK → inst_calibre.id)
numero_pedido      (INT)
cantidad_pedido    (NVARCHAR)
id_embalaje        (INT, FK → inst_embalaje.id)
id_categoria       (INT, FK → inst_categoria.id)
id_plu             (INT, FK → inst_plu.id)
id_etiqueta        (INT, FK → inst_etiqueta.id)
id_pallet          (INT, FK → inst_pallet.id)
altura_pallet      (INT, FK → inst_altura_pallet.id)  ← ¡OJO! Es "altura_pallet", NO "id_altura_pallet"
```

**Ejemplo de registros (mismo instructivo, 3 calibres):**
```
id: 10283 | id_cab_instructivo: 1324 | version: 1 | id_calibre: 1222 | numero_pedido: 1044 | cantidad_pedido: 1000 | id_embalaje: 1172 | id_categoria: 1056 | id_plu: 54 | id_etiqueta: 1038 | id_pallet: 2 | id_altura_pallet: 5
id: 10284 | id_cab_instructivo: 1324 | version: 1 | id_calibre: 1223 | numero_pedido: 1044 | cantidad_pedido: 1000 | id_embalaje: 1172 | id_categoria: 1056 | id_plu: 54 | id_etiqueta: 1038 | id_pallet: 2 | id_altura_pallet: 5
id: 10285 | id_cab_instructivo: 1324 | version: 1 | id_calibre: 1225 | numero_pedido: 1044 | cantidad_pedido: 1000 | id_embalaje: 1172 | id_categoria: 1056 | id_plu: 54 | id_etiqueta: 1038 | id_pallet: 2 | id_altura_pallet: 5
```

**Cantidad:** 1 registro por **cada calibre** seleccionado

---

## 🔗 RELACIONES ENTRE TABLAS

```
inst_cab_instructivo (1)
    │
    ├──→ inst_pedidos (N)
    │     └── Múltiples pedidos por instructivo
    │
    └──→ inst_detalle_instructivo (N)
          └── Múltiples calibres por instructivo
```

---

## 📝 EJEMPLO COMPLETO

**Usuario crea instructivo con:**
- Exportadora: AGUA SANTA
- Especie: KIWI
- Turno: Turno 1
- Pedidos: 1044 (1000 cajas), 1232 (500 cajas)
- Calibres: 21, 23, 25 (todos con misma configuración)

**Resultado en BD:**

### inst_cab_instructivo
| id_instructivo | fecha | id_exportadora | id_especie | turno | observacion |
|----------------|-------|----------------|------------|-------|-------------|
| 1324 | 2026-03-27 | 7 | 18 | Turno 1 | Prueba |

### inst_pedidos
| id_pedido | id_instructivo | version | numero_pedido | cantidad | prioridad |
|-----------|----------------|---------|---------------|----------|-----------|
| 1701 | 1324 | 1 | 1044 | 1000 | 1 |
| 1702 | 1324 | 1 | 1232 | 500 | 2 |

### inst_detalle_instructivo
| id | id_cab_instructivo | version | id_calibre | numero_pedido | cantidad_pedido | id_embalaje | id_categoria | id_plu | id_etiqueta | id_pallet | id_altura_pallet |
|----|-------------------|---------|------------|---------------|-----------------|-------------|--------------|--------|-------------|-----------|-----------------|
| 10283 | 1324 | 1 | 1222 | 1044 | 1000 | 1172 | 1056 | 54 | 1038 | 2 | 5 |
| 10284 | 1324 | 1 | 1223 | 1044 | 1000 | 1172 | 1056 | 54 | 1038 | 2 | 5 |
| 10285 | 1324 | 1 | 1225 | 1044 | 1000 | 1172 | 1056 | 54 | 1038 | 2 | 5 |

---

## 🔍 CONSULTAS PARA VERIFICAR

### Ver instructivo creado
```sql
SELECT * FROM inst_cab_instructivo ORDER BY id_instructivo DESC
```

### Ver pedidos de un instructivo
```sql
SELECT * FROM inst_pedidos 
WHERE id_instructivo = 1324 
ORDER BY numero_pedido
```

### Ver detalle de un instructivo
```sql
SELECT 
    det.id,
    cal.cod_calibre,
    det.numero_pedido,
    det.cantidad_pedido,
    emb.Codigo_emb,
    cat.nombre_categoria,
    plu.plu,
    etq.Nombre_etiqueta,
    pal.Descrip_pallet,
    alt.altura
FROM inst_detalle_instructivo det
LEFT JOIN inst_calibre cal ON det.id_calibre = cal.id
LEFT JOIN inst_embalaje emb ON det.id_embalaje = emb.id
LEFT JOIN inst_categoria cat ON det.id_categoria = cat.id
LEFT JOIN inst_plu plu ON det.id_plu = plu.id
LEFT JOIN inst_etiqueta etq ON det.id_etiqueta = etq.id
LEFT JOIN inst_pallet pal ON det.id_pallet = pal.id
LEFT JOIN inst_altura_pallet alt ON det.id_altura_pallet = alt.id
WHERE det.id_cab_instructivo = 1324
ORDER BY cal.cod_calibre
```

---

## ⚠️ CAMPO ELIMINADO

El campo `cajas` **NO se guarda** porque:
1. No existe en la tabla `inst_detalle_instructivo`
2. El usuario pidió eliminarlo del formulario
3. La información de cajas ya está en `inst_altura_pallet.cajas`

---

_Hecho por Scapy 🧪 - Criatura de Laboratorio_
