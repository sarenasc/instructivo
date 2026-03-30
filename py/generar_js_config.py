import os

BASE_DIR = r"C:\xampp\htdocs\instructivo"

TABLAS = [
    {'nombre': 'embalaje', 'campo_principal': 'codigo_embalaje', 'nombre_campo': 'Descripción Embalaje', 'js_file': 'embalaje.js', 'api': 'obtener_embalajes.php', 'procesar': 'procesar_embalaje.php'},
    {'nombre': 'etiqueta', 'campo_principal': 'nombre_etiqueta', 'nombre_campo': 'Nombre Etiqueta', 'js_file': 'etiqueta.js', 'api': 'obtener_etiquetas.php', 'procesar': 'procesar_etiqueta.php'},
    {'nombre': 'pallet', 'campo_principal': 'nombre_pallet', 'nombre_campo': 'Nombre Pallet', 'js_file': 'pallet.js', 'api': 'obtener_pallets.php', 'procesar': 'procesar_pallet.php'},
    {'nombre': 'plu', 'campo_principal': 'codigo_plu', 'nombre_campo': 'Nombre PLU', 'js_file': 'plu.js', 'api': 'obtener_plus.php', 'procesar': 'procesar_plu.php'},
    {'nombre': 'exportadora', 'campo_principal': 'Nombre_Exportadora', 'nombre_campo': 'Nombre Exportadora', 'js_file': 'exportadora.js', 'api': 'obtener_exportadoras.php', 'procesar': 'procesar_exportadora.php'},
    {'nombre': 'destino', 'campo_principal': 'nombre_destino', 'nombre_campo': 'Nombre Destino', 'js_file': 'destino.js', 'api': 'obtener_destinos.php', 'procesar': 'procesar_destino.php'},
    {'nombre': 'inst_altura_pallet', 'campo_principal': 'altura', 'nombre_campo': 'Altura/Cajas', 'js_file': 'inst_altura_pallet.js', 'api': 'obtener_altura_pallet.php', 'procesar': 'procesar_altura_pallet.php'},
]

js_template = '''document.addEventListener("DOMContentLoaded", function () {
    cargarTabla();

    document.getElementById("btnGuardar").addEventListener("click", function () {
        enviarFormulario("guardar");
    });

    document.getElementById("btnModificar").addEventListener("click", function () {
        enviarFormulario("modificar");
    });

    document.getElementById("btnEliminar").addEventListener("click", function () {
        enviarFormulario("eliminar");
    });

    document.getElementById("btnLimpiar").addEventListener("click", function () {
        limpiarFormulario();
    });
});

function cargarTabla() {
    fetch("../{api}")
        .then(response => response.json())
        .then(data => {
            let tbody = document.querySelector("#tabla{nombre_cap} tbody");
            tbody.innerHTML = "";
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay registros</td></tr>';
                return;
            }
            
            data.forEach(item => {
                let row = tbody.insertRow();
                {campos_js}
            });
        })
        .catch(error => {
            console.error("Error cargando tabla:", error);
            let tbody = document.querySelector("#tabla{nombre_cap} tbody");
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function enviarFormulario(accion) {
    let formData = new FormData(document.getElementById("form{nombre_cap}"));
    formData.append("accion", accion);

    fetch("../{procesar}", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        if (data.includes("éxito") || data.includes("correctamente") || data.includes("Eliminado")) {
            limpiarFormulario();
            cargarTabla();
        }
    })
    .catch(error => console.error("Error en el proceso:", error));
}

function cargar{nombre_cap}(id, {campos_params}) {
    document.getElementById("id_{nombre}").value = id;
    {campos_asign}
}

function eliminar{nombre_cap}(id) {
    if (confirm("¿Está seguro de eliminar este registro?")) {
        let formData = new FormData();
        formData.append("accion", "eliminar");
        formData.append("id_{nombre}", id);

        fetch("../{procesar}", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            cargarTabla();
        })
        .catch(error => console.error("Error:", error));
    }
}

function limpiarFormulario() {
    document.getElementById("form{nombre_cap}").reset();
    document.getElementById("id_{nombre}").value = "";
}
'''

