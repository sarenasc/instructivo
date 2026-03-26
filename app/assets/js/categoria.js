document.addEventListener("DOMContentLoaded", function () {
    cargarEspecies();
    cargarExportadora();
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

function cargarEspecies() {
    fetch("../obtener_especies.php")
        .then(response => response.json())
        .then(data => {
            let select = document.getElementById("especie_categoria");
            select.innerHTML = '<option value="">Seleccione una especie</option>';
            data.forEach(especie => {
                let option = document.createElement("option");
                option.value = especie.id_especie;
                option.textContent = especie.especie;
                select.appendChild(option);
            });
        })
        .catch(error => console.error("Error cargando especies:", error));
}

function cargarExportadora() {
    fetch("../obtener_exportadoras.php")
        .then(response => response.json())
        .then(data => {
            let select = document.getElementById("exportadora");
            select.innerHTML = '<option value="">Seleccione una exportadora</option>';
            data.forEach(exportadora => {
                let option = document.createElement("option");
                option.value = exportadora.id_exportadora;
                option.textContent = exportadora.nombre_exportadora;
                select.appendChild(option);
            });
        })
        .catch(error => console.error("Error cargando exportadoras:", error));
}

function cargarTabla() {
    fetch("../obtener_categoria.php")
        .then(response => response.json())
        .then(data => {
            let tbody = document.querySelector("#tablaCategoria tbody");
            tbody.innerHTML = "";
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay registros</td></tr>';
                return;
            }
            
            data.forEach(item => {
                let row = tbody.insertRow();
                row.innerHTML = `
                    <td>${item.id_categoria}</td>
                    <td>${item.codigo_categoria}</td>
                    <td>${item.nombre_categoria}</td>
                    <td>${item.especie || 'N/A'}</td>
                    <td>${item.nombre_exportadora || 'N/A'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="cargarCategoria(${item.id_categoria}, '${item.codigo_categoria}', '${item.nombre_categoria}', '${item.id_especie || ''}', '${item.id_exportadora || ''}')">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarCategoria(${item.id_categoria})">
                            🗑️ Eliminar
                        </button>
                    </td>
                `;
            });
        })
        .catch(error => {
            console.error("Error cargando tabla:", error);
            let tbody = document.querySelector("#tablaCategoria tbody");
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function enviarFormulario(accion) {
    let formData = new FormData(document.getElementById("formCategoria"));
    formData.append("accion", accion);

    fetch("../procesar_categoria.php", {
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

function cargarCategoria(id, codigo, nombre, id_especie, id_exportadora) {
    document.getElementById("id_categoria").value = id;
    document.getElementById("codigo_categoria").value = codigo;
    document.getElementById("nombre_categoria").value = nombre;
    document.getElementById("especie_categoria").value = id_especie;
    document.getElementById("exportadora").value = id_exportadora;
}

function eliminarCategoria(id) {
    if (confirm("¿Está seguro de eliminar esta categoría?")) {
        let formData = new FormData();
        formData.append("accion", "eliminar");
        formData.append("id_categoria", id);

        fetch("../procesar_categoria.php", {
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
    document.getElementById("formCategoria").reset();
    document.getElementById("id_categoria").value = "";
}
