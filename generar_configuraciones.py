import os

BASE_DIR = r"C:\xampp\htdocs\instructivo"

# Configuración de cada tabla
TABLAS = [
    {
        'nombre': 'embalaje',
        'titulo': 'Gestión de Embalajes',
        'campos_form': '''
                    <div class="col-md-4 mb-3">
                        <label for="codigo_embalaje" class="form-label">Código Embalaje</label>
                        <input type="text" class="form-control" id="codigo_embalaje" name="codigo_embalaje" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="nombre_embalaje" class="form-label">Descripción Embalaje</label>
                        <input type="text" class="form-control" id="nombre_embalaje" name="nombre_embalaje" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="peso_embalaje" class="form-label">Peso Embalaje</label>
                        <input type="text" class="form-control" id="peso_embalaje" name="peso_embalaje" required>
                    </div>
''',
        'columnas_tabla': 'ID, Código, Descripción, Peso, Acciones',
        'js_file': 'embalaje.js'
    },
    {
        'nombre': 'etiqueta',
        'titulo': 'Gestión de Etiquetas',
        'campos_form': '''
                    <div class="col-md-6 mb-3">
                        <label for="nombre_etiqueta" class="form-label">Nombre Etiqueta</label>
                        <input type="text" class="form-control" id="nombre_etiqueta" name="nombre_etiqueta" required>
                    </div>
''',
        'columnas_tabla': 'ID, Nombre, Acciones',
        'js_file': 'etiqueta.js'
    },
    {
        'nombre': 'pallet',
        'titulo': 'Gestión de Pallets',
        'campos_form': '''
                    <div class="col-md-6 mb-3">
                        <label for="nombre_pallet" class="form-label">Nombre Pallet</label>
                        <input type="text" class="form-control" id="nombre_pallet" name="nombre_pallet" required>
                    </div>
''',
        'columnas_tabla': 'ID, Nombre, Acciones',
        'js_file': 'pallet.js'
    },
    {
        'nombre': 'plu',
        'titulo': 'Gestión de PLUs',
        'campos_form': '''
                    <div class="col-md-4 mb-3">
                        <label for="codigo_plu" class="form-label">Código PLU</label>
                        <input type="text" class="form-control" id="codigo_plu" name="codigo_plu" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="nombre_plu" class="form-label">Nombre PLU</label>
                        <input type="text" class="form-control" id="nombre_plu" name="nombre_plu" required>
                    </div>
''',
        'columnas_tabla': 'ID, Código, Nombre, Acciones',
        'js_file': 'plu.js'
    },
    {
        'nombre': 'exportadora',
        'titulo': 'Gestión de Exportadoras',
        'campos_form': '''
                    <div class="col-md-6 mb-3">
                        <label for="Nombre_Exportadora" class="form-label">Nombre Exportadora</label>
                        <input type="text" class="form-control" id="Nombre_Exportadora" name="Nombre_Exportadora" required>
                    </div>
''',
        'columnas_tabla': 'ID, Nombre, Acciones',
        'js_file': 'exportadora.js'
    },
    {
        'nombre': 'destino',
        'titulo': 'Gestión de Destinos',
        'campos_form': '''
                    <div class="col-md-6 mb-3">
                        <label for="nombre_destino" class="form-label">Nombre Destino</label>
                        <input type="text" class="form-control" id="nombre_destino" name="nombre_destino" required>
                    </div>
''',
        'columnas_tabla': 'ID, Nombre, Acciones',
        'js_file': 'destino.js'
    },
    {
        'nombre': 'inst_altura_pallet',
        'titulo': 'Configuración Altura Pallet',
        'campos_form': '''
                    <div class="col-md-4 mb-3">
                        <label for="altura" class="form-label">Altura</label>
                        <input type="text" class="form-control" id="altura" name="altura" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="cajas" class="form-label">Cajas</label>
                        <input type="text" class="form-control" id="cajas" name="cajas" required>
                    </div>
''',
        'columnas_tabla': 'ID, Altura, Cajas, Acciones',
        'js_file': 'inst_altura_pallet.js'
    }
]

def generar_php(tabla):
    nombre = tabla['nombre']
    titulo = tabla['titulo']
    campos = tabla['campos_form']
    columnas = tabla['columnas_tabla'].split(', ')
    num_columnas = len(columnas)
    
    php_content = f'''<?php
$titulo_pagina = '{titulo}';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-5">
    <h2 class="mb-4">{titulo}</h2>
    
    <!-- Formulario -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Agregar/Editar Registro</h5>
            <form id="form{nombre.capitalize()}">
                <input type="hidden" id="id_{nombre}" name="id_{nombre}">
                <div class="row">{campos}
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
                    <button type="button" class="btn btn-warning" id="btnModificar">Modificar</button>
                    <button type="button" class="btn btn-danger" id="btnEliminar">Eliminar</button>
                    <button type="button" class="btn btn-secondary" id="btnLimpiar">Limpiar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Registros -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Registros Existentes</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tabla{nombre.capitalize()}">
                    <thead class="table-light">
                        <tr>
'''
    
    for col in columnas:
        php_content += f'                            <th>{col}</th>\n'
    
    php_content += f'''                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="{num_columnas}" class="text-center">Cargando registros...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$scripts_extra = '<script src="../assets/js/{tabla["js_file"]}"></script>';
require_once __DIR__ . '/../includes/footer.php';
?>
'''
    
    return php_content

def main():
    print("=" * 60)
    print("  GENERANDO PAGINAS DE CONFIGURACION CON TABLAS")
    print("=" * 60)
    print()
    
    for tabla in TABLAS:
        nombre = tabla['nombre']
        php_file = os.path.join(BASE_DIR, 'app', 'Configuracion', f'{nombre}.php')
        
        contenido = generar_php(tabla)
        
        with open(php_file, 'w', encoding='utf-8') as f:
            f.write(contenido)
        
        print(f"  [OK] {nombre}.php")
    
    print()
    print("=" * 60)
    print("  COMPLETADO")
    print("=" * 60)
    input("\nPresiona Enter para salir...")

if __name__ == "__main__":
    main()
