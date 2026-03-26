<?php
/**
 * ===========================================
 * MIGRACIÓN DE CONTRASEÑAS A HASH
 * ===========================================
 * 
 * ESTE SCRIPT SE EJECUTA UNA SOLA VEZ
 * 
 * Convierte todas las contraseñas en texto plano
 * a hashes seguros en la base de datos.
 * 
 * INSTRUCCIONES:
 * 1. Hacer backup de la tabla TRA_usuario
 * 2. Ejecutar este script desde la línea de comandos
 * 3. Verificar que todo funcione
 * 4. ELIMINAR ESTE ARCHIVO después de usarlo
 * 
 * Uso:
 *   php migrar_passwords.php
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

echo "===========================================\n";
echo "  MIGRACIÓN DE CONTRASEÑAS A HASH\n";
echo "===========================================\n\n";

// Verificación de seguridad
echo "⚠️  ADVERTENCIA: Este proceso modificará la tabla TRA_usuario\n";
echo "⚠️  ¿Estás seguro de continuar? (Escribe 'SI' para continuar)\n";
echo "> ";

// Si se ejecuta desde CLI, pedir confirmación
if (php_sapi_name() === 'cli') {
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim($line) !== 'SI') {
        echo "\n❌ Operación cancelada.\n";
        exit(1);
    }
}

echo "\n";

// ===========================================
// PASO 1: BACKUP DE LA TABLA
// ===========================================
echo "Paso 1/4: Creando backup de la tabla...\n";

$backup_sql = "SELECT * INTO TRA_usuario_backup FROM TRA_usuario";
$backup_stmt = sqlsrv_query($conn, $backup_sql);

if ($backup_stmt) {
    echo "✓ Backup creado: TRA_usuario_backup\n";
} else {
    echo "✗ Error al crear backup: " . print_r(sqlsrv_errors(), true);
    echo "\n❌ Proceso cancelado por seguridad.\n";
    exit(1);
}

echo "\n";

// ===========================================
// PASO 2: OBTENER USUARIOS
// ===========================================
echo "Paso 2/4: Obteniendo usuarios...\n";

$sql = "SELECT id, nom_usu, pass_usu FROM TRA_usuario";
$stmt = sqlsrv_query($conn, $sql);

if (!$stmt) {
    echo "✗ Error: " . print_r(sqlsrv_errors(), true);
    exit(1);
}

$usuarios = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $usuarios[] = $row;
}

echo "✓ Se encontraron " . count($usuarios) . " usuarios\n\n";

// ===========================================
// PASO 3: HASHEAR CONTRASEÑAS
// ===========================================
echo "Paso 3/4: Hasheando contraseñas...\n\n";

$actualizados = 0;
$ya_hasheados = 0;
$errores = 0;

foreach ($usuarios as $usuario) {
    $id = $usuario['id'];
    $nombre = $usuario['nom_usu'];
    $password = $usuario['pass_usu'];
    
    // Verificar si ya parece un hash (los hashes de password_hash empiezan con $)
    if (strpos($password, '$') === 0) {
        echo "  ⚠️  Usuario '$nombre': Ya tiene hash (saltado)\n";
        $ya_hasheados++;
        continue;
    }
    
    // Generar hash
    $hash = hashPassword($password);
    
    // Actualizar en la BD
    $update_sql = "UPDATE TRA_usuario SET pass_usu = ? WHERE id = ?";
    $update_stmt = sqlsrv_query($conn, $update_sql, [$hash, $id]);
    
    if ($update_stmt) {
        echo "  ✓ Usuario '$nombre': Actualizado\n";
        $actualizados++;
    } else {
        echo "  ✗ Usuario '$nombre': Error - " . print_r(sqlsrv_errors(), true);
        $errores++;
    }
}

echo "\n";

// ===========================================
// PASO 4: RESUMEN
// ===========================================
echo "Paso 4/4: Resumen\n";
echo "-------------------------------------------\n";
echo "  Usuarios actualizados: $actualizados\n";
echo "  Ya tenían hash:        $ya_hasheados\n";
echo "  Errores:               $errores\n";
echo "-------------------------------------------\n\n";

if ($errores === 0) {
    echo "✅ ¡MIGRACIÓN COMPLETADA EXITOSAMENTE!\n\n";
    echo "PRÓXIMOS PASOS:\n";
    echo "1. Actualizar login.php para usar verifyPassword()\n";
    echo "2. Probar el login con un usuario existente\n";
    echo "3. ELIMINAR este archivo (migrar_passwords.php)\n";
    echo "4. ELIMINAR la tabla TRA_usuario_backup (después de verificar)\n";
} else {
    echo "⚠️  MIGRACIÓN COMPLETADA CON ERRORES\n";
    echo "Revisa los errores arriba. Puedes reintentar o restaurar desde el backup.\n";
}

echo "\n";
