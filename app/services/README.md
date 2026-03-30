# 🔌 SERVICES - Servicios/API

**Ubicación:** `app/services/`

## Propósito

Contiene archivos que proveen **servicios o APIs** para consumo del frontend o sistemas externos. Similar a los models pero con lógica de negocio adicional o formatos específicos.

## Archivos

| Archivo | Descripción |
|---------|-------------|
| `api_altura_pallet.php` | API de alturas de pallet |
| `api_alturas_pallet.php` | API múltiple de alturas de pallet |
| `api_calibre.php` | API de calibres |
| `api_categoria.php` | API de categorías |
| `api_destino.php` | API de destinos |
| `api_embalaje.php` | API de embalajes |
| `api_especies.php` | API de especies |
| `api_etiqueta.php` | API de etiquetas |
| `api_exportadora.php` | API de exportadoras |
| `api_instructivo_combobox.php` | API para combobox de instructivo |

## Uso

```javascript
// Desde el frontend
fetch("../services/api_especies.php")
    .then(response => response.json())
    .then(data => {
        // Llenar combobox
        data.forEach(especie => {
            select.innerHTML += `<option value="${especie.id}">${especie.nombre}</option>`;
        });
    });
```

## Diferencia entre Models y Services

| Aspecto | Models | Services |
|---------|--------|----------|
| Propósito | Obtener datos crudos | Proveer servicios/APIs |
| Lógica | Mínima (solo SELECT) | Puede tener lógica de negocio |
| Formato | JSON genérico | JSON específico para UI |
| Uso | CRUD operations | Combobox, autocomplete, etc. |

## Convenciones

- Todos retornan JSON
- Nombres: `api_[recurso].php`
- Pueden incluir JOINs complejos
- Optimizados para consumo frontend
