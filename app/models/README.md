# 📁 MODELS - Modelos de Datos

**Ubicación:** `app/models/`

## Propósito

Contiene todos los archivos que **obtienen datos de la base de datos**. Cada archivo `obtener_*.php` retorna datos en formato JSON para ser consumidos por el frontend.

## Archivos

| Archivo | Descripción |
|---------|-------------|
| `obtener_altura_pallet.php` | Obtiene alturas de pallet |
| `obtener_calibres.php` | Obtiene calibres de fruta |
| `obtener_categoria.php` | Obtiene categorías |
| `obtener_destinos.php` | Obtiene destinos |
| `obtener_embalajes.php` | Obtiene tipos de embalaje |
| `obtener_etiquetas.php` | Obtiene etiquetas |
| `obtener_exportadoras.php` | Obtiene exportadoras |
| `obtener_pallets.php` | Obtiene configuración de pallets |
| `obtener_plus.php` | Obtiene PLU |
| `obtener_instructivo.php` | Obtiene instructivo específico |
| `obtener_instructivos.php` | Lista todos los instructivos |
| `obtener_version.php` | Obtiene versión de instructivo |
| `obtener_versiones.php` | Lista versiones de instructivo |
| `obtener_pedidos_existentes.php` | Obtiene pedidos existentes |
| `obtener_pedidos_posibles.php` | Obtiene pedidos posibles |
| `obtener_cabecera_por_version.php` | Obtiene cabecera por versión |
| `obtener_detalle_por_version.php` | Obtiene detalle por versión |
| `obtener_listas_detalle.php` | Obtiene listas de detalle |
| `obtener_embalaje_por_id.php` | Obtiene embalaje por ID |

## Uso

```javascript
// Desde el frontend
fetch("../models/obtener_calibres.php")
    .then(response => response.json())
    .then(data => {
        // Usar datos
    });
```

```php
// Desde PHP
require_once '../models/obtener_calibres.php';
```

## Convenciones

- Todos los archivos retornan JSON
- Usan `conexion.php` para conexión a BD
- No procesan datos, solo los obtienen
- Nombres descriptivos: `obtener_[recurso].php`
