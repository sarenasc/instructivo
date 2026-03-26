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
            let select = document.getElementById("id_exportadora");
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
    fetch("../obtener_pallets.php")
        .then(response => response.json())
        .then(data => {
            let tbody = document.querySelector("#tablaPallet tbody");
            tbody.innerHTML = "";
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay registros</td></tr>';
                return;
            }
            
            data.forEach(item => {
                let row = tbody.insertRow();
                row.innerHTML = `
                    <td>${item.id_pallet}</td>
                    <td>${item.cod_pallet}</td>
                    <td>${item.descrip_pallet}</td>
                    <td>${item.nombre_exportadora || 'N/A'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="cargarPallet(${item.id_pallet}, '${item.cod_pallet}', '${item.descrip_pallet}', '${item.id_exportadora || ''}')">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarPallet(${item.id_pallet})">
                            🗑️ Eliminar
                        </button>
                    </td>
                `;
            });
        })
        .catch(error => {
            console.error("Error cargando tabla:", error);
            let tbody = document.querySelector("#tablaPallet tbody");
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function enviarFormulario(accion) {
    let formData = new FormData(document.getElementById("formPallet"));
    formData.append("accion", accion);

    fetch("../procesar_pallet.php", {
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

function cargarPallet(id, codigo, descripcion, id_exportadora) {
    document.getElementById("id_pallet").value = id;
    document.getElementById("cod_pallet").value = codigo;
    document.getElementById("descrip_pallet").value = descripcion;
    document.getElementById("id_exportadora").value = id_exportadora;
}

function eliminarPallet(id) {
    if (confirm("¿Está seguro de eliminar este pallet?")) {
        let formData = new FormData();
        formData.append("accion", "eliminar");
        formData.append("id_pallet", id);

        fetch("../procesar_pallet.php", {
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
    document.getElementById("formPallet").reset();
    document.getElementById("id_pallet").value = "";
}
