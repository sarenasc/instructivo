# ✅ CORRECCIÓN - SELECTS UNDEFINED

**Fecha:** 27 Marzo 2026  
**Problema:** Selects de Embalaje, Etiqueta y PLU mostraban "undefined"  
**Solución:** Ajustar nombres de campos para coincidir con los aliases de los models

---

## 🔍 PROBLEMA ENCONTRADO

Los models SQL retornan campos con **aliases en minúsculas**, pero el JavaScript buscaba los campos originales con mayúsculas.

### Ejemplo

**SQL:**
```sql
SELECT 
    Codigo_emb as codigo_embalaje,
    Descripcion_Embalaje as nombre_embalaje
FROM inst_embalaje
```

**JavaScript (INCORRECTO):**
```javascript
emb.Codigo_emb  // ❌ undefined
emb.Descripcion_Embalaje  // ❌ undefined
```

**JavaScript (CORRECTO):**
```javascript
emb.codigo_embalaje  // ✅ "CA15MNA"
emb.nombre_embalaje  // ✅ "CAJA CARTON 15KG"
```

---

## ✅ CAMBIOS REALIZADOS

### 1. `cargarEmbalajes()` ✅

**Antes:**
```javascript
const codigo = emb.Codigo_emb;
const descripcion = emb.Descripcion_Embalaje;
```

**Ahora:**
```javascript
const codigo = emb.codigo_embalaje || emb.Codigo_emb || '';
const descripcion = emb.nombre_embalaje || emb.Descripcion_Embalaje || '';
```

---

### 2. `cargarEtiquetas()` ✅

**Antes:**
```javascript
const texto = etq.Nombre_etiqueta;
```

**Ahora:**
```javascript
const texto = etq.nombre_etiqueta || etq.Nombre_etiqueta || 'Sin nombre';
```

---

### 3. `cargarPLU()` ✅

**Antes:**
```javascript
const texto = plu.plu;
```

**Ahora:**
```javascript
const texto = plu.nombre_plu || plu.plu || plu.codigo_plu || 'Sin PLU';
```

---

## 📊 MAPEO DE CAMPOS

### Embalaje
| Campo SQL | Alias | JavaScript |
|-----------|-------|------------|
| `Codigo_emb` | `codigo_embalaje` | `emb.codigo_embalaje` ✅ |
| `Descripcion_Embalaje` | `nombre_embalaje` | `emb.nombre_embalaje` ✅ |

### Etiqueta
| Campo SQL | Alias | JavaScript |
|-----------|-------|------------|
| `Cod_etiqueta` | `codigo_etiqueta` | `etq.codigo_etiqueta` |
| `Nombre_etiqueta` | `nombre_etiqueta` | `etq.nombre_etiqueta` ✅ |

### PLU
| Campo SQL | Alias | JavaScript |
|-----------|-------|------------|
| `cod_plu` | `codigo_plu` | `plu.codigo_plu` |
| `plu` | `nombre_plu` | `plu.nombre_plu` ✅ |

---

## 🧪 PRUEBA

1. Abre `http://localhost/instructivo/app/Procesos/crear_instructivo.php`
2. Selecciona Exportadora y Especie
3. Verifica que carguen correctamente:
   - ✅ **Embalaje**: "CA15MNA - CAJA CARTON SWEET ORANGE 15KG"
   - ✅ **Etiqueta**: "SWEET ORANGE"
   - ✅ **PLU**: "PLU 4012"

---

## 📝 LECCIÓN APRENDIDA

**Siempre verificar los aliases en los models PHP** antes de acceder a las propiedades en JavaScript.

**Regla:**
1. El SQL define aliases: `SELECT campo as alias`
2. PHP retorna: `['alias' => 'valor']`
3. JavaScript accede: `data.alias`

**Fallback:** Siempre agregar fallback para compatibilidad:
```javascript
const valor = data.alias || data.CampoOriginal || 'Default';
```

---

_Hecho por Scapy 🧪 - Criatura de Laboratorio_
