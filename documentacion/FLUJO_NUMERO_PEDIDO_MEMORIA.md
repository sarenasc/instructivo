# 🔄 FLUJO DE DATOS: NÚMERO DE PEDIDO EN MEMORIA VS BASE DE DATOS

## 📊 DIAGRAMA DEL FLUJO COMPLETO

```
┌─────────────────────────────────────────────────────────────────────┐
│                    1. USUARIO AGREGA PEDIDO                         │
│                                                                     │
│  ┌──────────────┐      ┌─────────────────────────────────────┐     │
│  │ Número: 1044 │      │ JavaScript: pedidosEdit = []        │     │
│  │ Cantidad: 500│ ───► │                                     │     │
│  │ Prioridad: 1 │      │ [{numero_pedido: "1044",            │     │
│  └──────────────┘      │   cantidad: 500,                    │     │
│         │              │   prioridad: 1}]                    │     │
│         │              └─────────────────────────────────────┘     │
│         │                        │                                 │
│         │                        │ push()                          │
│         ▼                        ▼                                 │
│  ┌───────────────────────────────────────────────────────────┐     │
│  │  renderPedidosEdit()                                      │     │
│  │  - Dibuja tabla en pantalla                               │     │
│  │  - Muestra pedido 1044                                    │     │
│  └───────────────────────────────────────────────────────────┘     │
│                                                                     │
│  ┌───────────────────────────────────────────────────────────┐     │
│  │  actualizarSelectNumeroPedido()                           │     │
│  │  - Lee pedidosEdit (memoria)                              │     │
│  │  - Extrae números únicos: ["1044"]                        │     │
│  │  - Llena SELECT del formulario Detalle                    │     │
│  └───────────────────────────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    2. USUARIO AGREGA DETALLE                        │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────┐       │
│  │  Formulario "Detalle por Calibre"                       │       │
│  │                                                         │       │
│  │  Número Pedido: [1044 ▼]  ← SELECT (lee de memoria)    │       │
│  │  Calibres:        [42, 48, 56]                          │       │
│  │  Embalaje:        [Caja 5kg]                            │       │
│  │  Cantidad:        [500   ]                              │       │
│  │                                                         │       │
│  │  [➕ Agregar]                                           │       │
│  └─────────────────────────────────────────────────────────┘       │
│         │                                                           │
│         │ click                                                     │
│         ▼                                                           │
│  ┌───────────────────────────────────────────────────────────┐     │
│  │  agregarDetalle()                                         │     │
│  │                                                           │     │
│  │  const numeroPedido = document                            │     │
│  │    .getElementById('edit_numero_pedido_detalle')          │     │
│  │    .value;  // ← Lee del SELECT (valor: "1044")           │     │
│  │                                                           │     │
│  │  detalleEdit.push({                                       │     │
│  │    numero_pedido: numeroPedido,  // "1044"                │     │
│  │    cantidad: 500,                                         │     │
│  │    calibres: [...],                                       │     │
│  │    ...                                                    │     │
│  │  });                                                      │     │
│  └───────────────────────────────────────────────────────────┘     │
│                                                                     │
│  ┌───────────────────────────────────────────────────────────┐     │
│  │  detalleEdit (memoria) = [                                │     │
│  │    {                                                      │     │
│  │      numero_pedido: "1044",                               │     │
│  │      cantidad: 500,                                       │     │
│  │      calibres: [{id: 42, texto: "42"}],                   │     │
│  │      id_embalaje: 5,                                      │     │
│  │      ...                                                  │     │
│  │    }                                                      │     │
│  │  ]                                                        │     │
│  └───────────────────────────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    3. USUARIO GUARDA INSTRUCTIVO                    │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────┐       │
│  │  [💾 Guardar Nueva Versión]                             │       │
│  │         │                                               │       │
│  │         │ click                                         │       │
│  │         ▼                                               │       │
│  │  guardarNuevaVersion()                                  │       │
│  │                                                         │       │
│  │  fetch('../controllers/guardar_nueva_version.php', {   │       │
│  │    method: 'POST',                                      │       │
│  │    body: JSON.stringify({                               │       │
│  │      id_instructivo: 123,                               │       │
│  │      version_anterior: 1,                               │       │
│  │      cabecera: {...},                                   │       │
│  │      pedidos: pedidosEdit,  // ← ENVÍA MEMORIA          │       │
│  │      detalle: detalleEdit     // ← ENVÍA MEMORIA        │       │
│  │    })                                                   │       │
│  │  })                                                     │       │
│  └─────────────────────────────────────────────────────────┘       │
└─────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    4. BACKEND GUARDA EN BD                          │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────┐       │
│  │  guardar_nueva_version.php                              │       │
│  │                                                         │       │
│  │  $data = json_decode(file_get_contents('php://input'));│       │
│  │  $pedidos = $data['pedidos'];  // ← RECIBE DE MEMORIA  │       │
│  │  $detalle = $data['detalle'];  // ← RECIBE DE MEMORIA  │       │
│  │                                                         │       │
│  │  // INSERTAR PEDIDOS                                    │       │
│  │  foreach ($pedidos as $pedido) {                        │       │
│  │    sqlsrv_query($conn, "                                │       │
│  │      INSERT INTO inst_pedidos                           │       │
│  │      (id_instructivo, version, numero_pedido, cantidad) │       │
│  │      VALUES (?, ?, ?, ?)",                              │       │
│  │      [$id_instructivo, $nueva_version,                  │       │
│  │       $pedido['numero_pedido'], $pedido['cantidad']]    │       │
│  │    );                                                   │       │
│  │  }                                                      │       │
│  │                                                         │       │
│  │  // INSERTAR DETALLE                                    │       │
│  │  foreach ($detalle as $det) {                           │       │
│  │    sqlsrv_query($conn, "                                │       │
│  │      INSERT INTO inst_detalle_instructivo               │       │
│  │      (id_cab_instructivo, version, numero_pedido, ...)  │       │
│  │      VALUES (?, ?, ?, ...)",                            │       │
│  │      [$id_instructivo, $nueva_version,                  │       │
│  │       $det['numero_pedido'], ...]                       │       │
│  │    );                                                   │       │
│  │  }                                                      │       │
│  └─────────────────────────────────────────────────────────┘       │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────┐       │
│  │  BASE DE DATOS (SQL Server)                             │       │
│  │                                                         │       │
│  │  inst_pedidos:                                          │       │
│  │  ┌─────────┬───────────┬────────────────┬──────────┐   │       │
│  │  │id_pedido│id_instruct│version         │numero    │   │       │
│  │  ├─────────┼───────────┼────────────────┼──────────┤   │       │
│  │  │  1001   │   123     │   2            │  1044    │   │       │
│  │  │  1002   │   123     │   2            │  1045    │   │       │
│  │  └─────────┴───────────┴────────────────┴──────────┘   │       │
│  │                                                         │       │
│  │  inst_detalle_instructivo:                              │       │
│  │  ┌─────┬─────────┬─────────┬──────────────┬──────────┐ │       │
│  │  │ id  │id_cab   │version  │numero_pedido │calibre   │ │       │
│  │  ├─────┼─────────┼─────────┼──────────────┼──────────┤ │       │
│  │  │5001 │  123    │   2     │   1044       │   42     │ │       │
│  │  │5002 │  123    │   2     │   1044       │   48     │ │       │
│  │  └─────┴─────────┴─────────┴──────────────┴──────────┘ │       │
│  └─────────────────────────────────────────────────────────┘       │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🔑 PUNTOS CLAVE

### 1️⃣ **MEMORIA TEMPORAL (JavaScript)**

**Variables globales en `editar_instructivo.js`:**
```javascript
let pedidosEdit = [];   // ← Almacena pedidos EN MEMORIA
let detalleEdit = [];   // ← Almacena detalle EN MEMORIA
```

**Estos arrays:**
- ✅ Existen solo en el navegador del usuario
- ✅ Se pueden modificar libremente (agregar, editar, eliminar)
- ✅ NO están en la base de datos todavía
- ✅ Se pierden si recargas la página (antes de guardar)

---

### 2️⃣ **SELECT LEE DE MEMORIA**

**Función `actualizarSelectNumeroPedido()`:**
```javascript
function actualizarSelectNumeroPedido() {
    const selectAgregar = document.getElementById('edit_numero_pedido_detalle');
    
    // 1. Limpiar select
    selectAgregar.innerHTML = '<option value="">Seleccione...</option>';
    
    // 2. Leer de memoria (pedidosEdit)
    const pedidosUnicos = [...new Set(pedidosEdit.map(p => p.numero_pedido))];
    // Ejemplo: ["1044", "1045", "1046"]
    
    // 3. Crear opciones
    pedidosUnicos.forEach(numero => {
        const opt = document.createElement('option');
        opt.value = numero;
        opt.textContent = numero;
        selectAgregar.appendChild(opt);
    });
}
```

**¿Cuándo se llama?**
- ✅ Después de `agregarPedido()` → agrega nuevo número al select
- ✅ Después de `eliminarPedido()` → quita número del select
- ✅ Después de `cargarInstructivo()` → inicializa select con datos existentes

---

### 3️⃣ **AGREGAR DETALLE LEE DEL SELECT**

**Función `agregarDetalle()`:**
```javascript
function agregarDetalle() {
    // ← Lee del SELECT (que fue llenado desde memoria)
    const numeroPedido = document.getElementById('edit_numero_pedido_detalle').value;
    // Ejemplo: "1044"
    
    const cantidad = document.getElementById('edit_cantidad_detalle').value;
    
    // ← Guarda en memoria (detalleEdit)
    detalleEdit.push({
        numero_pedido: numeroPedido,  // "1044"
        cantidad: parseInt(cantidad),
        calibres: calibresSeleccionados,
        id_embalaje: parseInt(embalajeSelect.value) || null,
        // ... más campos
    });
}
```

---

### 4️⃣ **GUARDAR ENVÍA MEMORIA AL BACKEND**

**Función `guardarNuevaVersion()`:**
```javascript
async function guardarNuevaVersion() {
    // ← Prepara datos desde memoria
    const payload = {
        id_instructivo: instructivoActual,
        version_anterior: versionActual,
        cabecera: {
            id_exportadora: document.getElementById('edit_exportadora').value,
            id_especie: document.getElementById('edit_especie').value,
            fecha: document.getElementById('edit_fecha').value,
            turno: document.getElementById('edit_turno').value,
            observacion: document.getElementById('edit_observacion').value
        },
        pedidos: pedidosEdit,  // ← MEMORIA → BACKEND
        detalle: detalleEdit   // ← MEMORIA → BACKEND
    };
    
    // ← Envía al backend
    const response = await fetch('../controllers/guardar_nueva_version.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
    });
}
```

---

### 5️⃣ **BACKEND GUARDA EN BASE DE DATOS**

**Archivo `guardar_nueva_version.php`:**
```php
<?php
$data = json_decode(file_get_contents('php://input'), true);