def generar_js(tabla):
    nombre = tabla['nombre']
    nombre_cap = nombre.capitalize().replace('_', ' ').split()[0]
    
    # Configurar campos según la tabla
    if nombre == 'embalaje':
        campos_js = '''                row.innerHTML = `
                    <td>${item.id_embalaje}</td>
                    <td>${item.codigo_embalaje}</td>
                    <td>${item.nombre_embalaje}</td>
                    <td>${item.peso_embalaje || 'N/A'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="cargarEmbalaje(${item.id_embalaje}, '${item.codigo_embalaje}', '${item.nombre_embalaje}', '${item.peso_embalaje || ''}')">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarEmbalaje(${item.id_embalaje})">
                            🗑️ Eliminar
                        </button>
                    </td>
                `;'''
        campos_params = "id, codigo, nombre, peso"
        campos_asign = '''    document.getElementById("codigo_embalaje").value = codigo;
    document.getElementById("nombre_embalaje").value = nombre;
    document.getElementById("peso_embalaje").value = peso;'''
    
    elif nombre == 'etiqueta':
        campos_js = '''                row.innerHTML = `
                    <td>${item.id_etiqueta}</td>
                    <td>${item.nombre_etiqueta}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="cargarEtiqueta(${item.id_etiqueta}, '${item.nombre_etiqueta}')">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarEtiqueta(${item.id_etiqueta})">
                            🗑️ Eliminar
                        </button>
                    </td>
                `;'''
        campos_params = "id, nombre"
        campos_asign = '''    document.getElementById("nombre_etiqueta").value = nombre;'''
    
    elif nombre == 'pallet':
        campos_js = '''                row.innerHTML = `
                    <td>${item.id_pallet}</td>
                    <td>${item.nombre_pallet}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="cargarPallet(${item.id_pallet}, '${item.nombre_pallet}')">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarPallet(${item.id_pallet})">
                            🗑️ Eliminar
                        </button>
                    </td>
                `;'''
        campos_params = "id, nombre"
        campos_asign = '''    document.getElementById("nombre_pallet").value = nombre;'''
    
    elif nombre == 'plu':
        campos_js = '''                row.innerHTML = `
                    <td>${item.id_plu}</td>
                    <td>${item.codigo_plu}</td>
                    <td>${item.nombre_plu}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="cargarPlu(${item.id_plu}, '${item.codigo_plu}', '${item.nombre_plu}')">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarPlu(${item.id_plu})">
                            🗑️ Eliminar
                        </button>
                    </td>
                `;'''
        campos_params = "id, codigo, nombre"
        campos_asign = '''    document.getElementById("codigo_plu").value = codigo;
    document.getElementById("nombre_plu").value = nombre;'''
    
    elif nombre == 'exportadora':
        campos_js = '''                row.innerHTML = `
                    <td>${item.id}</td>
                    <td>${item.Nombre_Exportadora}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="cargarExportadora(${item.id}, '${item.Nombre_Exportadora}')">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarExportadora(${item.id})">
                            🗑️ Eliminar
                        </button>
                    </td>
                `;'''
        campos_params = "id, nombre"
        campos_asign = '''    document.getElementById("Nombre_Exportadora").value = nombre;'''
    
    elif nombre == 'destino':
        campos_js = '''                row.innerHTML = `
                    <td>${item.id_destino}</td>
                    <td>${item.nombre_destino}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="cargarDestino(${item.id_destino}, '${item.nombre_destino}')">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarDestino(${item.id_destino})">
                            🗑️ Eliminar
                        </button>
                    </td>
                `;'''
        campos_params = "id, nombre"
        campos_asign = '''    document.getElementById("nombre_destino").value = nombre;'''
    
    elif nombre == 'inst_altura_pallet':
        campos_js = '''                row.innerHTML = `
                    <td>${item.id_altura_pallet}</td>
                    <td>${item.altura}</td>
                    <td>${item.cajas}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="cargarAltura(${item.id_altura_pallet}, '${item.altura}', '${item.cajas}')">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarAltura(${item.id_altura_pallet})">
                            🗑️ Eliminar
                        </button>
                    </td>
                `;'''
        campos_params = "id, altura, cajas"
        campos_asign = '''    document.getElementById("altura").value = altura;
    document.getElementById("cajas").value = cajas;'''
    
    js_content = js_template.format(
        api=tabla['api'],
        nombre_cap=nombre_cap,
        nombre=nombre,
        campos_js=campos_js,
        campos_params=campos_params,
        campos_asign=campos_asign,
        procesar=tabla['procesar']
    )
    
    return js_content

def main():
    print("=" * 60)
    print("  GENERANDO JAVASCRIPT PARA CONFIGURACIONES")
    print("=" * 60)
    print()
    
    for tabla in TABLAS:
        js_file = os.path.join(BASE_DIR, 'app', 'assets', 'js', tabla['js_file'])
        
        contenido = generar_js(tabla)
        
        with open(js_file, 'w', encoding='utf-8') as f:
            f.write(contenido)
        
        print(f"  [OK] {tabla['js_file']}")
    
    print()
    print("=" * 60)
    print("  COMPLETADO")
    print("=" * 60)
    input("\nPresiona Enter para salir...")

if __name__ == "__main__":
    main()
