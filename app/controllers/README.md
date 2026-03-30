# 🎮 CONTROLLERS - Controladores de Acciones

**Ubicación:** `app/controllers/`

## Propósito

Contiene todos los archivos que **procesan acciones del usuario** (guardar, modificar, eliminar, listar). Reciben datos del frontend, los validan y ejecutan operaciones en la base de datos.

## Archivos

### Configuración
| Archivo | Descripción |
|---------|-------------|
| `procesar_calibre.php` | Procesa acciones de calibres |
| `procesar_categoria.php` | Procesa acciones de categorías |
| `procesar_destino.php` | Procesa acciones de destinos |
| `procesar_embalaje.php` | Procesa acciones de embalajes |
| `procesar_etiqueta.php` | Procesa acciones de etiquetas |
| `procesar_exportadora.php` | Procesa acciones de exportadoras |
| `procesar_pallet.php` | Procesa acciones de pallets |
| `procesar_plu.php` | Procesa acciones de PLU |
| `procesar_altura_pallet.php` | Procesa acciones de altura de pallet |

### Instructivos
| Archivo | Descripción |
|---------|-------------|
| `procesar_instructivo.php` | Procesa creación/edición de instructivos |
| `procesar_nueva_version.php` | Procesa nueva versión de instructivo |
| `procesar_detalle_instructivo.php` | Procesa detalle de instructivo |
| `procesar_edicion.php` | Procesa edición de instructivo |
| `procesar_etiquetas.php` | Procesa etiquetas de instructivo |

### Otros
| Archivo | Descripción |
|---------|-------------|
| `guardar_pedidos.php` | Guarda pedidos |
| `listar_embalaje.php` | Lista embalajes |
| `listar_altura_pallet.php` | Lista alturas de pallet |
| `eliminar_altura_pallet.php` | Elimina altura de pallet |
| `modificar_especies.php` | Modifica especies |

## Uso

```javascript
// Desde el frontend
const formData = new FormData();
formData.append('accion', 'guardar');
formData.append('codigo', 'ABC123');

fetch("../controllers/procesar_calibre.php", {
    method: "POST",
    body: formData
})
.then(response => response.text())
.then(data => alert(data));
```

## Convenciones

- Reciben método POST
- Usan parámetro `accion` para determinar operación (guardar, modificar, eliminar)
- Retornan mensajes de texto (éxito/error)
- Validan datos antes de operar
- Usan `conexion.php` para conexión a BD

## Estructura Típica

```php
<?php
require_once("../conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    switch ($accion) {
        case 'guardar':
            guardar($conn);
            break;
        case 'modificar':
            modificar($conn);
            break;
        case 'eliminar':
            eliminar($conn);
            break;
    }
}

function guardar($conn) {
    // Lógica de guardado
}

function modificar($conn) {
    // Lógica de modificación
}

function eliminar($conn) {
    // Lógica de eliminación
}
```
