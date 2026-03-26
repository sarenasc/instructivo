# 🔐 MIGRACIÓN A VARIABLES DE ENTORNO - COMPLETADA

## ¿Qué se hizo?

Se migraron **todas las credenciales hardcodeadas** a un archivo `.env` centralizado.

### Archivos Creados

| Archivo | Propósito |
|---------|-----------|
| `.env` | Variables de entorno reales (NO compartir) |
| `.env.example` | Plantilla para nuevos desarrolladores |
| `.gitignore` | Ignora `.env` y archivos sensibles |
| `config/database.php` | Configuración centralizada de BD |

### Archivos Modificados

| Archivo | Cambio |
|---------|--------|
| `app/conexion.php` | Ahora usa `config/database.php` |
| `app/server.js` | Lee credenciales desde `.env` |
| `app/api/main.py` | Lee credenciales desde `.env` |
| `composer.json` | Agregado `vlucas/phpdotenv` |

---

## 📦 Dependencias Instaladas

### PHP
```bash
composer require vlucas/phpdotenv
```

### Node.js
```bash
npm install dotenv --save
```

### Python
```bash
pip install python-dotenv
```

---

## 🔧 CÓMO USAR

### En PHP
```php
// Opción 1: Usar el archivo centralizado (recomendado)
require_once __DIR__ . '/config/database.php';
// $conn, $conn2, $conn3 ya están disponibles

// Opción 2: Cargar .env manualmente
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$server = $_ENV['DB_SERVER'];
```

### En Node.js
```javascript
require('dotenv').config();
const server = process.env.DB_SERVER;
const password = process.env.DB_PASSWORD;
```

### En Python
```python
from dotenv import load_dotenv
import os

load_dotenv()
server = os.getenv('DB_SERVER')
password = os.getenv('DB_PASSWORD')
```

---

## ⚠️ IMPORTANTE

1. **NUNCA compartas el archivo `.env`**
2. **NUNCA subas `.env` a Git** (ya está en `.gitignore`)
3. Si necesitas compartir la configuración, usa `.env.example`
4. En producción, configura las variables de entorno en el servidor

---

## 🧪 VERIFICACIÓN

Para verificar que todo funciona:

1. **PHP:**
   ```bash
   cd C:\xampp\htdocs\instructivo
   php -r "require 'config/database.php'; echo 'Conexión OK';"
   ```

2. **Node.js:**
   ```bash
   cd C:\xampp\htdocs\instructivo
   node app/server.js
   ```

3. **Python:**
   ```bash
   cd C:\xampp\htdocs\instructivo\app\api
   python main.py
   ```

---

## 📊 VARIABLES DISPONIBLES

| Variable | Descripción | Ejemplo |
|----------|-------------|---------|
| `DB_SERVER` | Servidor SQL Server | `192.168.19.4` |
| `DB_USER` | Usuario de BD | `sa` |
| `DB_PASSWORD` | Contraseña | `Robin@2021` |
| `DB_DATABASE` | BD Principal | `SistGestion` |
| `DB_DATABASE_FACTURADOR` | BD Facturador | `Facturador_ASanta_Almahue` |
| `DB_DATABASE_DW` | BD Data Warehouse | `DW_Almahue` |
| `NODE_PORT` | Puerto API Node | `3003` |
| `PYTHON_PORT` | Puerto API Python | `8000` |
| `APP_ENV` | Entorno | `production` |
| `APP_DEBUG` | Debug mode | `false` |

---

## ✅ ESTADO: COMPLETADO

- [x] Archivo `.env` creado
- [x] Archivo `.env.example` creado
- [x] Archivo `.gitignore` creado
- [x] `config/database.php` creado
- [x] `app/conexion.php` actualizado
- [x] `app/server.js` actualizado
- [x] `app/api/main.py` actualizado
- [x] Dependencias PHP instaladas
- [x] Dependencias Node instaladas
- [x] Dependencias Python instaladas

---

_Hecho por Scapy - Criatura de Laboratorio 🧪_
