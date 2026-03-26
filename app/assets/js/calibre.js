document.addEventListener("DOMContentLoaded", function () {
    cargarEspecies();
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
            let select = document.getElementById("especie");
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

function cargarTabla() {
    fetch("../obtener_calibres.php")
        .then(response => response.json())
        .then(data => {
            let tbody = document.querySelector("#tablaCalibres tbody");
            tbody.innerHTML = "";
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay registros</td></tr>';
                return;
            }
            
            data.forEach(calibre => {
                let row = tbody.insertRow();
                row.innerHTML = `
                    <td>${calibre.id_calibre}</td>
                    <td>${calibre.codigo_calibre}</td>
                    <td>${calibre.nombre_calibre}</td>
                    <td>${calibre.especie || 'N/A'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="cargarCalibre(${calibre.id_calibre}, '${calibre.codigo_calibre}', '${calibre.nombre_calibre}', '${calibre.id_especie || ''}')">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarCalibre(${calibre.id_calibre})">
                            🗑️ Eliminar
                        </button>
                    </td>
                `;
            });
        })
        .catch(error => {
            console.error("Error cargando tabla:", error);
            let tbody = document.querySelector("#tablaCalibres tbody");
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function enviarFormulario(accion) {
    let formData = new FormData(document.getElementById("formCalibre"));
    formData.append("accion", accion);

    fetch("../procesar_calibre.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        if (data.includes("éxito") || data.includes("correctamente")) {
            limpiarFormulario();
            cargarTabla();
        }
    })
    .catch(error => console.error("Error en el proceso:", error));
}

function cargarCalibre(id, codigo, nombre, id_especie) {
    document.getElementById("id_calibre").value = id;
    document.getElementById("codigo_calibre").value = codigo;
    document.getElementById("nombre_calibre").value = nombre;
    document.getElementById("especie").value = id_especie;
}

function eliminarCalibre(id) {
    if (confirm("¿Está seguro de eliminar este calibre?")) {
        let formData = new FormData();
        formData.append("accion", "eliminar");
        formData.append("id_calibre", id);

        fetch("../procesar_calibre.php", {
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
    document.getElementById("formCalibre").reset();
    document.getElementById("id_calibre").value = "";
}
