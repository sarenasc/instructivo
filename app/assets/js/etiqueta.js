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
    fetch("../models/obtener_exportadoras.php")
        .then(response => response.json())
        .then(data => {
            let select = document.getElementById("exportadora");
            select.innerHTML = '<option value="">Seleccione una exportadora</option>';
            data.forEach(exportadora => {
                let option = document.createElement("option");
                option.value = exportadora.id;
                option.textContent = exportadora.nombre_exportadora;
                select.appendChild(option);
            });
        })
        .catch(error => console.error("Error cargando exportadoras:", error));
}

function cargarTabla() {
    fetch("../models/obtener_etiquetas.php")
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
                    <td>${item.id}</td>
                    <td>${item.codigo_etiqueta}</td>
                    <td>${item.nombre_etiqueta}</td>
                    <td>${item.nombre_exportadora || 'N/A'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="cargarEtiqueta(${item.id}, '${item.codigo_etiqueta}', '${item.nombre_etiqueta}', '${item.id_exportadora || ''}')">
                            Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarEtiqueta(${item.id})">
                            Eliminar
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

    fetch("../controllers/procesar_etiqueta.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        if (data.includes("Ã©xito") || data.includes("correctamente") || data.includes("Eliminado")) {
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
    if (confirm("Â¿EstÃ¡ seguro de eliminar esta etiqueta?")) {
        let formData = new FormData();
        formData.append("accion", "eliminar");
        formData.append("id_etiqueta", id);

        fetch("../controllers/procesar_etiqueta.php", {
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

