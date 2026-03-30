document.addEventListener("DOMContentLoaded", function () {
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
    fetch("../models/obtener_exportadoras.php")
        .then(response => response.json())
        .then(data => {
            let tbody = document.querySelector("#tablaExportadora tbody");
            tbody.innerHTML = "";
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay registros</td></tr>';
                return;
            }
            
            data.forEach(item => {
                let row = tbody.insertRow();
                row.innerHTML = `
                    <td>${item.id_exportadora}</td>
                    <td>${item.cod_exportadora}</td>
                    <td>${item.nombre_exportadora}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="cargarExportadora(${item.id_exportadora}, '${item.cod_exportadora}', '${item.nombre_exportadora}')">
                            âœï¸ Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarExportadora(${item.id_exportadora})">
                            ðŸ—‘ï¸ Eliminar
                        </button>
                    </td>
                `;
            });
        })
        .catch(error => {
            console.error("Error cargando tabla:", error);
            let tbody = document.querySelector("#tablaExportadora tbody");
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function enviarFormulario(accion) {
    let formData = new FormData(document.getElementById("formExportadora"));
    formData.append("accion", accion);

    fetch("../controllers/procesar_exportadora.php", {
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

function cargarExportadora(id, codigo, nombre) {
    document.getElementById("id_exportadora").value = id;
    document.getElementById("cod_exportadora").value = codigo;
    document.getElementById("nombre_exportadora").value = nombre;
}

function eliminarExportadora(id) {
    if (confirm("Â¿EstÃ¡ seguro de eliminar esta exportadora?")) {
        let formData = new FormData();
        formData.append("accion", "eliminar");
        formData.append("id_exportadora", id);

        fetch("../controllers/procesar_exportadora.php", {
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
    document.getElementById("formExportadora").reset();
    document.getElementById("id_exportadora").value = "";
}

