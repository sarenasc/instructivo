import os

BASE_DIR = r"C:\xampp\htdocs\instructivo\app"

# Configuracion para cada tabla
TABLAS = [
    {'nombre': 'embalaje', 'tabla_db': 'embalaje', 'id': 'id_embalaje'},
    {'nombre': 'etiqueta', 'tabla_db': 'etiqueta', 'id': 'id_etiqueta'},
    {'nombre': 'pallet', 'tabla_db': 'pallet', 'id': 'id_pallet'},
    {'nombre': 'plu', 'tabla_db': 'plu', 'id': 'id_plu'},
    {'nombre': 'exportadora', 'tabla_db': 'exportadora', 'id': 'id'},
    {'nombre': 'destino', 'tabla_db': 'destino', 'id': 'id_destino'},
    {'nombre': 'inst_altura_pallet', 'tabla_db': 'inst_altura_pallet', 'id': 'id_altura_pallet'},
]

# Plantilla obtener
obtener_template = '''<?php
require_once("../conexion.php");

header('Content-Type: application/json');

$sql = "SELECT * FROM {tabla} ORDER BY 1";
$stmt = sqlsrv_query($conn, $sql);

$resultados = [];

if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $resultados[] = $row;
    }
    echo json_encode($resultados);
} else {
    echo json_encode([]);
}
?>
'''

# Plantilla procesar
procesar_template = '''<?php
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
        default:
            echo "Acción no válida";
    }
} else {
    echo "Método no permitido";
}

function guardar($conn) {
    // Obtener todos los campos del POST excepto 'accion'
    $campos = [];
    $valores = [];
    $placeholders = [];
    
    foreach ($_POST as $key => $value) {
        if ($key !== 'accion' && $key !== 'id_{id}') {
            $campos[] = $key;
            $valores[] = $value;
            $placeholders[] = '?';
        }
    }
    
    if (empty($campos)) {
        echo "Error: No hay datos para guardar";
        return;
    }
    
    // Verificar si ya existe (usando el primer campo como identificador)
    $primer_campo = $campos[0];
    $primer_valor = $valores[0];
    
    $checkSql = "SELECT COUNT(*) as total FROM {tabla} WHERE $primer_campo = ?";
    $checkStmt = sqlsrv_prepare($conn, $checkSql);
    sqlsrv_execute($checkStmt, [$primer_valor]);
    $checkRow = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
    
    if ($checkRow['total'] > 0) {
        echo "Error: Ya existe un registro con ese valor";
        return;
    }
    
    $sql = "INSERT INTO {tabla} (" . implode(', ', $campos) . ") VALUES (" . implode(', ', $placeholders) . ")";
    $stmt = sqlsrv_prepare($conn, $sql);
    
    if (sqlsrv_execute($stmt, $valores)) {
        echo "Registro guardado correctamente";
    } else {
        $errores = sqlsrv_errors();
        if ($errores) {
            echo "Error al guardar: " . $errores[0]['message'];
        } else {
            echo "Registro guardado correctamente";
        }
    }
}

function modificar($conn) {
    $id = $_POST['id_{id}'] ?? null;
    
    if (empty($id)) {
        echo "Error: ID no válido";
        return;
    }
    
    // Obtener todos los campos del POST excepto 'accion' e 'id'
    $campos = [];
    $valores = [];
    
    foreach ($_POST as $key => $value) {
        if ($key !== 'accion' && $key !== 'id_{id}') {
            $campos[] = "$key = ?";
            $valores[] = $value;
        }
    }
    
    if (empty($campos)) {
        echo "Error: No hay datos para modificar";
        return;
    }
    
    $valores[] = $id;
    
    $sql = "UPDATE {tabla} SET " . implode(', ', $campos) . " WHERE {id} = ?";
    $stmt = sqlsrv_prepare($conn, $sql);
    
    if (sqlsrv_execute($stmt, $valores)) {
        echo "Registro modificado correctamente";
    } else {
        $errores = sqlsrv_errors();
        if ($errores) {
            echo "Error al modificar: " . $errores[0]['message'];
        } else {
            echo "Registro modificado correctamente";
        }
    }
}

function eliminar($conn) {
    $id = $_POST['id_{id}'] ?? null;
    
    if (empty($id)) {
        echo "Error: ID no válido";
        return;
    }
    
    $sql = "DELETE FROM {tabla} WHERE {id} = ?";
    $stmt = sqlsrv_prepare($conn, $sql);
    
    if (sqlsrv_execute($stmt, [$id])) {
        echo "Registro eliminado correctamente";
    } else {
        $errores = sqlsrv_errors();
        if ($errores) {
            echo "Error al eliminar: " . $errores[0]['message'];
        } else {
            echo "Registro eliminado correctamente";
        }
    }
}
?>
'''

def main():
    print("=" * 60)
    print("  GENERANDO ARCHIVOS PHP PARA CONFIGURACIONES")
    print("=" * 60)
    print()
    
    for tabla in TABLAS:
        nombre = tabla['nombre']
        tabla_db = tabla['tabla_db']
        id_field = tabla['id']
        
        # Crear obtener_*.php
        obtener_file = os.path.join(BASE_DIR, f'obtener_{nombre}.php')
        obtener_content = obtener_template.format(tabla=tabla_db)
        
        with open(obtener_file, 'w', encoding='utf-8') as f:
            f.write(obtener_content)
        
        print(f"  [OK] obtener_{nombre}.php")
        
        # Crear procesar_*.php
        procesar_file = os.path.join(BASE_DIR, f'procesar_{nombre}.php')
        procesar_content = procesar_template.format(tabla=tabla_db, id=id_field)
        
        with open(procesar_file, 'w', encoding='utf-8') as f:
            f.write(procesar_content)
        
        print(f"  [OK] procesar_{nombre}.php")
    
    print()
    print("=" * 60)
    print("  COMPLETADO")
    print("=" * 60)
    input("\nPresiona Enter para salir...")

if __name__ == "__main__":
    main()
