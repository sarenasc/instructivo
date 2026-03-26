document.addEventListener("DOMContentLoaded", function () {
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
    fetch("../obtener_etiquetas.php")
        .then(response => response.json())
        .then(data => {
            let tbody = document.querySelector("#tablaEtiqueta tbody");
            tbody.innerHTML = "";
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay registros</td></tr>';
                return;
            }
            
            data.forEach(item => {
                let row = tbody.insertRow();
                row.innerHTML = `
                    <td>${item.id_etiqueta}</td>
                    <td>${item.codigo_etiqueta}</td>
                    <td>${item.nombre_etiqueta}</td>
                    <td>${item.nombre_exportadora || 'N/A'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="cargarEtiqueta(${item.id_etiqueta}, '${item.codigo_etiqueta}', '${item.nombre_etiqueta}', '${item.id_exportadora || ''}')">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarEtiqueta(${item.id_etiqueta})">
                            🗑️ Eliminar
                        </button>
                    </td>
                `;
            });
        })
        .catch(error => {
            console.error("Error cargando tabla:", error);
            let tbody = document.querySelector("#tablaEtiqueta tbody");
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function enviarFormulario(accion) {
    let formData = new FormData(document.getElementById("formEtiqueta"));
    formData.append("accion", accion);

    fetch("../procesar_etiqueta.php", {
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

function cargarEtiqueta(id, codigo, nombre, id_exportadora) {
    document.getElementById("id_etiqueta").value = id;
    document.getElementById("codigo_etiqueta").value = codigo;
    document.getElementById("nombre_etiqueta").value = nombre;
    document.getElementById("exportadora").value = id_exportadora;
}

function eliminarEtiqueta(id) {
    if (confirm("¿Está seguro de eliminar esta etiqueta?")) {
        let formData = new FormData();
        formData.append("accion", "eliminar");
        formData.append("id_etiqueta", id);

        fetch("../procesar_etiqueta.php", {
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
    document.getElementById("formEtiqueta").reset();
    document.getElementById("id_etiqueta").value = "";
}
