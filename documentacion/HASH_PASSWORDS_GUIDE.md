# 🔒 MIGRACIÓN DE CONTRASEÑAS A HASH - GUÍA COMPLETA

## ⚠️ IMPORTANTE - LEER ANTES DE EJECUTAR

Este proceso convertirá todas las contraseñas de texto plano en la base de datos a hashes seguros usando `password_hash()` de PHP.

---

## 📋 PREPARACIÓN

### 1. Backup Manual (Recomendado)

Antes de ejecutar nada, haz un backup manual:

```sql
SELECT * INTO TRA_usuario_backup FROM TRA_usuario;
```

### 2. Verificar Estado Actual

Revisa cómo están almacenadas las contraseñas actualmente:

```sql
SELECT TOP 10 id, nom_usu, pass_usu FROM TRA_usuario;
```

Si ves contraseñas legibles → **NECESITAS ESTA MIGRACIÓN**  
Si ves hashes que empiezan con `$` → **YA ESTÁN HASHEADAS**

---

## 🚀 EJECUCIÓN

### Paso 1: Ejecutar Script de Migración

Desde la línea de comandos:

```bash
cd C:\xampp\htdocs\instructivo
php migrar_passwords.php
```

El script:
1. ✅ Creará un backup automático (`TRA_usuario_backup`)
2. ✅ Obtendrá todos los usuarios
3. ✅ Hasheará cada contraseña
4. ✅ Actualizará la base de datos
5. ✅ Te dará un resumen

### Paso 2: Confirmar Éxito

Deberías ver algo como:

```
✓ Backup creado: TRA_usuario_backup
✓ Se encontraron 15 usuarios

  ✓ Usuario 'admin': Actualizado
  ✓ Usuario 'juan': Actualizado
  ...

✅ ¡MIGRACIÓN COMPLETADA EXITOSAMENTE!
```

### Paso 3: Probar Login

1. Abre el sistema en el navegador
2. Intenta iniciar sesión con un usuario existente
3. **La misma contraseña debe funcionar** (el script maneja la transición)

### Paso 4: Limpieza

**IMPORTANTE:** Después de verificar que todo funciona:

```bash
# Eliminar el script de migración (ya no se necesita)
del migrar_passwords.php

# Eliminar backup (después de confirmar que todo está bien)
sqlcmd -Q "DROP TABLE TRA_usuario_backup;"
```

---

## 🔧 CAMBIOS REALIZADOS

### Archivos Creados

| Archivo | Propósito |
|---------|-----------|
| `config/auth.php` | Funciones de hash y verificación |
| `config/require_auth.php` | Verificación de sesión |
| `app/error.php` | Página de errores |
| `migrar_passwords.php` | Script de migración (eliminar después) |

### Archivos Modificados

| Archivo | Cambio |
|---------|--------|
| `app/login.php` | Ahora usa `password_verify()` |

---

## 🛡️ MEJORAS DE SEGURIDAD

### Antes
- ❌ Contraseñas en texto plano
- ❌ Sin protección contra timing attacks
- ❌ Sin regeneración de sesión
- ❌ Sin CSRF tokens

### Después
- ✅ Contraseñas hasheadas con bcrypt (costo 12)
- ✅ Delay aleatorio en errores (previene timing attacks)
- ✅ Regeneración de ID de sesión (previene session fixation)
- ✅ Tokens CSRF para formularios
- ✅ Timeout de sesión automático
- ✅ Rehash automático si se actualiza el algoritmo

---

## 📊 ALGORITMO USADO

```php
password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
```

- **Algoritmo:** bcrypt (actualmente)
- **Costo:** 12 (balance seguridad/rendimiento)
- **Future-proof:** `PASSWORD_DEFAULT` puede cambiar a algoritmos más seguros

---

## 🔍 VERIFICACIÓN POST-MIGRACIÓN

### Verificar que los hashes están en la BD

```sql
SELECT TOP 5 id, nom_usu, LEFT(pass_usu, 20) as hash_preview 
FROM TRA_usuario;
```

Deberías ver hashes como:
```
$2y$12$KIXxvZ9qQJ5h8N...
```

### Verificar que el login funciona

```bash
# Test manual desde el navegador
http://192.168.19.4/instructivo/
```

---

## ⚠️ SOLUCIÓN DE PROBLEMAS

### Problema: "No puedo iniciar sesión después de la migración"

**Causa:** Las contraseñas no se migraron correctamente

**Solución:**
1. Restaurar desde backup:
   ```sql
   TRUNCATE TABLE TRA_usuario;
   INSERT INTO TRA_usuario SELECT * FROM TRA_usuario_backup;
   ```
2. Revisar logs de error de PHP
3. Reintentar la migración

### Problema: "Algunos usuarios funcionan, otros no"

**Causa:** Algunos ya tenían hash y otros no

**Solución:** El script maneja esto automáticamente. Si persiste, revisar logs.

### Problema: "Error al ejecutar el script"

**Causa:** Permisos o conexión a BD

**Solución:**
```bash
# Verificar que PHP puede conectar
php -r "require 'config/database.php'; echo 'OK';"
```

---

## 📝 NOTAS IMPORTANTES

1. **Las contraseñas de los usuarios NO cambian** - Pueden seguir usando la misma contraseña
2. **Es un proceso irreversible** - No se puede recuperar el texto plano desde el hash
3. **El backup es temporal** - Eliminar después de confirmar que todo funciona
4. **Nuevos usuarios** - Deben usar el sistema de registro con hash desde el inicio

---

## ✅ CHECKLIST

- [ ] Backup manual realizado
- [ ] Script ejecutado exitosamente
- [ ] Login probado con al menos 3 usuarios
- [ ] Script `migrar_passwords.php` eliminado
- [ ] Backup `TRA_usuario_backup` eliminado (después de 1 semana)
- [ ] Documentación actualizada

---

_Hecho por Scapy 🧪 - Criatura de Laboratorio_