$pedidos = $data['pedidos'];  // ← RECIBE desde JavaScript
$detalle = $data['detalle'];  // ← RECIBE desde JavaScript

// INSERTAR PEDIDOS EN BD
foreach ($pedidos as $pedido) {
    sqlsrv_query($conn, "
        INSERT INTO inst_pedidos 
        (id_instructivo, version, numero_pedido, cantidad, prioridad)
        VALUES (?, ?, ?, ?, ?)
    ", [
        $id_instructivo,
        $nueva_version,
        $pedido['numero_pedido'],  // ← "1044" desde memoria
        $pedido['cantidad'],
        $pedido['prioridad']
    ]);
}

// INSERTAR DETALLE EN BD
foreach ($detalle as $det) {
    sqlsrv_query($conn, "
        INSERT INTO inst_detalle_instructivo 
        (id_cab_instructivo, version, numero_pedido, cantidad_pedido, ...)
        VALUES (?, ?, ?, ?, ...)
    ", [
        $id_instructivo,
        $nueva_version,
        $det['numero_pedido'],  // ← "1044" desde memoria
        $det['cantidad'],
        // ... más campos
    ]);
}
```

---

## 📝 RESUMEN DEL CICLO DE VIDA

| Etapa | ¿Dónde están los datos? | ¿Se pueden modificar? |
|-------|------------------------|----------------------|
| 1. Usuario agrega pedido | `pedidosEdit[]` (memoria) | ✅ Sí (agregar/editar/eliminar) |
| 2. Usuario agrega detalle | `detalleEdit[]` (memoria) | ✅ Sí (agregar/editar/eliminar) |
| 3. Select muestra números | Lee de `pedidosEdit[]` | ✅ Se actualiza automáticamente |
| 4. Usuario guarda | Se envían al backend | ❌ Ya no se pueden modificar |
| 5. Backend procesa | INSERT en SQL Server | ❌ Guardado en BD |
| 6. Página recargada | Lee desde BD | ❌ Solo lectura (hasta editar) |

---

## 🎯 CONCLUSIÓN

**El SELECT de "Número Pedido" NO lee de la base de datos.**

Lee del array `pedidosEdit[]` que está en **memoria temporal del navegador**.

**Ventajas:**
- ✅ Rápido (no necesita consulta a BD)
- ✅ Inmediato (se actualiza al instante)
- ✅ Flexible (puedes agregar/eliminar sin guardar)

**Desventajas:**
- ❌ Temporal (se pierde si recargas)
- ❌ Solo existe en el navegador del usuario actual
- ❌ Necesita guardar para persistir en BD

---

_Última actualización: 27 Mar 2026 18:00_
