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
    fetch("../models/obtener_destinos.php")
        .then(response => response.json())
        .then(data => {
            let tbody = document.querySelector("#tablaDestino tbody");
            tbody.innerHTML = "";
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay registros</td></tr>';
                return;
            }
            
            data.forEach(item => {
                let row = tbody.insertRow();
                row.innerHTML = `
                    <td>${item.id}</td>
                    <td>${item.codigo_destino}</td>
                    <td>${item.nombre_destino}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="cargarDestino(${item.id}, '${item.codigo_destino}', '${item.nombre_destino}')">
                            Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarDestino(${item.id})">
                            Eliminar
                        </button>
                    </td>
                `;
            });
        })
        .catch(error => {
            console.error("Error cargando tabla:", error);
            let tbody = document.querySelector("#tablaDestino tbody");
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function enviarFormulario(accion) {
    let formData = new FormData(document.getElementById("formDestino"));
    formData.append("accion", accion);

    fetch("../controllers/procesar_destino.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        if (data.includes("Exito") || data.includes("correctamente") || data.includes("Eliminado")) {
            limpiarFormulario();
            cargarTabla();
        }
    })
    .catch(error => console.error("Error en el proceso:", error));
}

function cargarDestino(id, codigo, nombre) {
    document.getElementById("id_destino").value = id;
    document.getElementById("codigo_destino").value = codigo;
    document.getElementById("nombre_destino").value = nombre;
}

function eliminarDestino(id) {
    if (confirm("Â¿EstÃ¡ seguro de eliminar este destino?")) {
        let formData = new FormData();
        formData.append("accion", "eliminar");
        formData.append("id_destino", id);

        fetch("../controllers/procesar_destino.php", {
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
    document.getElementById("formDestino").reset();
    document.getElementById("id_destino").value = "";
}

